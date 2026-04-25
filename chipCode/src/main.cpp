#include <Arduino.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1331.h>
#include <Adafruit_AHTX0.h>
#include <SPI.h>
#include <Wire.h>
#include <time.h>

#include "secrets.h"
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include <PubSubClient.h>
#include <Preferences.h>

#include <NimBLEDevice.h>

// Definicja stanów (zgodnie z panelem WWW: 1–3 sterowanie, 4 = tylko pomiar MQTT)
enum state {
  HEATING = 1,
  COOLING = 2,
  AUTO = 3,
  SENSOR_ONLY = 4
};

Preferences preferences;
String deviceID;      
float targetTemp = 21.0; 
state devState = SENSOR_ONLY; // Domyślnie tryb automatyczny

// KONFIGURACJA IDENTYFIKACJI
const char* userId = "user_1"; // Tu ID później pobierane przez Bluetooth
#define SERVICE_UUID        "12345678-1234-1234-1234-1234567890ab"
#define DEVICE_ID_CHARACTERISTIC_UUID "abcd1234-5678-1234-5678-abcdef123456"

NimBLECharacteristic *deviceIdCharacteristic;

#define WIFI_CONFIG_CHARACTERISTIC_UUID "dcba4321-8765-4321-8765-fedcba654321"
NimBLECharacteristic *wifiConfigCharacteristic;

#define WIFI_STATUS_CHARACTERISTIC_UUID "eeee1111-2222-3333-4444-555566667777"
NimBLECharacteristic *wifiStatusCharacteristic;
bool wifiConfiguredSuccessfully = false;

// Dynamiczne tematy MQTT
char publishTopic[128];
char subscribeTopic[128];

// Definicja pinów (bez zmian):
#define SCLK_PIN 18
#define MOSI_PIN 23
#define CS_PIN   5
#define RST_PIN  4
#define DC_PIN   2
#define JOY_X_PIN 34
#define JOY_Y_PIN 35
#define JOY_SW_PIN 25
#define RELAY_HEATER 26 
#define RELAY_FAN    27 

// Kolory 
#define BLACK   0x0000
#define RED     0xF800
#define GREEN   0x07E0
#define WHITE   0xFFFF
#define YELLOW  0xFFE0
#define GRAY    0x7BEF 

Adafruit_AHTX0 aht;
Adafruit_SSD1331 display = Adafruit_SSD1331(CS_PIN, DC_PIN, MOSI_PIN, SCLK_PIN, RST_PIN);
WiFiClientSecure net = WiFiClientSecure();
PubSubClient client(net);

float tempC = 0, humi = 0;
int dotX = 48, dotY = 32;

bool shouldConnectWiFi = false;

// --- FUNKCJA OBSŁUGI KOMEND ---
void callback(char* topic, byte* payload, unsigned int length) {
    Serial.print("Odebrano komende na temacie: "); Serial.println(topic);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, payload, length);

    if (error) {
        Serial.print("Błąd parsowania JSON: "); Serial.println(error.c_str());
        return;
    }

    // 1. Obsługa zmiany stanu (HEATING/COOLING/AUTO/SENSOR_ONLY)
    if (doc.containsKey("state")) {
        int newState = doc["state"];
        if (newState >= (int)HEATING && newState <= (int)SENSOR_ONLY) {
            devState = (state)newState;
            Serial.print("Zmiana trybu na: "); Serial.println(newState);

            preferences.begin("thermio", false);
            preferences.putInt("devState", (int)devState);
            preferences.end();
        }
    }

    // 2. Obsługa nowej temperatury zadanej (tylko jeśli tryb AUTO)
    if (doc.containsKey("target")) {
        targetTemp = doc["target"];
        Serial.print("Nowa temp zadana (web): "); Serial.println(targetTemp);

        preferences.begin("thermio", false);
        preferences.putFloat("target", targetTemp);
        preferences.end();
    }
}

bool connectToAWS() {
    if (WiFi.status() == WL_CONNECTED && client.connected()) {
        return true;
    }

    preferences.begin("thermio", true);
    String savedSsid = preferences.getString("wifi_ssid", "");
    String savedPass = preferences.getString("wifi_pass", "");
    preferences.end();

    if (savedSsid.length() == 0) {
        Serial.println("Brak zapisanej sieci WiFi. Skonfiguruj przez BLE.");
        return false;
    }

    Serial.print("Laczenie z WiFi: ");
    Serial.println(savedSsid);

    WiFi.mode(WIFI_STA);
    WiFi.begin(savedSsid.c_str(), savedPass.c_str());

    unsigned long startAttempt = millis();

    while (WiFi.status() != WL_CONNECTED && millis() - startAttempt < 15000) {
        delay(500);
        Serial.print(".");
    }

    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("\nNie udalo sie polaczyc z WiFi.");

        preferences.begin("thermio", false);
        preferences.remove("wifi_ssid");
        preferences.remove("wifi_pass");
        preferences.end();

        if (wifiStatusCharacteristic) {
            wifiStatusCharacteristic->setValue("WIFI_ERROR");
        }

        return false;
    }

    Serial.println("\nWi-Fi polaczone.");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());

    if (wifiStatusCharacteristic) {
        wifiStatusCharacteristic->setValue("WIFI_OK");
    }

    configTime(3600, 3600, "pool.ntp.org");

    net.setCACert(AWS_CERT_CA);
    net.setCertificate(AWS_CERT_CRT);
    net.setPrivateKey(AWS_CERT_PRIVATE);

    client.setServer(AWS_IOT_ENDPOINT, 8883);
    client.setCallback(callback);

    Serial.println("Laczenie z AWS IoT...");

    unsigned long mqttStart = millis();

    while (!client.connected() && millis() - mqttStart < 15000) {
        if (client.connect(THING_NAME)) {
            Serial.println("AWS IoT Polaczone!");
            client.subscribe(subscribeTopic);

            if (wifiStatusCharacteristic) {
                wifiStatusCharacteristic->setValue("AWS_OK");
            }

            return true;
        }

        delay(500);
        Serial.print("M");
    }

    Serial.println("\nNie udalo sie polaczyc z AWS IoT.");

    if (wifiStatusCharacteristic) {
        wifiStatusCharacteristic->setValue("AWS_ERROR");
    }

    return false;
}

void publishMessage() {
    if (!client.connected()) {
        return;
    }
    StaticJsonDocument<256> doc;
    doc["id"] = deviceID;
    doc["temp"] = tempC;
    doc["humi"] = humi;
    doc["target"] = targetTemp;
    doc["state"] = (int)devState; // Wysyłamy aktualny tryb pracy
    doc["heater"] = (digitalRead(RELAY_HEATER) == LOW);
    doc["fan"] = (digitalRead(RELAY_FAN) == HIGH);

    char jsonBuffer[512];
    serializeJson(doc, jsonBuffer);
    client.publish(publishTopic, jsonBuffer);
}

class WifiConfigCallback : public NimBLECharacteristicCallbacks {
    void onWrite(NimBLECharacteristic *pCharacteristic) override {
        std::string value = pCharacteristic->getValue();

        if (value.empty()) {
            Serial.println("Puste dane WiFi");
            return;
        }

        Serial.print("Odebrano WiFi JSON: ");
        Serial.println(value.c_str());

        StaticJsonDocument<256> doc;
        DeserializationError error = deserializeJson(doc, value);

        if (error) {
            Serial.print("Błąd JSON WiFi: ");
            Serial.println(error.c_str());
            return;
        }

        const char* ssid = doc["ssid"];
        const char* password = doc["password"];

        if (!ssid || !password) {
            Serial.println("Brak ssid albo password");
            return;
        }

        preferences.begin("thermio", false);
        preferences.putString("wifi_ssid", ssid);
        preferences.putString("wifi_pass", password);
        preferences.end();

        Serial.println("sZapisano dane WiFi");

        if (wifiStatusCharacteristic) {
            wifiStatusCharacteristic->setValue("CONNECTING");
        }

        shouldConnectWiFi = true;
    }
};

void setupBLE() {
    NimBLEDevice::init("Thermio");

    NimBLEServer *pServer = NimBLEDevice::createServer();
    NimBLEService *pService = pServer->createService(SERVICE_UUID);

    deviceIdCharacteristic = pService->createCharacteristic(
        DEVICE_ID_CHARACTERISTIC_UUID,
        NIMBLE_PROPERTY::READ
    );

    deviceIdCharacteristic->setValue((uint8_t*)deviceID.c_str(), deviceID.length());

    wifiConfigCharacteristic = pService->createCharacteristic(
        WIFI_CONFIG_CHARACTERISTIC_UUID,
        NIMBLE_PROPERTY::WRITE
    );

    wifiConfigCharacteristic->setCallbacks(new WifiConfigCallback());
    wifiStatusCharacteristic = pService->createCharacteristic(
        WIFI_STATUS_CHARACTERISTIC_UUID,
        NIMBLE_PROPERTY::READ
    );

    wifiStatusCharacteristic->setValue("WAITING");

    pService->start();

    NimBLEAdvertising *pAdvertising = NimBLEDevice::getAdvertising();
    pAdvertising->addServiceUUID(SERVICE_UUID);
    pAdvertising->start();

    Serial.println("BLE gotowe");
}

void setup() {
    Serial.begin(115200);

    // ID urządzenia na podstawie MAC
    WiFi.mode(WIFI_STA);
    deviceID = WiFi.macAddress();
    deviceID.replace(":", "");

    // BUDOWANIE TEMATÓW Z userId
    sprintf(publishTopic, "thermio/%s/devices/%s/status", userId, deviceID.c_str());
    sprintf(subscribeTopic, "thermio/%s/devices/%s/cmd", userId, deviceID.c_str());

    Serial.print("Urzadzenie identyfikowane przez ID: "); Serial.println(deviceID);

    // Odczyt ustawień z pamięci
    preferences.begin("thermio", false);
    targetTemp = preferences.getFloat("target", 21.0);
    int storedState = preferences.getInt("devState", (int)AUTO);
    if (storedState >= (int)HEATING && storedState <= (int)SENSOR_ONLY) {
        devState = (state)storedState;
    } else {
        devState = AUTO;
    }
    preferences.end();

    pinMode(RELAY_HEATER, OUTPUT);
    pinMode(RELAY_FAN, OUTPUT);
    digitalWrite(RELAY_HEATER, HIGH); 
    digitalWrite(RELAY_FAN, LOW);

    pinMode(JOY_SW_PIN, INPUT_PULLUP);
    display.begin();
    display.fillScreen(BLACK);
    aht.begin();
    setupBLE();
    connectToAWS();
}

void loop() {
    if (shouldConnectWiFi) {
        shouldConnectWiFi = false;

        delay(1000);
        WiFi.disconnect();
        delay(500);

        connectToAWS();
    }

    if (WiFi.status() == WL_CONNECTED && !client.connected()) {
        connectToAWS();
    }

    if (!client.connected()) {
        return;
    }
    client.loop();

    // Odczyt czujnika
    static unsigned long lastSensor = 0;
    if (millis() - lastSensor > 2000) {
        sensors_event_t humidity, temp;
        aht.getEvent(&humidity, &temp);
        tempC = temp.temperature;
        humi = humidity.relative_humidity;
        lastSensor = millis();
    }

    // --- LOGIKA STEROWANIA PRZEKAŹNIKAMI ---
    if (devState == HEATING) {
        digitalWrite(RELAY_HEATER, LOW);  // Wymuś grzanie
        digitalWrite(RELAY_FAN, LOW);     // Wiatrak OFF
    }
    else if (devState == COOLING) {
        digitalWrite(RELAY_HEATER, HIGH); // Grzałka OFF
        digitalWrite(RELAY_FAN, HIGH);    // Wymuś wiatrak
    }
    else if (devState == SENSOR_ONLY) {
        digitalWrite(RELAY_HEATER, HIGH); // Brak sterowania — stany bezczynne
        digitalWrite(RELAY_FAN, LOW);
    }
    else { // AUTO (zależny od temperatury)
        if (tempC < targetTemp - 0.5) {
            digitalWrite(RELAY_HEATER, LOW);
            digitalWrite(RELAY_FAN, LOW);
        } else if (tempC > targetTemp + 0.5) {
            digitalWrite(RELAY_HEATER, HIGH);
            digitalWrite(RELAY_FAN, HIGH);
        } else {
            digitalWrite(RELAY_HEATER, HIGH);
            digitalWrite(RELAY_FAN, LOW);
        }
    }

    // Obsługa joysticka (tylko w trybie AUTO)
    if (devState == AUTO) {
        int rawY = analogRead(JOY_Y_PIN);
        static unsigned long lastJoy = 0;
        if (millis() - lastJoy > 200) {
            if (rawY < 500) targetTemp += 0.5;
            if (rawY > 3500) targetTemp -= 0.5;
            if (rawY < 500 || rawY > 3500) {
                lastJoy = millis();
                preferences.begin("thermio", false);
                preferences.putFloat("target", targetTemp);
                preferences.end();
            }
        }
    }

    // Publikacja danych co 5s
    static unsigned long lastPub = 0;
    if (millis() - lastPub > 5000) {
        publishMessage();
        lastPub = millis();
    }

    // Wyświetlacz
    display.setCursor(0, 0);
    display.setTextColor(WHITE, BLACK);
    display.print("T:"); display.print(tempC, 1);
    display.print(" H:"); display.print(humi, 0); display.print("%");

    display.setCursor(0, 12);
    display.setTextColor(YELLOW, BLACK);
    if (devState == AUTO) {
        display.print("MODE: AUTO ("); display.print(targetTemp, 1); display.print(")");
    } else if (devState == HEATING) {
        display.print("MODE: HEATING   ");
    } else if (devState == COOLING) {
        display.print("MODE: COOLING   ");
    } else if (devState == SENSOR_ONLY) {
        display.print("MODE: SENSOR    ");
    } else {
        display.print("MODE: ?         ");
    }

    display.setCursor(0, 25);
    if (digitalRead(RELAY_HEATER) == LOW) {
        display.setTextColor(RED, BLACK); display.print("SYS: HEATING");
    } else if (digitalRead(RELAY_FAN) == HIGH) {
        display.setTextColor(GREEN, BLACK); display.print("SYS: COOLING");
    } else {
        display.setTextColor(GRAY, BLACK); display.print("SYS: IDLE   ");
    }

    delay(20);
}