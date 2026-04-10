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

// Definicja stanów
enum state {
  HEATING = 1,
  COOLING = 2,
  AUTO = 3
};

Preferences preferences;
String deviceID;      
float targetTemp = 21.0; 
state devState = AUTO; // Domyślnie tryb automatyczny

// KONFIGURACJA IDENTYFIKACJI
const char* userId = "user_1"; // Tu wpisz swoje ID (później pobierane np. przez Bluetooth)

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

const char* WIFI_SSID = "TWOJE_SSID";
const char* WIFI_PASS = "TWOJE_HASLO";

Adafruit_AHTX0 aht;
Adafruit_SSD1331 display = Adafruit_SSD1331(CS_PIN, DC_PIN, MOSI_PIN, SCLK_PIN, RST_PIN);
WiFiClientSecure net = WiFiClientSecure();
PubSubClient client(net);

float tempC = 0, humi = 0;
int dotX = 48, dotY = 32;

// --- FUNKCJA OBSŁUGI KOMEND ---
void callback(char* topic, byte* payload, unsigned int length) {
    Serial.print("Odebrano komende na temacie: "); Serial.println(topic);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, payload, length);

    if (error) {
        Serial.print("Błąd parsowania JSON: "); Serial.println(error.c_str());
        return;
    }

    // 1. Obsługa zmiany stanu (HEATING/COOLING/AUTO)
    if (doc.containsKey("state")) {
        int newState = doc["state"];
        devState = (state)newState;
        Serial.print("Zmiana trybu na: "); Serial.println(newState);
        
        // Zapisz tryb w pamięci, aby po restarcie ESP pamiętało stan
        preferences.begin("thermio", false);
        preferences.putInt("devState", (int)devState);
        preferences.end();
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

void connectToAWS() {
    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASS);
    
    while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
    Serial.println("\nWi-Fi polaczone.");

    configTime(3600, 3600, "pool.ntp.org"); // Czas letni/zimowy

    net.setCACert(AWS_CERT_CA);
    net.setCertificate(AWS_CERT_CRT);
    net.setPrivateKey(AWS_CERT_PRIVATE);

    client.setServer(AWS_IOT_ENDPOINT, 8883);
    client.setCallback(callback);

    while (!client.connect(THING_NAME)) { delay(500); Serial.print("M"); }

    client.subscribe(subscribeTopic);
    Serial.println("AWS IoT Polaczone!");
}

void publishMessage() {
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

void setup() {
    Serial.begin(115200);

    // ID urządzenia na podstawie MAC
    deviceID = WiFi.macAddress();
    deviceID.replace(":", "");

    // BUDOWANIE TEMATÓW Z userId
    sprintf(publishTopic, "thermio/%s/devices/%s/status", userId, deviceID.c_str());
    sprintf(subscribeTopic, "thermio/%s/devices/%s/cmd", userId, deviceID.c_str());

    Serial.print("Urzadzenie identyfikowane przez ID: "); Serial.println(deviceID);

    // Odczyt ustawień z pamięci
    preferences.begin("thermio", false);
    targetTemp = preferences.getFloat("target", 21.0);
    devState = (state)preferences.getInt("devState", (int)AUTO);
    preferences.end();

    pinMode(RELAY_HEATER, OUTPUT);
    pinMode(RELAY_FAN, OUTPUT);
    digitalWrite(RELAY_HEATER, HIGH); 
    digitalWrite(RELAY_FAN, LOW);

    pinMode(JOY_SW_PIN, INPUT_PULLUP);
    display.begin();
    display.fillScreen(BLACK);
    aht.begin();

    connectToAWS();
}

void loop() {
    if (!client.connected()) connectToAWS();
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
    else { // Tryb AUTO (Zależny od temperatury)
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

    // Obsługa joysticka (tylko zmiana targetu w trybie AUTO dla wygody)
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
    } else {
        display.print("MODE: COOLING   ");
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