# MQTT Testing Tools

Quick reference for testing MQTT connectivity before ESP32 integration.

## Available Test Scripts

### 1. Connection Test
**File**: `mqtt_connection_test.sh`

**Purpose**: Verify MQTT broker is ready for ESP32

**Usage**:
```bash
bash tests/mqtt_connection_test.sh
```

**What it tests**:
- ✓ Mosquitto container status
- ✓ Port 1883 accessibility
- ✓ MQTT publish capability
- ✓ MQTT subscribe capability
- ✓ All IoT topics
- ✓ Backend API connectivity

---

### 2. ESP32 Simulator
**File**: `simulate_esp32.sh`

**Purpose**: Continuously simulate ESP32 sending sensor data

**Usage**:
```bash
bash tests/simulate_esp32.sh
```

**What it does**:
- Generates realistic sensor values
- Publishes to all MQTT topics every 5 seconds
- Also sends data to REST API
- Displays readings on screen

**Output Example**:
```
Reading #1 at 14:30:15
  Water Level:      75.23 %
  Turbidity:        28.45 NTU
  Battery Voltage:  12.34 V
  Battery Current:  2.87 A
  Solar Voltage:    13.56 V
  Solar Current:    3.21 A
  ✓ Data published to MQTT and API
```

---

### 3. MQTT Monitor
**File**: `mqtt_monitor.sh`

**Purpose**: Watch all MQTT topics in real-time

**Usage**:
```bash
bash tests/mqtt_monitor.sh
```

**What it shows**:
- All messages published to `iot/#` topics
- Topic name and payload
- Timestamp of each message

**Output Example**:
```
2025-11-02 14:30:15 iot/water/level 75.5
2025-11-02 14:30:15 iot/water/turbidity 25.3
2025-11-02 14:30:15 iot/battery/voltage 12.4
```

---

## Quick Start

### Step 1: Start Docker Services
```bash
docker-compose up -d
```

### Step 2: Run Connection Test
```bash
bash tests/mqtt_connection_test.sh
```

Expected output: All tests pass ✓

### Step 3: Monitor MQTT Topics
```bash
# In one terminal
bash tests/mqtt_monitor.sh
```

### Step 4: Simulate ESP32 Data
```bash
# In another terminal
bash tests/simulate_esp32.sh
```

You should see data flowing in the monitor terminal!

---

## Testing from Outside Docker

If you want to test from your host machine (not inside Docker):

### Install MQTT Clients
```bash
# Ubuntu/Debian
sudo apt-get install mosquitto-clients

# macOS
brew install mosquitto

# Windows
# Download from: https://mosquitto.org/download/
```

### Test Commands

**Publish a message**:
```bash
mosquitto_pub -h localhost -p 1883 -t "iot/water/level" -m "75.5"
```

**Subscribe to topics**:
```bash
mosquitto_sub -h localhost -p 1883 -t "iot/#" -v
```

**Test connection**:
```bash
mosquitto_pub -h localhost -p 1883 -t "test" -m "hello"
```

---

## Manual Testing via REST API

You can also test without MQTT using the REST API:

```bash
# Send sensor data
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 75.5,
    "turbidity": 25.3,
    "battery_voltage": 12.4,
    "battery_current": 2.8,
    "solar_voltage": 13.2,
    "solar_current": 3.5
  }'

# Get latest data
curl http://localhost:8080/backend/api/sensors.php?latest=true

# Check system status
curl http://localhost:8080/backend/api/status.php
```

---

## Verifying Data in Dashboard

1. Start the simulator: `bash tests/simulate_esp32.sh`
2. Open browser: http://localhost:8080/frontend/dashboard.php
3. Login (create account first if needed)
4. You should see live data updating!

---

## Troubleshooting

### "mosquitto_pub: command not found"

**Solution**: Install mosquitto-clients
```bash
sudo apt-get install mosquitto-clients
```

### "Connection refused" error

**Solution**: Check if mosquitto is running
```bash
docker-compose ps mosquitto
docker-compose logs mosquitto
```

### No data appearing in dashboard

**Solutions**:
1. Check if data is being published: `bash tests/mqtt_monitor.sh`
2. Check API logs: `docker-compose logs app`
3. Verify database: `docker-compose logs db`
4. Test API directly: `curl http://localhost:8080/backend/api/sensors.php?latest=true`

### Port 1883 already in use

**Solution**: Stop other MQTT broker or change port in docker-compose.yml

---

## Integration Checklist

Before giving project to ESP32 team:

- [ ] Run `mqtt_connection_test.sh` - all tests pass
- [ ] Monitor shows topics working
- [ ] Simulator runs without errors
- [ ] Dashboard displays simulated data
- [ ] API returns sensor data
- [ ] Database stores readings
- [ ] Alerts generate properly

---

## For ESP32 Team

Share these details with the hardware team:

**Connection Info**:
- MQTT Broker: `YOUR_SERVER_IP` (replace with actual)
- MQTT Port: `1883`
- Authentication: None (currently)

**Topics to publish**:
- `iot/water/level` - float
- `iot/water/turbidity` - float
- `iot/battery/voltage` - float
- `iot/battery/current` - float
- `iot/solar/voltage` - float
- `iot/solar/current` - float

**Documentation**: See `ESP32_INTEGRATION_GUIDE.md`

---

## Advanced Testing

### Load Testing
```bash
# Send 100 readings rapidly
for i in {1..100}; do
  mosquitto_pub -h localhost -t "iot/water/level" -m "75.$i"
done
```

### Test with Python
```python
import paho.mqtt.client as mqtt
import time
import random

client = mqtt.Client()
client.connect("localhost", 1883, 60)

while True:
    value = round(random.uniform(60, 95), 2)
    client.publish("iot/water/level", str(value))
    print(f"Published: {value}")
    time.sleep(5)
```

---

## All Set! 🚀

Your MQTT infrastructure is ready for ESP32 integration. Use these tools to verify everything works before hardware arrives.
