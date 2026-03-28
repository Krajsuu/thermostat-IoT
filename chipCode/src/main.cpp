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

// Definicja pinów:
#define SCLK_PIN 18
#define MOSI_PIN 23
#define CS_PIN   5
#define RST_PIN  4
#define DC_PIN   2

#define JOY_X_PIN 34
#define JOY_Y_PIN 35
#define JOY_SW_PIN 25

#define RELAY_HEATER 26 // IN1 na module
#define RELAY_FAN    27 // IN2 na module

// Kolory 
#define BLACK   0x0000
#define RED     0xF800
#define GREEN   0x07E0
#define WHITE   0xFFFF
#define YELLOW  0xFFE0
#define GRAY    0x7BEF

#define AWS_TEST_PUBLISH_TOPIC "mqtt/test/thermio"
#define AWS_TEST_SUBSCRIBE_TOPIC "mqtt/test/thermio" 

const char* WIFI_SSID = "";
const char* WIFI_PASS = "";

Adafruit_AHTX0 aht;
Adafruit_SSD1331 display = Adafruit_SSD1331(CS_PIN, DC_PIN, MOSI_PIN, SCLK_PIN, RST_PIN);
WiFiClientSecure net = WiFiClientSecure();
PubSubClient client(net);


int dotX = 48;
int dotY = 32;
float tempC = 0, humi = 0;

void connectToAWS(){
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);

  Serial.print("Laczenie z Wi-Fi...");
  while(WiFi.status() != WL_CONNECTED){
    delay(500);
    Serial.print(".");
  }
  Serial.println("");

  Serial.println("Synchronizacja czasu...");
  configTime(0,0, "pool.ntp.org", "time.nist.gov");
  while(time(nullptr) < 8 * 3600 * 2){
    delay(500);
    Serial.print("t");
  }
  Serial.println("...Czas synchronizowany.");

  net.setCACert(AWS_CERT_CA);
  net.setCertificate(AWS_CERT_CRT);
  net.setPrivateKey(AWS_CERT_PRIVATE);

  client.setServer(AWS_IOT_ENDPOINT, 8883);
  Serial.println("Laczenie z AWS IoT...");
  while(!client.connect(THING_NAME)){
    delay(500);
    Serial.print("Blad mqtt -> ");
    Serial.println(client.state());
    Serial.print(".");
  }

  if(!client.connected()){
    Serial.println("AWS IoT Timeout!");
    return;
  }

  Serial.println("Polaczono z AWS IoT!");
}

void publishMessage(){
    StaticJsonDocument<200> doc;
    doc["temp"] = tempC;
    doc["humi"] = humi;

    char jsonBuffer[512];
    serializeJson(doc, jsonBuffer);

    client.publish(AWS_TEST_PUBLISH_TOPIC, jsonBuffer);
    Serial.println("Przeslano dane na AWS!");
}

void setup() {
  Serial.begin(115200);
  
  pinMode(RELAY_HEATER, OUTPUT);
  pinMode(RELAY_FAN, OUTPUT);
  
  // POPRAWKA: Teraz LOW to OFF na starcie
  digitalWrite(RELAY_HEATER, HIGH); 
  digitalWrite(RELAY_FAN, LOW);

  pinMode(JOY_SW_PIN, INPUT_PULLUP);
  display.begin();
  display.fillScreen(BLACK);
  if (!aht.begin()) Serial.println("Brak AHT30!");

  connectToAWS();
}

void loop() {
  // 1. Odczyt Joysticka

  if(!client.connected()){
    connectToAWS();
  }
  client.loop();

  int rawX = analogRead(JOY_X_PIN);
  int rawY = analogRead(JOY_Y_PIN);
  bool btnPressed = (digitalRead(JOY_SW_PIN) == LOW);

  dotX = map(rawX, 0, 4095, 0, 95);
  dotY = map(rawY, 0, 4095, 0, 63);

  // 2. Logika Sterowania Przekaźnikami
  // Jeśli joystick w górę (małe wartości Y) -> GRZEJ
  if (rawY < 500 && !btnPressed) {
    digitalWrite(RELAY_HEATER, LOW); // WŁĄCZ grzałkę
    digitalWrite(RELAY_FAN, LOW);    // WYŁĄCZ wiatrak
  } 
  else if (rawY > 3500 && !btnPressed) {
    digitalWrite(RELAY_HEATER, HIGH); // WYŁĄCZ grzałkę
    digitalWrite(RELAY_FAN, HIGH);   // WŁĄCZ wiatrak
  }
  else {
    digitalWrite(RELAY_HEATER, HIGH); // WYŁĄCZ wszystko
    digitalWrite(RELAY_FAN, LOW);
  }

  // 3. Odczyt Czujnika (co 2 sekundy)
  static unsigned long lastSensorRead = 0;
  static unsigned long lastPublish = 0;

  if (millis() - lastSensorRead > 2000) {
    sensors_event_t humidity, temp;
    aht.getEvent(&humidity, &temp);
    tempC = temp.temperature;
    humi = humidity.relative_humidity;
    lastSensorRead = millis();
  }

  if (millis() - lastPublish > 5000) {
    publishMessage();
    lastPublish = millis();
  }

  // 4. Rysowanie na ekranie
  display.fillScreen(BLACK);

  // Dane pogodowe
  display.setCursor(0, 0);
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.print("T:"); display.print(tempC, 1);
  display.print(" H:"); display.print(humi, 0); display.print("%");

  // Status urządzeń
  display.setCursor(0, 12);
  if (digitalRead(RELAY_HEATER) != HIGH) { // Sprawdzamy czy HIGH
    display.setTextColor(RED);
    display.print("HEAT: ON");
  } else {
    display.setTextColor(GRAY);
    display.print("HEAT: OFF");
  }

  display.setCursor(0, 22);
  if (digitalRead(RELAY_FAN) == HIGH) { // Sprawdzamy czy HIGH
    display.setTextColor(GREEN);
    display.print("FAN:  ON");
  } else {
    display.setTextColor(GRAY);
    display.print("FAN:  OFF");
  }

  // Celownik joysticka
  uint16_t color = btnPressed ? RED : GREEN;
  display.fillCircle(dotX, dotY, 3, color); 

  // Surowe wartości X/Y
  display.setCursor(0, 55);
  display.setTextColor(YELLOW);
  display.print("X:"); display.print(rawX);
  display.print(" Y:"); display.print(rawY);

  delay(30); 
}