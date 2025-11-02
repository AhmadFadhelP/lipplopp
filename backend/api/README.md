# IoT Monitoring System API Documentation

Complete REST API for IoT water quality and power monitoring system.

## Base Information

- **API Version**: v1
- **Base URL**: `/backend/api/`
- **Content-Type**: `application/json`
- **Timezone**: Asia/Jakarta

## Available Endpoints

### 1. Status Endpoint

Check system health and status.

**Endpoint**: `GET /backend/api/status.php`

**Response**:
```json
{
  "success": true,
  "health_status": "healthy",
  "status": {
    "api": {...},
    "database": {...},
    "mqtt": {...},
    "system": {...},
    "data": {...}
  }
}
```

---

### 2. Sensors Endpoint

Manage sensor data for water quality, battery, and solar panel monitoring.

#### Get Latest Sensor Data
**Request**: `GET /backend/api/sensors.php?latest=true`

**Response**:
```json
{
  "success": true,
  "message": "Latest sensor data retrieved",
  "data": {
    "water_level": 75.5,
    "turbidity": 25.3,
    "battery_voltage": 12.4,
    "battery_current": 2.8,
    "battery_percentage": 85.7,
    "battery_status": "Normal",
    "solar_voltage": 13.2,
    "solar_current": 3.5,
    "timestamp": "2025-11-02 14:30:00"
  }
}
```

#### Get Sensor History
**Request**: `GET /backend/api/sensors.php?limit=50&from=2025-11-01&to=2025-11-02`

**Parameters**:
- `limit` (optional): Number of records (default: 100, max: 1000)
- `from` (optional): Start date (Y-m-d H:i:s)
- `to` (optional): End date (Y-m-d H:i:s)

#### Post Sensor Data
**Request**: `POST /backend/api/sensors.php`

**Body**:
```json
{
  "water_level": 75.5,
  "turbidity": 25.3,
  "battery_voltage": 12.4,
  "battery_current": 2.8,
  "solar_voltage": 13.2,
  "solar_current": 3.5
}
```

**Response**:
```json
{
  "success": true,
  "message": "Sensor data saved successfully",
  "data": {
    "id": 123,
    "timestamp": "2025-11-02 14:30:00"
  }
}
```

#### Update Sensor Data
**Request**: `PUT /backend/api/sensors.php`

**Body**:
```json
{
  "id": 123,
  "water_level": 76.0,
  "turbidity": 24.8
}
```

#### Delete Sensor Data
**Request**: `DELETE /backend/api/sensors.php?id=123`

---

### 3. Alerts Endpoint

Manage system alerts and notifications.

#### Get Unresolved Alerts
**Request**: `GET /backend/api/alerts.php?unresolved=true`

**Response**:
```json
{
  "success": true,
  "data": {
    "count": 3,
    "statistics": {
      "info": 1,
      "warning": 2,
      "critical": 0
    },
    "alerts": [...]
  }
}
```

#### Filter Alerts
**Parameters**:
- `id`: Get specific alert
- `unresolved`: Filter unresolved alerts (true/false)
- `type`: Filter by alert type
- `level`: Filter by level (info, warning, critical)
- `limit`: Number of records (default: 50)

#### Create Alert
**Request**: `POST /backend/api/alerts.php`

**Body**:
```json
{
  "alert_type": "low_battery",
  "alert_level": "warning",
  "message": "Battery voltage is low",
  "sensor_value": 11.2,
  "threshold_value": 11.5
}
```

#### Resolve Alert
**Request**: `PUT /backend/api/alerts.php`

**Body**:
```json
{
  "id": 45,
  "resolve": true
}
```

---

### 4. Dashboard Endpoint

Get aggregated data and statistics for dashboard display.

#### Get Dashboard Summary
**Request**: `GET /backend/api/dashboard.php?action=summary`

**Response**:
```json
{
  "success": true,
  "data": {
    "current_readings": {
      "water_level": 75.5,
      "turbidity": 25.3,
      "battery": {...},
      "solar": {...}
    },
    "statistics_24h": {...},
    "alerts": {...},
    "recent_feeding": [...]
  }
}
```

#### Get Chart Data
**Request**: `GET /backend/api/dashboard.php?action=charts&hours=24`

**Parameters**:
- `hours`: Time range in hours (default: 24, max: 168)

**Response**:
```json
{
  "success": true,
  "data": {
    "period_hours": 24,
    "chart_data": {
      "labels": ["14:00", "14:10", "14:20", ...],
      "datasets": {
        "water_level": [75.5, 76.2, ...],
        "turbidity": [25.3, 24.8, ...],
        "battery_voltage": [12.4, 12.5, ...],
        ...
      }
    }
  }
}
```

#### Get Historical Data
**Request**: `GET /backend/api/dashboard.php?action=history&page=1&per_page=20`

**Parameters**:
- `page`: Page number (default: 1)
- `per_page`: Records per page (default: 20, max: 100)

---

## MQTT Integration

The API integrates with MQTT broker for real-time IoT communication.

### MQTT Topics

- **Water Level**: `iot/water/level`
- **Water Turbidity**: `iot/water/turbidity`
- **Battery Voltage**: `iot/battery/voltage`
- **Battery Current**: `iot/battery/current`
- **Solar Voltage**: `iot/solar/voltage`
- **Solar Current**: `iot/solar/current`

### MQTT Configuration

Configure in `backend/api/config.php`:
```php
define('MQTT_HOST', 'mosquitto');
define('MQTT_PORT', 1883);
```

### Test MQTT Connection
```bash
php backend/api/mqtt.php test
```

---

## Error Handling

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": ["Detailed error 1", "Detailed error 2"]
}
```

### HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized
- `404` - Not Found
- `405` - Method Not Allowed
- `429` - Rate Limit Exceeded
- `500` - Internal Server Error
- `503` - Service Unavailable

---

## Data Validation

### Sensor Data Ranges

- **Water Level**: 0-100%
- **Turbidity**: ≥0 NTU
- **Battery Voltage**: 0-20V
- **Battery Current**: -50 to 50A
- **Solar Voltage**: 0-20V
- **Solar Current**: -50 to 50A

### Alert Levels

- `info` - Informational alert
- `warning` - Warning that requires attention
- `critical` - Critical issue requiring immediate action

---

## Security Features

✅ SQL injection protection (prepared statements)
✅ Input sanitization
✅ XSS prevention
✅ CORS headers configured
✅ Rate limiting support
✅ API key authentication (optional)

---

## Database Schema

### sensor_data Table
```sql
- id (INT PRIMARY KEY)
- water_level (DECIMAL 5,2)
- turbidity (DECIMAL 6,2)
- battery_voltage (DECIMAL 5,2)
- battery_current (DECIMAL 6,2)
- solar_voltage (DECIMAL 5,2)
- solar_current (DECIMAL 6,2)
- timestamp (DATETIME)
- created_at (TIMESTAMP)
```

### sensor_alerts Table
```sql
- id (INT PRIMARY KEY)
- alert_type (VARCHAR 50)
- alert_level (ENUM: info, warning, critical)
- message (TEXT)
- sensor_value (DECIMAL 10,2)
- threshold_value (DECIMAL 10,2)
- is_resolved (BOOLEAN)
- resolved_at (DATETIME)
- created_at (TIMESTAMP)
```

---

## Usage Examples

### Using cURL

```bash
# Get latest sensor data
curl http://localhost:8080/backend/api/sensors.php?latest=true

# Post sensor data
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

# Get dashboard summary
curl http://localhost:8080/backend/api/dashboard.php?action=summary

# Check system status
curl http://localhost:8080/backend/api/status.php
```

### Using JavaScript (Fetch)

```javascript
// Get latest sensor data
async function getLatestData() {
  const response = await fetch('/backend/api/sensors.php?latest=true');
  const data = await response.json();
  console.log(data);
}

// Post sensor data
async function postSensorData(sensorData) {
  const response = await fetch('/backend/api/sensors.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(sensorData)
  });
  const result = await response.json();
  console.log(result);
}
```

---

## Logging

API logs are stored in `/logs/api_YYYY-MM-DD.log`

View recent logs:
```bash
tail -f logs/api_$(date +%Y-%m-%d).log
```

---

## Support & Troubleshooting

### Common Issues

1. **Database connection failed**
   - Check Docker containers are running
   - Verify database credentials in `config.php`

2. **MQTT publish failed**
   - Ensure mosquitto container is running
   - Check MQTT configuration

3. **No sensor data available**
   - Post initial sensor data
   - Check database tables are created

### Health Check

Always start with checking system status:
```bash
curl http://localhost:8080/backend/api/status.php
```

---

## Development

### File Structure
```
backend/api/
├── config.php      # Configuration
├── db.php          # Database connection
├── helper.php      # Utility functions
├── mqtt.php        # MQTT handler
├── sensors.php     # Sensors endpoint
├── alerts.php      # Alerts endpoint
├── dashboard.php   # Dashboard endpoint
├── status.php      # Status endpoint
├── index.php       # API index
└── README.md       # This file
```

### Adding New Endpoints

1. Create new PHP file in `backend/api/`
2. Include required files (config, db, helper)
3. Implement request handlers
4. Add documentation to this README

---

## License

Part of the IoT Water Quality Monitoring System (Tugas Akhir Project)

---

For more information, visit: `http://localhost:8080/backend/api/index.php`
