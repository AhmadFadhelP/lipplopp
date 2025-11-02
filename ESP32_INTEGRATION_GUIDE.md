# ESP32 Integration Guide

Complete guide for the hardware team to integrate ESP32 with the backend system via MQTT.

## Overview

This document provides all necessary information for the ESP32 team to connect their devices to the IoT monitoring system.

---

## MQTT Broker Details

### Connection Information

| Parameter | Value | Notes |
|-----------|-------|-------|
| **MQTT Broker Host** | `your-server-ip` or `localhost` | Replace with actual server IP |
| **MQTT Port** | `1883` | Standard MQTT port |
| **WebSocket Port** | `9001` | If using WebSockets |
| **Authentication** | None (currently) | Can be configured if needed |
| **QoS Level** | `0` or `1` | Recommended: 1 for reliability |
| **Keep Alive** | `60` seconds | Adjust as needed |

---

## MQTT Topics

### Sensor Data Topics (Publish from ESP32)

The ESP32 should **publish** sensor readings to these topics:

| Topic | Data Type | Unit | Example | Description |
|-------|-----------|------|---------|-------------|
| `iot/water/level` | Float | % | `75.5` | Water level percentage (0-100) |
| `iot/water/turbidity` | Float | NTU | `25.3` | Water turbidity |
| `iot/battery/voltage` | Float | V | `12.4` | Battery voltage |
| `iot/battery/current` | Float | A | `2.8` | Battery current |
| `iot/solar/voltage` | Float | V | `13.2` | Solar panel voltage |
| `iot/solar/current` | Float | A | `3.5` | Solar panel current |

### Control Topics (Subscribe from ESP32)

The ESP32 can **subscribe** to these topics for commands:

| Topic | Purpose | Example Payload |
|-------|---------|----------------|
| `iot/control/feed` | Trigger fish feeding | `{"amount": 10}` |
| `iot/control/settings` | Update device settings | `{"interval": 300}` |
| `iot/status/request` | Request status update | `1` |

---

## Data Format

### Publishing Sensor Data

**Format**: Plain text numbers (simple) or JSON (advanced)

#### Simple Format (Recommended for ESP32):
```cpp
// Publish individual values
mqtt.publish("iot/water/level", "75.5");
mqtt.publish("iot/water/turbidity", "25.3");
mqtt.publish("iot/battery/voltage", "12.4");
```

#### JSON Format (Optional):
```json
{
  "sensor": "water_level",
  "value": 75.5,
  "unit": "%",
  "timestamp": 1699012345
}
```

---

## ESP32 Sample Code

### Arduino/PlatformIO Example

```cpp
#include <WiFi.h>
#include <PubSubClient.h>

// WiFi credentials
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// MQTT Broker
const char* mqtt_server = "YOUR_SERVER_IP";  // e.g., "192.168.1.100"
const int mqtt_port = 1883;
const char* mqtt_client_id = "ESP32_Sensor_01";

// MQTT Topics
const char* topic_water_level = "iot/water/level";
const char* topic_turbidity = "iot/water/turbidity";
const char* topic_battery_voltage = "iot/battery/voltage";
const char* topic_battery_current = "iot/battery/current";
const char* topic_solar_voltage = "iot/solar/voltage";
const char* topic_solar_current = "iot/solar/current";

WiFiClient espClient;
PubSubClient mqtt(espClient);

// Sensor reading interval
unsigned long lastSend = 0;
const long interval = 5000; // 5 seconds

void setup() {
  Serial.begin(115200);

  // Connect to WiFi
  connectWiFi();

  // Setup MQTT
  mqtt.setServer(mqtt_server, mqtt_port);
  mqtt.setCallback(mqttCallback);
}

void loop() {
  // Maintain MQTT connection
  if (!mqtt.connected()) {
    reconnectMQTT();
  }
  mqtt.loop();

  // Send sensor data every 5 seconds
  unsigned long now = millis();
  if (now - lastSend >= interval) {
    lastSend = now;
    sendSensorData();
  }
}

void connectWiFi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void reconnectMQTT() {
  while (!mqtt.connected()) {
    Serial.print("Connecting to MQTT...");

    if (mqtt.connect(mqtt_client_id)) {
      Serial.println("connected");

      // Subscribe to control topics
      mqtt.subscribe("iot/control/#");

    } else {
      Serial.print("failed, rc=");
      Serial.print(mqtt.state());
      Serial.println(" retrying in 5 seconds");
      delay(5000);
    }
  }
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived [");
  Serial.print(topic);
  Serial.print("]: ");

  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
  }
  Serial.println();

  // Handle control commands here
}

void sendSensorData() {
  // Read sensors (replace with actual sensor readings)
  float waterLevel = readWaterLevel();
  float turbidity = readTurbidity();
  float batteryVoltage = readBatteryVoltage();
  float batteryCurrent = readBatteryCurrent();
  float solarVoltage = readSolarVoltage();
  float solarCurrent = readSolarCurrent();

  // Convert to strings
  char waterLevelStr[10];
  char turbidityStr[10];
  char batteryVoltageStr[10];
  char batteryCurrentStr[10];
  char solarVoltageStr[10];
  char solarCurrentStr[10];

  dtostrf(waterLevel, 4, 2, waterLevelStr);
  dtostrf(turbidity, 4, 2, turbidityStr);
  dtostrf(batteryVoltage, 4, 2, batteryVoltageStr);
  dtostrf(batteryCurrent, 4, 2, batteryCurrentStr);
  dtostrf(solarVoltage, 4, 2, solarVoltageStr);
  dtostrf(solarCurrent, 4, 2, solarCurrentStr);

  // Publish to MQTT
  mqtt.publish(topic_water_level, waterLevelStr, true);
  mqtt.publish(topic_turbidity, turbidityStr, true);
  mqtt.publish(topic_battery_voltage, batteryVoltageStr, true);
  mqtt.publish(topic_battery_current, batteryCurrentStr, true);
  mqtt.publish(topic_solar_voltage, solarVoltageStr, true);
  mqtt.publish(topic_solar_current, solarCurrentStr, true);

  Serial.println("Sensor data published");
}

// Placeholder functions - replace with actual sensor readings
float readWaterLevel() {
  // TODO: Read from water level sensor
  return 75.5;
}

float readTurbidity() {
  // TODO: Read from turbidity sensor
  return 25.3;
}

float readBatteryVoltage() {
  // TODO: Read battery voltage
  return 12.4;
}

float readBatteryCurrent() {
  // TODO: Read battery current
  return 2.8;
}

float readSolarVoltage() {
  // TODO: Read solar panel voltage
  return 13.2;
}

float readSolarCurrent() {
  // TODO: Read solar panel current
  return 3.5;
}
```

---

## Testing the Connection

### 1. Test MQTT Broker Availability

Before coding ESP32, test the broker is accessible:

```bash
# Run connection test
bash tests/mqtt_connection_test.sh
```

### 2. Monitor MQTT Topics

Watch what the backend receives in real-time:

```bash
# Monitor all topics
bash tests/mqtt_monitor.sh
```

### 3. Simulate ESP32 Data

Test with simulated data before hardware is ready:

```bash
# Simulate continuous ESP32 data
bash tests/simulate_esp32.sh
```

### 4. Manual Testing

```bash
# Install mosquitto-clients
sudo apt-get install mosquitto-clients

# Test publish
mosquitto_pub -h YOUR_SERVER_IP -p 1883 -t "iot/water/level" -m "75.5"

# Test subscribe
mosquitto_sub -h YOUR_SERVER_IP -p 1883 -t "iot/#" -v
```

---

## Sensor Value Ranges

### Valid Ranges (Backend Validation)

| Sensor | Min | Max | Unit |
|--------|-----|-----|------|
| Water Level | 0 | 100 | % |
| Turbidity | 0 | 999 | NTU |
| Battery Voltage | 0 | 20 | V |
| Battery Current | -50 | 50 | A |
| Solar Voltage | 0 | 20 | V |
| Solar Current | -50 | 50 | A |

**Note**: Values outside these ranges will be rejected by the API.

---

## Alert Thresholds

The backend automatically generates alerts for:

| Condition | Threshold | Alert Level |
|-----------|-----------|-------------|
| Low Water Level | < 30% | Warning |
| High Turbidity | > 50 NTU | Warning |
| Low Battery | < 11.5V | Warning |
| Critical Battery | < 10.8V | Critical |

---

## Recommended Publishing Frequency

| Sensor Type | Recommended Interval | Notes |
|-------------|---------------------|-------|
| Water Level | 10-30 seconds | Frequent updates |
| Turbidity | 10-30 seconds | Frequent updates |
| Battery Voltage | 30-60 seconds | Slower changes |
| Battery Current | 30-60 seconds | Slower changes |
| Solar Voltage | 30-60 seconds | Slower changes |
| Solar Current | 30-60 seconds | Slower changes |

**Recommended**: 10-15 seconds for all sensors to keep dashboard live.

---

## Network Requirements

### Firewall Rules

Ensure these ports are open:

- **1883** (MQTT)
- **9001** (MQTT WebSockets, optional)
- **8080** (Backend API, optional)

### Bandwidth Estimation

- Per message: ~50-100 bytes
- 6 sensors @ 10 second intervals = 36 messages/minute
- Bandwidth: ~3.6 KB/minute (~5 MB/day)

Very low bandwidth requirements!

---

## Troubleshooting

### ESP32 Cannot Connect to MQTT

**Check**:
1. Server IP is correct
2. MQTT broker is running: `docker-compose ps mosquitto`
3. Port 1883 is accessible from ESP32's network
4. No firewall blocking the connection

**Test from ESP32 network**:
```bash
telnet YOUR_SERVER_IP 1883
```

### Messages Not Appearing in Backend

**Check**:
1. Topic names are exactly correct (case-sensitive)
2. Messages are being published (check MQTT monitor)
3. Backend API is running
4. Database is accepting data

**Monitor**:
```bash
# Watch MQTT traffic
bash tests/mqtt_monitor.sh

# Check backend logs
docker-compose logs -f app
```

### Connection Drops Frequently

**Solutions**:
1. Increase keep-alive interval
2. Use QoS 1 instead of 0
3. Implement reconnection logic (see sample code)
4. Check WiFi signal strength

---

## Security Recommendations

### For Production:

1. **Enable MQTT Authentication**:
   - Configure username/password in mosquitto
   - Update ESP32 code with credentials

2. **Use TLS/SSL**:
   - Configure mosquitto with certificates
   - Use port 8883 instead of 1883

3. **Network Security**:
   - Use VPN for remote access
   - Firewall rules to restrict access
   - Don't expose MQTT broker to internet directly

---

## API Alternative

If MQTT is not suitable, ESP32 can also use the REST API directly:

### HTTP POST Example

```cpp
#include <HTTPClient.h>

void sendViaAPI() {
  HTTPClient http;

  http.begin("http://YOUR_SERVER_IP:8080/backend/api/sensors.php");
  http.addHeader("Content-Type", "application/json");

  String payload = "{";
  payload += "\"water_level\":" + String(waterLevel) + ",";
  payload += "\"turbidity\":" + String(turbidity) + ",";
  payload += "\"battery_voltage\":" + String(batteryVoltage) + ",";
  payload += "\"battery_current\":" + String(batteryCurrent) + ",";
  payload += "\"solar_voltage\":" + String(solarVoltage) + ",";
  payload += "\"solar_current\":" + String(solarCurrent);
  payload += "}";

  int httpCode = http.POST(payload);

  if (httpCode == 201) {
    Serial.println("Data sent successfully");
  } else {
    Serial.printf("Error: %d\n", httpCode);
  }

  http.end();
}
```

---

## Support & Contact

### Quick Reference

- **MQTT Broker**: `YOUR_SERVER_IP:1883`
- **Backend API**: `http://YOUR_SERVER_IP:8080/backend/api/`
- **System Status**: `http://YOUR_SERVER_IP:8080/backend/api/status.php`

### Testing Tools

```bash
# Test MQTT connection
bash tests/mqtt_connection_test.sh

# Monitor topics
bash tests/mqtt_monitor.sh

# Simulate ESP32
bash tests/simulate_esp32.sh
```

### For Questions

Contact the backend team with:
- ESP32 device ID
- Network configuration
- Error messages
- MQTT client logs

---

## Checklist for ESP32 Team

Before integration:

- [ ] Server IP address obtained
- [ ] MQTT broker tested and accessible
- [ ] Topics documented and understood
- [ ] Sample code reviewed
- [ ] Sensor value ranges noted
- [ ] Publishing frequency decided
- [ ] Error handling implemented
- [ ] Reconnection logic added
- [ ] Testing completed with simulation
- [ ] WiFi credentials configured

After integration:

- [ ] Verify data appears in backend
- [ ] Check dashboard shows live data
- [ ] Test connection stability (24h test)
- [ ] Validate alert generation
- [ ] Document any issues

---

## Ready to Connect!

The backend system is ready to receive data from ESP32. Use the test scripts to verify connectivity before hardware implementation.

**Good luck with the integration!** 🚀
