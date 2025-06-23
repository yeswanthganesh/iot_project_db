#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

const char* ssid = "project";
const char* password = "project123";
const char* serverURL = "http://192.168.144.180/sensor_project/save_data.php";  // Change IP if needed

int counter = 1;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
  dht.begin();
}

void loop() {
  float temp = dht.readTemperature();
  float hum = dht.readHumidity();
  float heatIndex = dht.computeHeatIndex(temp, hum, false);

  if (isnan(temp) || isnan(hum)) {
    Serial.println("DHT read failed");
    delay(2000);
    return;
  }

  String postData = "{";
  postData += "\"temperature\":" + String(temp) + ",";
  postData += "\"humidity\":" + String(hum) + ",";
  postData += "\"heatIndex\":" + String(heatIndex) + ",";
  postData += "\"reading\":" + String(counter);
  postData += "}";

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/json");
    int code = http.POST(postData);
    if (code > 0) {
      Serial.println("Success: " + http.getString());
    } else {
      Serial.println("Failed: " + String(code));
    }
    http.end();
  }

  counter++;
  delay(5000);
}
