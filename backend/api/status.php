<?php
/**
 * Status/Health Check API Endpoint
 * Provides system health and status information
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/mqtt.php';

$method = validateMethod(['GET']);
$db = getDB();

// Get system status
$status = [
    'api' => [
        'status' => 'online',
        'version' => API_VERSION,
        'timestamp' => getCurrentTimestamp(),
        'timezone' => API_TIMEZONE
    ],
    'database' => checkDatabaseStatus($db),
    'mqtt' => checkMQTTStatus(),
    'system' => getSystemInfo(),
    'data' => getDataStatus($db)
];

// Determine overall health
$healthy = (
    $status['database']['connected'] === true &&
    $status['data']['has_data'] === true
);

$httpCode = $healthy ? 200 : 503;

sendResponse([
    'success' => $healthy,
    'message' => $healthy ? 'System is healthy' : 'System has issues',
    'health_status' => $healthy ? 'healthy' : 'degraded',
    'status' => $status
], $httpCode);

/**
 * Check database connection and tables
 */
function checkDatabaseStatus($db) {
    try {
        $conn = $db->getConnection();

        if (!$conn || $conn->connect_error) {
            return [
                'connected' => false,
                'error' => 'Connection failed'
            ];
        }

        // Check required tables
        $tables = ['users', 'pakan', 'sensor_data', 'sensor_alerts'];
        $existingTables = [];

        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '{$table}'");
            $existingTables[$table] = ($result && $result->num_rows > 0);
        }

        return [
            'connected' => true,
            'host' => DB_HOST,
            'database' => DB_NAME,
            'tables' => $existingTables
        ];

    } catch (Exception $e) {
        return [
            'connected' => false,
            'error' => 'Database check failed'
        ];
    }
}

/**
 * Check MQTT broker status
 */
function checkMQTTStatus() {
    try {
        $mqtt = getMQTT();
        $result = $mqtt->testConnection();

        return [
            'host' => MQTT_HOST,
            'port' => MQTT_PORT,
            'status' => $result['success'] ? 'connected' : 'disconnected',
            'test_result' => $result
        ];

    } catch (Exception $e) {
        return [
            'host' => MQTT_HOST,
            'port' => MQTT_PORT,
            'status' => 'error',
            'error' => 'MQTT check failed'
        ];
    }
}

/**
 * Get system information
 */
function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
        'server_time' => date('Y-m-d H:i:s'),
        'uptime' => getUptime(),
        'memory' => [
            'used' => formatBytes(memory_get_usage(true)),
            'peak' => formatBytes(memory_get_peak_usage(true)),
            'limit' => ini_get('memory_limit')
        ]
    ];
}

/**
 * Get data status (record counts, last update, etc.)
 */
function getDataStatus($db) {
    try {
        // Get sensor data count
        $sensorCount = $db->fetchOne("SELECT COUNT(*) as count FROM sensor_data");

        // Get latest sensor reading
        $latestSensor = $db->fetchOne("
            SELECT timestamp FROM sensor_data
            ORDER BY timestamp DESC
            LIMIT 1
        ");

        // Get unresolved alerts count
        $alertCount = $db->fetchOne("
            SELECT COUNT(*) as count FROM sensor_alerts
            WHERE is_resolved = 0
        ");

        // Get user count
        $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users");

        // Get feeding records count
        $feedingCount = $db->fetchOne("SELECT COUNT(*) as count FROM pakan");

        return [
            'has_data' => (int)$sensorCount['count'] > 0,
            'sensor_records' => (int)$sensorCount['count'],
            'last_sensor_reading' => $latestSensor['timestamp'] ?? null,
            'unresolved_alerts' => (int)$alertCount['count'],
            'total_users' => (int)$userCount['count'],
            'feeding_records' => (int)$feedingCount['count']
        ];

    } catch (Exception $e) {
        return [
            'has_data' => false,
            'error' => 'Failed to retrieve data status'
        ];
    }
}

/**
 * Get server uptime (if available)
 */
function getUptime() {
    if (file_exists('/proc/uptime')) {
        $uptime = file_get_contents('/proc/uptime');
        $uptime = explode(' ', $uptime)[0];
        return formatUptime((int)$uptime);
    }
    return 'N/A';
}

/**
 * Format uptime seconds to readable string
 */
function formatUptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    return "{$days}d {$hours}h {$minutes}m";
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}
?>
