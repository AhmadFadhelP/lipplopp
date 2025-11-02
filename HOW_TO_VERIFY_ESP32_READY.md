# How to Verify MQTT is Ready for ESP32

Quick guide to show your team that the backend is ready for ESP32 integration.

**⚡ TL;DR - Quick Test:**
```bash
bash test_mqtt_simple.sh
```
If you see messages appearing → **MQTT is working!** ✅

**No installation required** - All tests use Docker!

---

## 🎯 Goal

Demonstrate that:
1. ✅ MQTT broker is running and accessible
2. ✅ All topics are configured correctly
3. ✅ Backend can receive and store data
4. ✅ Dashboard displays data in real-time

---

## 🚀 Available Test Scripts

| Script | Purpose | Command |
|--------|---------|---------|
| **Simple Test** | Quick MQTT verification | `bash test_mqtt_simple.sh` |
| **All Topics** | Test all 6 sensor topics | `bash test_all_topics.sh` |
| **Quick Test** | Comprehensive check | `bash tests/quick_mqtt_test.sh` |

**All scripts use Docker - no mosquitto-clients installation needed!**

---

## 📋 Quick Demo (5 minutes)

### Step 1: Start Everything
```bash
# Navigate to project
cd /home/lipplopp/playground/padell/lipplopp

# Start all services
docker-compose up -d

# Wait 10 seconds for services to start
sleep 10
```

### Step 2: Run Simple MQTT Test (EASIEST!)

**Option A - Super Simple (Recommended):**
```bash
# One command to test everything
bash test_mqtt_simple.sh
```

**Expected Output:**
```
╔══════════════════════════════════════════════════════════════╗
║            MQTT CONNECTION TEST - Simple Demo                ║
╚══════════════════════════════════════════════════════════════╝

Step 1: Starting monitor in background...

Step 2: Publishing test messages...

iot/water/level 75.5
  ► Published: water level = 75.5%
iot/battery/voltage 12.4
  ► Published: battery voltage = 12.4V
iot/solar/current 3.5
  ► Published: solar current = 3.5A

✅ MQTT is WORKING! 🎉
```

**Option B - Test All 6 Topics:**
```bash
# Test all sensor topics at once
bash test_all_topics.sh
```

**Expected Output:**
```
═══════════════════════════════════════════════════════════
  Testing All 6 IoT Topics
═══════════════════════════════════════════════════════════

iot/water/level 75.5
  [1/6] ✓ Water Level
iot/water/turbidity 25.3
  [2/6] ✓ Water Turbidity
iot/battery/voltage 12.4
  [3/6] ✓ Battery Voltage
iot/battery/current 2.8
  [4/6] ✓ Battery Current
iot/solar/voltage 13.2
  [5/6] ✓ Solar Voltage
iot/solar/current 3.5
  [6/6] ✓ Solar Current

✅ All 6 topics tested!
```

✅ **If you see messages appearing → MQTT is working!**

### Step 3: Manual Two-Terminal Test (Visual Demo)

This is the **best way to visually demonstrate** MQTT working:

**Terminal 1 - Monitor (shows what backend receives)**:
```bash
# Using Docker - no installation needed!
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v
```
*(Leave this running - it will show all incoming MQTT messages)*

**Terminal 2 - Publisher (simulating ESP32)**:
```bash
# Send test messages
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "80.0"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "12.8"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "3.5"
```

**Alternative - Use Simulator Script:**
```bash
# Terminal 2 - Continuous simulation
bash tests/simulate_esp32.sh
```

### Step 4: Watch the Magic ✨

In **Terminal 1** you'll see:
```
==========================================
MQTT Topic Monitor
==========================================

Monitoring all IoT topics: iot/#
This shows exactly what your backend will receive from ESP32

Waiting for messages...
==========================================

2025-11-02 14:30:15 iot/water/level 75.23
2025-11-02 14:30:15 iot/water/turbidity 28.45
2025-11-02 14:30:15 iot/battery/voltage 12.34
2025-11-02 14:30:15 iot/battery/current 2.87
2025-11-02 14:30:15 iot/solar/voltage 13.56
2025-11-02 14:30:15 iot/solar/current 3.21
```

In **Terminal 2** you'll see:
```
==========================================
ESP32 Sensor Data Simulator
==========================================

Reading #1 at 14:30:15
  Water Level:      75.23 %
  Turbidity:        28.45 NTU
  Battery Voltage:  12.34 V
  Battery Current:  2.87 A
  Solar Voltage:    13.56 V
  Solar Current:    3.21 A
  ✓ Data published to MQTT and API
```

### Step 5: Check Dashboard

1. Open browser: http://localhost:8080/frontend/dashboard.php
2. Login (create account at signup page if needed)
3. **Watch data update live!** 🎉

---

## 🎬 Screen Recording Demo

Record your screen showing:

1. ✅ Running `mqtt_connection_test.sh` - all green checkmarks
2. ✅ Two terminals side by side:
   - Left: Monitor showing incoming messages
   - Right: Simulator sending data
3. ✅ Browser with dashboard showing live updates
4. ✅ Chart data flowing in real-time

**This proves to ESP32 team**: "Just send data to these topics, and it works!"

---

## 📸 Screenshot Checklist

Take screenshots of:

1. **Connection test passing**:
   - Shows all ✓ green checkmarks
   - "MQTT is ready for ESP32 integration!"

2. **MQTT Monitor**:
   - Shows messages arriving on topics
   - Proves broker is receiving data

3. **Dashboard**:
   - Shows live sensor values
   - Charts updating
   - Battery indicator working

4. **API Response**:
```bash
curl http://localhost:8080/backend/api/sensors.php?latest=true | jq
```
   - Shows JSON response with latest sensor data

---

## 🔍 Manual Verification Steps (Using Docker)

**Note:** All commands use Docker - **no need to install mosquitto-clients**!

### Test 1: MQTT Publish Works
```bash
# Publish a test message
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "88.8"

# Verify it was received (in another terminal)
docker exec mosquitto mosquitto_sub -h localhost -t "iot/water/level" -C 1
# Should output: 88.8
```

✅ **If you see 88.8, MQTT publish works!**

### Test 2: Quick Publish & Subscribe Test
```bash
# Start subscriber in background, publish, then check
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v &
sleep 1
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "77.7"
sleep 1
kill %1
```

✅ **If you see "iot/water/level 77.7", MQTT is working!**

### Test 3: All Topics Work
```bash
# Start monitor
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v &
sleep 1

# Publish to all topics
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "75.5"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/turbidity" -m "25.3"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "12.4"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/current" -m "2.8"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/voltage" -m "13.2"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "3.5"

sleep 2
kill %1
```

✅ **If you see all 6 messages, all topics configured correctly!**

**Or use the automated script:**
```bash
bash test_all_topics.sh
```

### Test 4: Data Persists in Database
```bash
# Send data
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{"water_level": 99.9, "turbidity": 11.1, "battery_voltage": 12.5, "battery_current": 3.0, "solar_voltage": 14.0, "solar_current": 4.0}'

# Wait 2 seconds
sleep 2

# Retrieve it
curl http://localhost:8080/backend/api/sensors.php?latest=true | grep "99.9"
```

✅ **If you see 99.9, database storage works!**

---

## 📊 What to Show ESP32 Team

### 1. Connection Details Document
Share `ESP32_INTEGRATION_GUIDE.md` with:
- MQTT broker address
- Port number (1883)
- All topic names
- Sample ESP32 code

### 2. Live Demo
Show them:
- Terminal with MQTT monitor running
- You publish a message manually
- Message appears in monitor instantly
- Dashboard updates in real-time

### 3. Test Results
Show output from:
```bash
bash tests/mqtt_connection_test.sh
```

### 4. API Documentation
Point them to:
- http://localhost:8080/backend/api/index.php
- Shows all available endpoints
- Includes MQTT topics

---

## 🚀 One-Command Demo

Run everything at once:
```bash
# Start services and run tests
docker-compose up -d && \
sleep 10 && \
echo "=== Running Connection Test ===" && \
bash tests/mqtt_connection_test.sh && \
echo "" && \
echo "=== Starting Simulator (Press Ctrl+C to stop) ===" && \
bash tests/simulate_esp32.sh
```

Then open: http://localhost:8080/frontend/dashboard.php

---

## ✅ Checklist for "Ready" Status

Before telling ESP32 team you're ready:

- [ ] `docker-compose ps` shows all 3 containers running
- [ ] `mqtt_connection_test.sh` passes all tests
- [ ] `mqtt_monitor.sh` shows incoming messages
- [ ] `simulate_esp32.sh` runs without errors
- [ ] Dashboard displays simulated data
- [ ] API returns latest sensor data
- [ ] Database stores sensor readings
- [ ] All 6 MQTT topics working

**If all checked**: ✅ **READY FOR ESP32!**

---

## 🎓 Teaching ESP32 Team

### Show them this simple test:

**From your server (to demonstrate MQTT is working):**
```bash
# Test using Docker (no installation needed)
docker exec mosquitto mosquitto_pub -h localhost -t "iot/test" -m "hello_from_server"
docker exec mosquitto mosquitto_sub -h localhost -t "iot/test" -C 1
# Should output: hello_from_server
```

**From their ESP32 (or laptop on same network):**
```bash
# Test 1: Can we reach the broker?
telnet YOUR_SERVER_IP 1883
# If connected, broker is reachable

# Test 2: Can we publish? (requires mosquitto-clients on their machine)
mosquitto_pub -h YOUR_SERVER_IP -p 1883 -t "iot/test" -m "hello"
# Check with monitor if message arrives

# Test 3: Full data
mosquitto_pub -h YOUR_SERVER_IP -p 1883 -t "iot/water/level" -m "75.5"
# Check dashboard if it appears
```

**You can monitor from your server:**
```bash
# Watch for ESP32 messages
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v
```

---

## 📝 Summary for ESP32 Team

**Email/Message Template**:

---

Hi ESP32 Team,

The backend is ready to receive data from your devices! 🎉

**MQTT Broker Details:**
- Host: `[YOUR_SERVER_IP]`
- Port: `1883`
- No authentication required

**Topics to publish to:**
```
iot/water/level        - float (%)
iot/water/turbidity    - float (NTU)
iot/battery/voltage    - float (V)
iot/battery/current    - float (A)
iot/solar/voltage      - float (V)
iot/solar/current      - float (A)
```

**Test it works:**
```bash
mosquitto_pub -h [YOUR_SERVER_IP] -p 1883 -t "iot/water/level" -m "75.5"
```

Then check: http://[YOUR_SERVER_IP]:8080/frontend/dashboard.php

**Full documentation:** See `ESP32_INTEGRATION_GUIDE.md`

**Sample ESP32 code:** Included in the guide

Questions? Let me know!

---

## 🎉 You're All Set!

Your MQTT infrastructure is **production-ready** for ESP32 integration. The tests prove it works end-to-end.

**Next**: Hand off `ESP32_INTEGRATION_GUIDE.md` to hardware team and they can start coding! 🚀

---

## 📚 Docker Commands Quick Reference

**All commands work without installing mosquitto-clients - they use Docker!**

### Check MQTT Status
```bash
# Check if mosquitto is running
docker-compose ps mosquitto

# Check mosquitto logs
docker logs mosquitto --tail 20

# Check if port is accessible
timeout 2 bash -c "cat < /dev/null > /dev/tcp/localhost/1883" && echo "✓ Port 1883 OK"
```

### Publish Messages (Simulating ESP32)
```bash
# Single topic
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "75.5"

# All topics
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "75.5"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/turbidity" -m "25.3"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "12.4"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/current" -m "2.8"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/voltage" -m "13.2"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "3.5"
```

### Subscribe/Monitor Messages
```bash
# Monitor all IoT topics (live)
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v

# Monitor specific topic
docker exec mosquitto mosquitto_sub -h localhost -t "iot/water/level" -v

# Get one message and exit
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -C 1
```

### Quick Tests
```bash
# Automated simple test
bash test_mqtt_simple.sh

# Test all 6 topics
bash test_all_topics.sh

# Comprehensive test
bash tests/quick_mqtt_test.sh
```

### Two-Terminal Live Demo
```bash
# Terminal 1 - Monitor
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v

# Terminal 2 - Publish
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "88.8"
```

### Debugging
```bash
# Restart mosquitto
docker-compose restart mosquitto

# Restart all services
docker-compose restart

# View mosquitto config
docker exec mosquitto cat /mosquitto/config/mosquitto.conf

# Check MQTT connection from inside container
docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello" && echo "✓ MQTT working"
```

---

## ✅ Final Checklist

Before sharing with ESP32 team:

- [ ] Run `bash test_mqtt_simple.sh` → See messages ✓
- [ ] Run `bash test_all_topics.sh` → All 6 topics work ✓
- [ ] `docker-compose ps` → All containers Up ✓
- [ ] Can publish: `docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hi"` ✓
- [ ] Can subscribe: `docker exec mosquitto mosquitto_sub -h localhost -t "test" -C 1` ✓
- [ ] Dashboard updates when publishing data ✓

**All checked?** → ✅ **100% READY FOR ESP32!** 🚀
