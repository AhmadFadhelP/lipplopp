<?php
/**
 * Sensors API Endpoint
 * Handles CRUD operations for sensor data
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/mqtt.php';

$method = validateMethod(['GET', 'POST', 'PUT', 'DELETE']);
$db = getDB();

// Route handling based on request method
switch ($method) {
    case 'GET':
        handleGet($db);
        break;

    case 'POST':
        handlePost($db);
        break;

    case 'PUT':
        handlePut($db);
        break;

    case 'DELETE':
        handleDelete($db);
        break;
}

/**
 * GET - Retrieve sensor data
 * Query params:
 * - id: Get specific record
 * - latest: Get latest reading
 * - limit: Number of records (default 100)
 * - from: Start date (Y-m-d H:i:s)
 * - to: End date (Y-m-d H:i:s)
 */
function handleGet($db) {
    // Get latest reading
    if (isset($_GET['latest'])) {
        $query = "SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 1";
        $data = $db->fetchOne($query);

        if ($data) {
            $data['battery_percentage'] = calculateBatteryPercentage($data['battery_voltage']);
            $data['battery_status'] = getBatteryStatus($data['battery_voltage'], $data['battery_current']);
            sendSuccess($data, "Latest sensor data retrieved");
        } else {
            sendError("No sensor data available", 404);
        }
    }

    // Get specific record by ID
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM sensor_data WHERE id = ?";
        $data = $db->fetchOne($query, [$id], "i");

        if ($data) {
            sendSuccess(formatSensorData($data), "Sensor data retrieved");
        } else {
            sendError("Sensor data not found", 404);
        }
    }

    // Get multiple records with filters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $limit = min($limit, 1000); // Max 1000 records

    $conditions = [];
    $params = [];
    $types = "";

    if (isset($_GET['from'])) {
        $conditions[] = "timestamp >= ?";
        $params[] = $_GET['from'];
        $types .= "s";
    }

    if (isset($_GET['to'])) {
        $conditions[] = "timestamp <= ?";
        $params[] = $_GET['to'];
        $types .= "s";
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
    $query = "SELECT * FROM sensor_data {$whereClause} ORDER BY timestamp DESC LIMIT {$limit}";

    $data = $db->fetchAll($query, $params, $types);

    sendSuccess([
        'count' => count($data),
        'limit' => $limit,
        'records' => array_map('formatSensorData', $data)
    ], "Sensor data retrieved");
}

/**
 * POST - Create new sensor data
 * Body: JSON with sensor values
 */
function handlePost($db) {
    $input = getJsonInput();

    // Validate sensor data
    validateSensorData($input);

    // Prepare values
    $waterLevel = $input['water_level'] ?? null;
    $turbidity = $input['turbidity'] ?? null;
    $batteryVoltage = $input['battery_voltage'] ?? null;
    $batteryCurrent = $input['battery_current'] ?? null;
    $solarVoltage = $input['solar_voltage'] ?? null;
    $solarCurrent = $input['solar_current'] ?? null;
    $timestamp = $input['timestamp'] ?? getCurrentTimestamp();

    // Insert into database
    $query = "INSERT INTO sensor_data
              (water_level, turbidity, battery_voltage, battery_current,
               solar_voltage, solar_current, timestamp)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $waterLevel,
        $turbidity,
        $batteryVoltage,
        $batteryCurrent,
        $solarVoltage,
        $solarCurrent,
        $timestamp
    ];

    $id = $db->insert($query, $params, "dddddds");

    if ($id) {
        // Publish to MQTT
        $mqtt = getMQTT();
        $mqtt->publishSensorData($input);

        // Check for alerts
        checkAndCreateAlerts($db, $input);

        sendSuccess([
            'id' => $id,
            'timestamp' => $timestamp
        ], "Sensor data saved successfully", 201);
    } else {
        sendError("Failed to save sensor data", 500);
    }
}

/**
 * PUT - Update existing sensor data
 */
function handlePut($db) {
    $input = getJsonInput();

    if (!isset($input['id'])) {
        sendError("ID is required for update", 400);
    }

    $id = (int)$input['id'];

    // Check if record exists
    $existing = $db->fetchOne("SELECT * FROM sensor_data WHERE id = ?", [$id], "i");
    if (!$existing) {
        sendError("Sensor data not found", 404);
    }

    // Validate sensor data
    validateSensorData($input);

    // Build update query dynamically
    $fields = [];
    $params = [];
    $types = "";

    $allowedFields = [
        'water_level' => 'd',
        'turbidity' => 'd',
        'battery_voltage' => 'd',
        'battery_current' => 'd',
        'solar_voltage' => 'd',
        'solar_current' => 'd',
        'timestamp' => 's'
    ];

    foreach ($allowedFields as $field => $type) {
        if (isset($input[$field])) {
            $fields[] = "{$field} = ?";
            $params[] = $input[$field];
            $types .= $type;
        }
    }

    if (empty($fields)) {
        sendError("No fields to update", 400);
    }

    $params[] = $id;
    $types .= "i";

    $query = "UPDATE sensor_data SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $db->executeQuery($query, $params, $types);

    if ($stmt) {
        sendSuccess(['id' => $id], "Sensor data updated successfully");
    } else {
        sendError("Failed to update sensor data", 500);
    }
}

/**
 * DELETE - Remove sensor data
 */
function handleDelete($db) {
    if (!isset($_GET['id'])) {
        sendError("ID is required for deletion", 400);
    }

    $id = (int)$_GET['id'];

    // Check if record exists
    $existing = $db->fetchOne("SELECT * FROM sensor_data WHERE id = ?", [$id], "i");
    if (!$existing) {
        sendError("Sensor data not found", 404);
    }

    $query = "DELETE FROM sensor_data WHERE id = ?";
    $stmt = $db->executeQuery($query, [$id], "i");

    if ($stmt) {
        sendSuccess(['id' => $id], "Sensor data deleted successfully");
    } else {
        sendError("Failed to delete sensor data", 500);
    }
}

/**
 * Check sensor values and create alerts if needed
 */
function checkAndCreateAlerts($db, $data) {
    $alerts = [];

    // Check water level
    if (isset($data['water_level']) && $data['water_level'] < 30) {
        $alerts[] = [
            'type' => 'low_water_level',
            'level' => 'warning',
            'message' => "Water level is low: {$data['water_level']}%",
            'value' => $data['water_level'],
            'threshold' => 30
        ];
    }

    // Check turbidity
    if (isset($data['turbidity']) && $data['turbidity'] > 50) {
        $alerts[] = [
            'type' => 'high_turbidity',
            'level' => 'warning',
            'message' => "Water turbidity is high: {$data['turbidity']} NTU",
            'value' => $data['turbidity'],
            'threshold' => 50
        ];
    }

    // Check battery voltage
    if (isset($data['battery_voltage']) && $data['battery_voltage'] < 11.5) {
        $alerts[] = [
            'type' => 'low_battery',
            'level' => $data['battery_voltage'] < 10.8 ? 'critical' : 'warning',
            'message' => "Battery voltage is low: {$data['battery_voltage']}V",
            'value' => $data['battery_voltage'],
            'threshold' => 11.5
        ];
    }

    // Insert alerts
    foreach ($alerts as $alert) {
        $query = "INSERT INTO sensor_alerts
                  (alert_type, alert_level, message, sensor_value, threshold_value)
                  VALUES (?, ?, ?, ?, ?)";

        $db->insert($query, [
            $alert['type'],
            $alert['level'],
            $alert['message'],
            $alert['value'],
            $alert['threshold']
        ], "ssddd");
    }
}
?>
