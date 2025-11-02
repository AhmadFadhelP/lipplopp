# Test Dashboard with Real Data

## ✅ Changes Made

**Dashboard now shows REAL DATA instead of random data!**

- ❌ OLD: Generated random sensor values every 5 seconds
- ✅ NEW: Fetches actual data from API every 5 seconds
- ✅ Battery indicator calculates real percentage from voltage

---

## 🧪 How to Test

### Step 1: Send Some Test Data

Run this command to send test sensor data:

```bash
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 85.5,
    "turbidity": 22.3,
    "battery_voltage": 12.4,
    "battery_current": 2.8,
    "solar_voltage": 13.2,
    "solar_current": 3.5
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Sensor data saved successfully"
}
```

### Step 2: Open Dashboard

1. Open browser: http://localhost:8080/frontend/dashboard.php
2. Login with your account
3. **You should see:**
   - Water Level: **85.5%**
   - Turbidity: **22.3 NTU**
   - Battery Voltage: **12.4 V**
   - Battery Current: **2.8 A**
   - Solar Voltage: **13.2 V**
   - Solar Current: **3.5 A**

### Step 3: Send Different Data

Now send different values to verify it updates:

```bash
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 95.0,
    "turbidity": 15.5,
    "battery_voltage": 12.8,
    "battery_current": 3.2,
    "solar_voltage": 14.1,
    "solar_current": 4.5
  }'
```

### Step 4: Wait and Watch

- Wait **5 seconds** (dashboard updates every 5 seconds)
- Refresh if needed
- **Numbers should change to new values!**
  - Water Level: **95.0%** ← Changed!
  - Turbidity: **15.5 NTU** ← Changed!
  - Battery: **12.8 V** ← Changed!

---

## 📊 Test with MQTT

Now test with **actual MQTT** (simulating ESP32):

### Using Docker Commands:

```bash
# Terminal 1 - Monitor what's being sent
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v

# Terminal 2 - Send data via MQTT
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "99.9"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/turbidity" -m "10.5"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "13.0"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/current" -m "4.0"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/voltage" -m "14.5"
docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "5.0"
```

**Note:** MQTT publishes to topics, but **you need to send data to API** for dashboard to see it.

For now, use the curl command above, or wait for backend MQTT listener to be implemented.

---

## 🔄 Automated Test

Use the test script to send continuous data:

```bash
# Send test data every 5 seconds
bash test_all_topics.sh
```

Then send to API:

```bash
# Send to API so dashboard sees it
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
```

Watch the dashboard update!

---

## 🎯 Verification Checklist

- [ ] Dashboard shows "**--**" when no data available
- [ ] Sending data via API updates dashboard within 5 seconds
- [ ] Sending different values changes the display
- [ ] Battery percentage is calculated from voltage (not random)
- [ ] Charts update with real values
- [ ] No more random fluctuations every 5 seconds

**All checked?** → ✅ **Dashboard is now using REAL DATA!**

---

## 🔍 Troubleshooting

### Dashboard shows "--" (dashes)

**Cause:** No sensor data in database yet

**Fix:** Send initial data:
```bash
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{"water_level": 75, "turbidity": 25, "battery_voltage": 12.4, "battery_current": 2.8, "solar_voltage": 13.2, "solar_current": 3.5}'
```

### Dashboard doesn't update

**Causes:**
1. API not responding
2. Database not connected
3. Browser cached old version

**Fix:**
```bash
# Check API works
curl http://localhost:8080/backend/api/sensors.php?latest=true

# Hard refresh browser
Press Ctrl+Shift+R (or Cmd+Shift+R on Mac)
```

### Still seeing random data

**Fix:** Clear browser cache:
1. Press F12 (open dev tools)
2. Right-click refresh button
3. Select "Empty Cache and Hard Reload"

---

## 📝 What Changed in Code

### Before (Random Data):
```javascript
function updateRandomData() {
  const tingkatAir = getRandom(50, 100, 0);  // Random!
  const kekeruhan = getRandom(10, 80, 1);    // Random!
  // ... etc
}
```

### After (Real Data):
```javascript
async function updateRealData() {
  const response = await fetch('/backend/api/sensors.php?latest=true');
  const data = result.data;
  const tingkatAir = parseFloat(data.water_level);  // Real data from API!
  const kekeruhan = parseFloat(data.turbidity);     // Real data from API!
  // ... etc
}
```

---

## ✅ Summary

✓ Dashboard now fetches REAL data from `/backend/api/sensors.php?latest=true`
✓ Updates every 5 seconds automatically
✓ Shows "--" when no data available
✓ Battery percentage calculated from actual voltage
✓ You can verify by sending different data and watching it update

**Now when ESP32 sends data via MQTT → It will show on dashboard!** 🎉

(After MQTT listener is implemented to save MQTT data to database)
