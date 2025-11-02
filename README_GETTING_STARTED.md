# Getting Started - IoT Water Monitoring System

Quick start guide to get your IoT Water Monitoring System up and running in minutes!

---

## 🚀 Quick Start (5 Minutes)

### 1. Install Docker

**Choose your platform:**
- **Windows**: Download from https://www.docker.com/products/docker-desktop
- **Linux**: See detailed instructions in [DOCKER_SETUP_GUIDE.md](DOCKER_SETUP_GUIDE.md)

### 2. Start the Application

```bash
# Navigate to project folder
cd lipplopp

# Start all services
docker-compose up -d

# Wait 30 seconds for services to start...

# Run system test
bash test_complete_system.sh
```

### 3. Access the Dashboard

1. Open browser: http://localhost:8080/frontend/index.php
2. Register a new account
3. Login and view dashboard

**Done! Your system is running!** ✅

---

## 📚 Documentation Index

### For Software/Backend Team

| Document | Purpose |
|----------|---------|
| **DOCKER_SETUP_GUIDE.md** | Complete Docker installation and setup for Windows/Linux |
| **QUICKSTART.md** | Quick commands and workflow |
| **WORKFLOW.md** | Development workflow and best practices |

### For Hardware/Firmware Team

| Document | Purpose |
|----------|---------|
| **ESP32_INTEGRATION_GUIDE.md** | Complete ESP32 integration with MQTT and API |
| **HOW_TO_VERIFY_ESP32_READY.md** | Testing checklist before hardware integration |
| **MQTT_READY_SUMMARY.md** | MQTT broker configuration and testing |

### System Testing

| Document | Purpose |
|----------|---------|
| **TEST_DASHBOARD_REAL_DATA.md** | Testing dashboard with real sensor data |
| **FIXES_SUMMARY.md** | Recent bug fixes and improvements |

---

## 🎯 What This System Does

### Real-Time Monitoring
- **Water Quality**: Level and turbidity monitoring
- **Power System**: Battery and solar panel monitoring
- **Feed Control**: Automated fish feeding system
- **Alerts**: Automatic alerts for critical conditions

### Technology Stack
- **Frontend**: PHP with responsive dashboard
- **Backend**: REST API with MySQL database
- **IoT Communication**: MQTT broker for ESP32 devices
- **Deployment**: Docker containers for easy deployment

---

## 🏗️ System Architecture

```
┌─────────────┐
│   ESP32     │  ← Your Hardware
│  (Sensors)  │
└──────┬──────┘
       │
       │ MQTT (port 1883)
       │
┌──────▼──────────────────────────────┐
│     Docker Containers                │
│                                      │
│  ┌──────────┐  ┌────────┐  ┌─────┐ │
│  │Mosquitto │  │  Web   │  │MySQL│ │
│  │  MQTT    │  │  App   │  │ DB  │ │
│  └────┬─────┘  └───┬────┘  └──┬──┘ │
│       │            │           │     │
│       └────────────┴───────────┘     │
└──────────────┬───────────────────────┘
               │
         Port 8080 (HTTP)
               │
        ┌──────▼──────┐
        │   Browser   │
        │ (Dashboard) │
        └─────────────┘
```

---

## 💡 Common Workflows

### For First Time Setup

1. **Install Docker** → See [DOCKER_SETUP_GUIDE.md](DOCKER_SETUP_GUIDE.md)
2. **Start containers** → `docker-compose up -d`
3. **Run test** → `bash test_complete_system.sh`
4. **Access dashboard** → http://localhost:8080
5. **Read ESP32 guide** → See [ESP32_INTEGRATION_GUIDE.md](ESP32_INTEGRATION_GUIDE.md)

### For Daily Development

```bash
# Start system
docker-compose up -d

# View logs
docker-compose logs -f

# Make code changes (auto-reloads)
# Edit files in backend/ or frontend/

# Test changes
bash test_complete_system.sh

# Stop system
docker-compose down
```

### For ESP32 Integration

1. **Get server IP address**
   ```bash
   # Linux
   hostname -I

   # Windows
   ipconfig
   ```

2. **Update ESP32 code**
   ```cpp
   const char* mqtt_server = "192.168.1.100";  // Your IP
   ```

3. **Monitor MQTT data**
   ```bash
   bash tests/mqtt_monitor.sh
   ```

4. **Simulate ESP32 (for testing)**
   ```bash
   bash tests/simulate_esp32.sh
   ```

---

## 🧪 Testing Tools

### System Health Check
```bash
bash test_complete_system.sh
```
Tests MQTT, API, database, and dashboard in one command.

### MQTT Testing
```bash
# Monitor all MQTT topics
bash tests/mqtt_monitor.sh

# Test MQTT connection
bash tests/mqtt_connection_test.sh

# Simulate ESP32 sending data
bash tests/simulate_esp32.sh
```

### API Testing
```bash
# Send test sensor data
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 85.5,
    "turbidity": 22.3,
    "battery_voltage": 12.6,
    "battery_current": 3.2,
    "solar_voltage": 13.8,
    "solar_current": 4.1
  }'

# Get latest data
curl http://localhost:8080/backend/api/sensors.php?latest=true

# Get historical data
curl http://localhost:8080/backend/api/sensors.php?limit=10
```

---

## 📊 Dashboard Features

### Real-Time Monitoring Cards
- Water Level (with progress bar)
- Water Turbidity (NTU)
- Battery Status (animated indicator)
- Battery Voltage & Current
- Solar Panel Voltage & Current

### Charts
- **Water Quality Chart**: Level and turbidity trends
- **Power System Chart**: Battery and solar metrics
- Updates every 30 seconds with last 20 data points

### Feed Control
- Manual feeding trigger
- Feeding history log
- Automated scheduling (optional)

### Auto-Refresh
- Display values: Every 5 seconds
- Charts: Every 30 seconds
- No page reload needed!

---

## 🔧 Troubleshooting Quick Fixes

### Containers won't start
```bash
docker-compose down
docker-compose up -d
docker-compose logs
```

### Dashboard not accessible
```bash
# Check if running
docker-compose ps

# Check logs
docker-compose logs app
```

### No data showing
```bash
# Send test data
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{"water_level": 75, "turbidity": 25, "battery_voltage": 12.5, "battery_current": 3, "solar_voltage": 13.5, "solar_current": 4}'

# Verify data saved
curl http://localhost:8080/backend/api/sensors.php?latest=true
```

### MQTT not working
```bash
# Check Mosquitto is running
docker-compose ps mosquitto

# Test MQTT
docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello"
```

---

## 🌐 Network Access

### Local Access Only
```
Dashboard: http://localhost:8080
MQTT: localhost:1883
```

### Network Access (other devices)

1. Find server IP:
   ```bash
   # Linux
   hostname -I

   # Windows
   ipconfig
   ```

2. Allow firewall:
   ```bash
   # Linux
   sudo ufw allow 8080/tcp
   sudo ufw allow 1883/tcp

   # Windows: Use Windows Security → Firewall
   ```

3. Access from other devices:
   ```
   Dashboard: http://192.168.1.100:8080
   MQTT: 192.168.1.100:1883
   ```

---

## 📦 What's Included

### Backend API Endpoints
- `GET /backend/api/sensors.php?latest=true` - Latest sensor data
- `GET /backend/api/sensors.php?limit=20` - Historical data
- `POST /backend/api/sensors.php` - Add new sensor data
- More endpoints in `/backend/api/`

### MQTT Topics
```
iot/water/level       → Water level (%)
iot/water/turbidity   → Turbidity (NTU)
iot/battery/voltage   → Battery voltage (V)
iot/battery/current   → Battery current (A)
iot/solar/voltage     → Solar voltage (V)
iot/solar/current     → Solar current (A)
```

### Database Tables
- `users` - Authentication
- `sensor_data` - Sensor readings
- `sensor_alerts` - Alert records
- `feeding_schedule` - Feed control

---

## 🎓 Learning Path

### New to Docker?
1. Read: [DOCKER_SETUP_GUIDE.md](DOCKER_SETUP_GUIDE.md)
2. Practice: Start/stop containers
3. Explore: Check logs and inspect containers

### Working on ESP32?
1. Read: [ESP32_INTEGRATION_GUIDE.md](ESP32_INTEGRATION_GUIDE.md)
2. Test: Use simulation scripts
3. Monitor: Watch MQTT topics
4. Deploy: Connect real hardware

### Backend Development?
1. Check: API documentation in `/backend/api/`
2. Test: Use curl or Postman
3. Debug: Check logs with `docker-compose logs app`

---

## ⚡ Quick Reference

### Essential Commands
```bash
# Lifecycle
docker-compose up -d        # Start
docker-compose down         # Stop
docker-compose restart      # Restart

# Monitoring
docker-compose ps           # Status
docker-compose logs -f      # Follow logs
docker stats                # Resource usage

# Testing
bash test_complete_system.sh                # Full test
bash tests/mqtt_monitor.sh                  # Watch MQTT
bash tests/simulate_esp32.sh                # Simulate data
```

### Important URLs
```
Dashboard:  http://localhost:8080/frontend/dashboard.php
Login:      http://localhost:8080/frontend/index.php
API:        http://localhost:8080/backend/api/sensors.php
```

### Important Ports
```
8080 - Web Application
1883 - MQTT Broker
3306 - MySQL (internal only)
```

---

## 🎯 Next Steps

Based on your role:

### Backend Developer
1. ✅ Setup complete - containers running
2. 📖 Read API documentation in `/backend/api/`
3. 🧪 Test APIs with curl/Postman
4. 💻 Start coding!

### Frontend Developer
1. ✅ Dashboard accessible
2. 📖 Check `/frontend/dashboard.php`
3. 🎨 Customize UI/UX
4. 📊 Add new visualizations

### Hardware/Firmware Engineer
1. ✅ Backend ready to receive data
2. 📖 Read [ESP32_INTEGRATION_GUIDE.md](ESP32_INTEGRATION_GUIDE.md)
3. 🧪 Test with simulation scripts
4. 🔌 Connect ESP32 hardware

### DevOps/Deployment
1. ✅ Docker setup working
2. 📖 Read production section in [DOCKER_SETUP_GUIDE.md](DOCKER_SETUP_GUIDE.md)
3. 🔒 Setup security (HTTPS, auth)
4. 🚀 Deploy to cloud/server

---

## 📞 Support

### Getting Help

1. **Check Documentation**
   - Look for .md files in project root
   - Check comments in code

2. **Check Logs**
   ```bash
   docker-compose logs
   ```

3. **Test System**
   ```bash
   bash test_complete_system.sh
   ```

4. **Common Issues**
   - See troubleshooting sections in docs
   - Check Docker is running
   - Verify firewall settings

---

## ✅ Success Checklist

- [ ] Docker installed
- [ ] Containers running (`docker-compose ps`)
- [ ] System test passed (`test_complete_system.sh`)
- [ ] Dashboard accessible (http://localhost:8080)
- [ ] Account created and can login
- [ ] Can send test data via API
- [ ] Data appears on dashboard
- [ ] Charts updating
- [ ] Read ESP32 integration guide

**All checked?** You're ready to go! 🎉

---

## 🌟 Project Highlights

### ✅ Complete System
- Full-stack IoT monitoring solution
- Real-time data visualization
- MQTT and REST API support
- Automated alerts

### ✅ Easy Deployment
- Docker-based (cross-platform)
- One command to start
- Auto-configured services
- Built-in testing

### ✅ Production Ready
- Secure authentication
- Database persistence
- Error handling
- Comprehensive logging

### ✅ Well Documented
- Setup guides for all platforms
- ESP32 integration guide
- Testing documentation
- Code examples

---

**Happy Monitoring!** 📊🚀

For detailed instructions, see the specific guide for your needs:
- 🐳 **Docker Setup**: [DOCKER_SETUP_GUIDE.md](DOCKER_SETUP_GUIDE.md)
- 🔌 **ESP32 Integration**: [ESP32_INTEGRATION_GUIDE.md](ESP32_INTEGRATION_GUIDE.md)
- 🧪 **Testing**: Run `bash test_complete_system.sh`

---

*Last Updated: 2025-11-02*
