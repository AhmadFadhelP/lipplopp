# Quick Start Guide

Complete guide to get your IoT monitoring system up and running.

## Prerequisites

- Docker and Docker Compose installed
- Ports 8080, 1883, 9001, 3306 available

## Step 1: Start the Services

```bash
# Navigate to project directory
cd /home/lipplopp/playground/padell/lipplopp

# Start all services (app, database, mosquitto)
docker-compose up -d

# Check if all containers are running
docker-compose ps
```

Expected output:
```
NAME                COMMAND                  SERVICE      STATUS
mosquitto           "/docker-entrypoint.…"   mosquitto    Up
lipplopp-app-1      "docker-php-entrypoint"  app          Up
lipplopp-db-1       "docker-entrypoint.s…"   db           Up
```

## Step 2: Verify Database Setup

```bash
# Wait for database to initialize (first time only)
sleep 10

# Check database logs
docker-compose logs db
```

The database should automatically create all tables from `database/init.sql`:
- ✅ users
- ✅ pakan
- ✅ sensor_data
- ✅ sensor_alerts

## Step 3: Test the API

```bash
# Check API status
curl http://localhost:8080/backend/api/status.php

# View all available endpoints
curl http://localhost:8080/backend/api/index.php
```

You should see a JSON response indicating system status.

## Step 4: Access the Application

Open your browser and navigate to:

### Main Application
- **Dashboard**: http://localhost:8080/frontend/dashboard.php
- **Login**: http://localhost:8080/frontend/index.php
- **Sign Up**: http://localhost:8080/frontend/signup.php

### API Endpoints
- **API Index**: http://localhost:8080/backend/api/index.php
- **System Status**: http://localhost:8080/backend/api/status.php
- **Latest Sensors**: http://localhost:8080/backend/api/sensors.php?latest=true
- **Dashboard Summary**: http://localhost:8080/backend/api/dashboard.php?action=summary

## Step 5: Create a User Account

1. Go to: http://localhost:8080/frontend/signup.php
2. Fill in:
   - Username (min 3 characters)
   - Email (valid email)
   - Password (min 8 characters)
3. Click "Register"
4. Login at: http://localhost:8080/frontend/index.php

## Step 6: Test MQTT Communication

```bash
# Enter the app container
docker-compose exec app bash

# Test MQTT connection
php backend/api/mqtt.php test

# Publish test data
mosquitto_pub -h mosquitto -t iot/water/level -m "75.5"

# Subscribe to a topic (in another terminal)
mosquitto_sub -h mosquitto -t "iot/#" -v
```

## Step 7: Send Sensor Data

### Using cURL

```bash
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

### Using MQTT

```bash
# Publish sensor readings via MQTT
mosquitto_pub -h localhost -t iot/water/level -m "75.5"
mosquitto_pub -h localhost -t iot/water/turbidity -m "25.3"
mosquitto_pub -h localhost -t iot/battery/voltage -m "12.4"
mosquitto_pub -h localhost -t iot/battery/current -m "2.8"
mosquitto_pub -h localhost -t iot/solar/voltage -m "13.2"
mosquitto_pub -h localhost -t iot/solar/current -m "3.5"
```

## Step 8: View the Dashboard

1. Login to the application
2. Go to Dashboard: http://localhost:8080/frontend/dashboard.php
3. You should see:
   - Water quality metrics
   - Battery status with visual indicator
   - Solar panel metrics
   - Real-time charts
   - Control buttons

## Common Operations

### Stop Services
```bash
docker-compose down
```

### Stop and Remove Data
```bash
docker-compose down -v
```

### Rebuild Containers
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f mosquitto

# API logs
docker-compose exec app tail -f logs/api_$(date +%Y-%m-%d).log
```

### Database Access
```bash
# Enter MySQL container
docker-compose exec db mysql -uroot -proot db_tugasakhir

# Run SQL commands
mysql> SELECT * FROM users;
mysql> SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 10;
mysql> SELECT * FROM sensor_alerts WHERE is_resolved = 0;
```

## API Usage Examples

### Get Latest Sensor Data
```bash
curl http://localhost:8080/backend/api/sensors.php?latest=true
```

### Get Dashboard Summary
```bash
curl http://localhost:8080/backend/api/dashboard.php?action=summary
```

### Get Chart Data (24 hours)
```bash
curl http://localhost:8080/backend/api/dashboard.php?action=charts&hours=24
```

### Get Unresolved Alerts
```bash
curl http://localhost:8080/backend/api/alerts.php?unresolved=true
```

### Post New Sensor Reading
```bash
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 80.0,
    "turbidity": 20.5,
    "battery_voltage": 12.8,
    "battery_current": 3.2,
    "solar_voltage": 14.1,
    "solar_current": 4.5
  }'
```

### Resolve an Alert
```bash
curl -X PUT http://localhost:8080/backend/api/alerts.php \
  -H "Content-Type: application/json" \
  -d '{"id": 1, "resolve": true}'
```

## Troubleshooting

### Database Connection Error

**Problem**: API returns database connection failed

**Solution**:
```bash
# Check if database container is running
docker-compose ps db

# Restart database
docker-compose restart db

# Wait for database to be ready
sleep 10
```

### MQTT Not Working

**Problem**: Cannot publish/subscribe to MQTT

**Solution**:
```bash
# Check mosquitto container
docker-compose ps mosquitto

# Restart mosquitto
docker-compose restart mosquitto

# Test connection
docker-compose exec app mosquitto_pub -h mosquitto -t test -m "hello"
```

### Port Already in Use

**Problem**: Port 8080/1883/3306 is already in use

**Solution**:
Edit `docker-compose.yml` and change the port mapping:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081
```

### Permission Denied on Logs/Cache

**Problem**: Cannot write to logs or cache directory

**Solution**:
```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/logs /var/www/html/cache
```

### No Sensor Data Available

**Problem**: Dashboard shows no data

**Solution**:
```bash
# Post initial sensor data
curl -X POST http://localhost:8080/backend/api/sensors.php \
  -H "Content-Type: application/json" \
  -d '{
    "water_level": 75.0,
    "turbidity": 25.0,
    "battery_voltage": 12.5,
    "battery_current": 3.0,
    "solar_voltage": 13.5,
    "solar_current": 3.5
  }'
```

## Development Tips

### Enable PHP Error Display

```bash
# Edit PHP configuration
docker-compose exec app bash
echo "display_errors = On" >> /usr/local/etc/php/php.ini
echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini

# Restart container
docker-compose restart app
```

### Watch API Logs in Real-Time

```bash
docker-compose exec app tail -f logs/api_*.log
```

### Reset Database

```bash
# Stop containers
docker-compose down -v

# Start fresh
docker-compose up -d

# Database will be recreated from init.sql
```

## Next Steps

1. ✅ Services running
2. ✅ Database initialized
3. ✅ API tested
4. ✅ User account created
5. ✅ MQTT working
6. ✅ Sensor data flowing

### Recommended Next Steps:

- Set up automated sensor data collection
- Configure alert thresholds
- Customize dashboard UI
- Add more sensor types
- Implement data export features
- Set up monitoring and backups

## Support

- **API Documentation**: http://localhost:8080/backend/api/README.md
- **API Endpoints**: http://localhost:8080/backend/api/index.php
- **System Status**: http://localhost:8080/backend/api/status.php

## Project Structure

```
lipplopp/
├── backend/
│   ├── api/              # REST API endpoints ✅ Fixed
│   ├── login.php
│   ├── register.php
│   └── simpan_pakan.php
├── frontend/
│   ├── dashboard.php     # Main dashboard
│   └── ...
├── database/
│   ├── config.php
│   └── init.sql         # ✅ Updated with new tables
├── docker-compose.yml
├── Dockerfile           # ✅ Updated with MQTT support
└── logs/               # API logs
```

## All Fixed! 🎉

The project is now fully functional with:
- ✅ Complete REST API backend
- ✅ Database schema with sensor tables
- ✅ MQTT integration
- ✅ Security features
- ✅ Comprehensive documentation
- ✅ Health monitoring
- ✅ Error handling

Enjoy your IoT monitoring system!
