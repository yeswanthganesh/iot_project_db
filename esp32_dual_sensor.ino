#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

// ESP1 Sensor (DHT22 on GPIO 4)
#define DHTPIN1 4
#define DHTTYPE DHT22
DHT dht1(DHTPIN1, DHTTYPE);

// ESP2 Sensor (DHT22 on GPIO 5)
#define DHTPIN2 5
DHT dht2(DHTPIN2, DHTTYPE);

const char* ssid = "project";
const char* password = "project123";

const char* serverURL1 = "http://192.168.144.180/project1/save_data.php"; // ESP1
const char* serverURL2 = "http://192.168.144.180/project2/save_data.php"; // ESP2

int counter = 1;

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n✅ WiFi Connected");
  dht1.begin();
  dht2.begin();
}

void loop() {
  // Read sensor 1
  float temp1 = dht1.readTemperature();
  float hum1 = dht1.readHumidity();
  float heatIndex1 = dht1.computeHeatIndex(temp1, hum1, false);

  // Read sensor 2
  float temp2 = dht2.readTemperature();
  float hum2 = dht2.readHumidity();
  float heatIndex2 = dht2.computeHeatIndex(temp2, hum2, false);

  if (isnan(temp1) || isnan(hum1) || isnan(temp2) || isnan(hum2)) {
    Serial.println("❌ Sensor Read Failed");
    delay(2000);
    return;
  }

  Serial.println("📟 Sensor 1 --> Temp: " + String(temp1) + "°C, Humidity: " + String(hum1) + "%, Heat: " + String(heatIndex1) + "°C");
  Serial.println("📟 Sensor 2 --> Temp: " + String(temp2) + "°C, Humidity: " + String(hum2) + "%, Heat: " + String(heatIndex2) + "°C");

  // Send sensor 1 data
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http1;
    http1.begin(serverURL1);
    http1.addHeader("Content-Type", "application/json");

    String data1 = "{";
    data1 += "\"temperature\":" + String(temp1) + ",";
    data1 += "\"humidity\":" + String(hum1) + ",";
    data1 += "\"heatIndex\":" + String(heatIndex1) + ",";
    data1 += "\"reading\":" + String(counter);
    data1 += "}";

    int code1 = http1.POST(data1);
    Serial.println("ESP1 POST Code: " + String(code1));
    http1.end();
  }

  // Send sensor 2 data
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http2;
    http2.begin(serverURL2);
    http2.addHeader("Content-Type", "application/json");

    String data2 = "{";
    data2 += "\"temperature\":" + String(temp2) + ",";
    data2 += "\"humidity\":" + String(hum2) + ",";
    data2 += "\"heatIndex\":" + String(heatIndex2) + ",";
    data2 += "\"reading\":" + String(counter);
    data2 += "}";

    int code2 = http2.POST(data2);
    Serial.println("ESP2 POST Code: " + String(code2));
    http2.end();
  }

  counter++;
  delay(5000);
}
