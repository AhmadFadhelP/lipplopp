<?php
/**
 * Dashboard API Endpoint
 * Provides aggregated data and statistics for the dashboard
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helper.php';

$method = validateMethod(['GET']);
$db = getDB();

// Handle GET requests
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'summary';

    switch ($action) {
        case 'summary':
            getDashboardSummary($db);
            break;

        case 'charts':
            getChartData($db);
            break;

        case 'history':
            getHistoricalData($db);
            break;

        default:
            sendError("Invalid action. Available actions: summary, charts, history", 400);
    }
}

/**
 * Get dashboard summary with latest readings and statistics
 */
function getDashboardSummary($db) {
    // Get latest sensor data
    $latestData = $db->fetchOne("
        SELECT * FROM sensor_data
        ORDER BY timestamp DESC
        LIMIT 1
    ");

    if (!$latestData) {
        sendError("No sensor data available", 404);
    }

    // Calculate battery metrics
    $batteryPercentage = calculateBatteryPercentage($latestData['battery_voltage']);
    $batteryStatus = getBatteryStatus($latestData['battery_voltage'], $latestData['battery_current']);

    // Get unresolved alerts
    $unresolvedAlerts = $db->fetchAll("
        SELECT * FROM sensor_alerts
        WHERE is_resolved = 0
        ORDER BY created_at DESC
        LIMIT 5
    ");

    // Get alert counts
    $alertCounts = $db->fetchOne("
        SELECT
            SUM(CASE WHEN alert_level = 'info' AND is_resolved = 0 THEN 1 ELSE 0 END) as info,
            SUM(CASE WHEN alert_level = 'warning' AND is_resolved = 0 THEN 1 ELSE 0 END) as warning,
            SUM(CASE WHEN alert_level = 'critical' AND is_resolved = 0 THEN 1 ELSE 0 END) as critical
        FROM sensor_alerts
    ");

    // Get statistics for the last 24 hours
    $stats24h = $db->fetchOne("
        SELECT
            AVG(water_level) as avg_water_level,
            MIN(water_level) as min_water_level,
            MAX(water_level) as max_water_level,
            AVG(turbidity) as avg_turbidity,
            MIN(turbidity) as min_turbidity,
            MAX(turbidity) as max_turbidity,
            AVG(battery_voltage) as avg_battery_voltage,
            MIN(battery_voltage) as min_battery_voltage,
            MAX(battery_voltage) as max_battery_voltage,
            AVG(solar_voltage) as avg_solar_voltage,
            MAX(solar_voltage) as max_solar_voltage,
            COUNT(*) as reading_count
        FROM sensor_data
        WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");

    // Get feeding history (from pakan table)
    $recentFeeding = $db->fetchAll("
        SELECT * FROM pakan
        ORDER BY waktu DESC
        LIMIT 5
    ");

    // Prepare response
    $summary = [
        'current_readings' => [
            'water_level' => (float)$latestData['water_level'],
            'turbidity' => (float)$latestData['turbidity'],
            'battery' => [
                'voltage' => (float)$latestData['battery_voltage'],
                'current' => (float)$latestData['battery_current'],
                'percentage' => $batteryPercentage,
                'status' => $batteryStatus
            ],
            'solar' => [
                'voltage' => (float)$latestData['solar_voltage'],
                'current' => (float)$latestData['solar_current'],
                'power' => round((float)$latestData['solar_voltage'] * (float)$latestData['solar_current'], 2)
            ],
            'timestamp' => $latestData['timestamp']
        ],
        'statistics_24h' => [
            'water_level' => [
                'avg' => round((float)$stats24h['avg_water_level'], 2),
                'min' => (float)$stats24h['min_water_level'],
                'max' => (float)$stats24h['max_water_level']
            ],
            'turbidity' => [
                'avg' => round((float)$stats24h['avg_turbidity'], 2),
                'min' => (float)$stats24h['min_turbidity'],
                'max' => (float)$stats24h['max_turbidity']
            ],
            'battery_voltage' => [
                'avg' => round((float)$stats24h['avg_battery_voltage'], 2),
                'min' => (float)$stats24h['min_battery_voltage'],
                'max' => (float)$stats24h['max_battery_voltage']
            ],
            'solar_voltage' => [
                'avg' => round((float)$stats24h['avg_solar_voltage'], 2),
                'max' => (float)$stats24h['max_solar_voltage']
            ],
            'reading_count' => (int)$stats24h['reading_count']
        ],
        'alerts' => [
            'counts' => [
                'info' => (int)$alertCounts['info'],
                'warning' => (int)$alertCounts['warning'],
                'critical' => (int)$alertCounts['critical'],
                'total' => (int)$alertCounts['info'] + (int)$alertCounts['warning'] + (int)$alertCounts['critical']
            ],
            'recent' => $unresolvedAlerts
        ],
        'recent_feeding' => $recentFeeding
    ];

    sendSuccess($summary, "Dashboard summary retrieved");
}

/**
 * Get data formatted for charts
 */
function getChartData($db) {
    $hours = isset($_GET['hours']) ? (int)$_GET['hours'] : 24;
    $hours = min($hours, 168); // Max 1 week

    $interval = $hours <= 24 ? 10 : 60; // 10 min intervals for 24h, 1h for longer periods

    $query = "
        SELECT
            DATE_FORMAT(timestamp, '%Y-%m-%d %H:%i:00') as time_bucket,
            AVG(water_level) as water_level,
            AVG(turbidity) as turbidity,
            AVG(battery_voltage) as battery_voltage,
            AVG(battery_current) as battery_current,
            AVG(solar_voltage) as solar_voltage,
            AVG(solar_current) as solar_current
        FROM sensor_data
        WHERE timestamp >= DATE_SUB(NOW(), INTERVAL ? HOUR)
        GROUP BY time_bucket
        ORDER BY time_bucket ASC
    ";

    $data = $db->fetchAll($query, [$hours], "i");

    // Format data for Chart.js
    $chartData = [
        'labels' => [],
        'datasets' => [
            'water_level' => [],
            'turbidity' => [],
            'battery_voltage' => [],
            'battery_current' => [],
            'solar_voltage' => [],
            'solar_current' => []
        ]
    ];

    foreach ($data as $row) {
        $chartData['labels'][] = $row['time_bucket'];
        $chartData['datasets']['water_level'][] = round((float)$row['water_level'], 2);
        $chartData['datasets']['turbidity'][] = round((float)$row['turbidity'], 2);
        $chartData['datasets']['battery_voltage'][] = round((float)$row['battery_voltage'], 2);
        $chartData['datasets']['battery_current'][] = round((float)$row['battery_current'], 2);
        $chartData['datasets']['solar_voltage'][] = round((float)$row['solar_voltage'], 2);
        $chartData['datasets']['solar_current'][] = round((float)$row['solar_current'], 2);
    }

    sendSuccess([
        'period_hours' => $hours,
        'data_points' => count($data),
        'chart_data' => $chartData
    ], "Chart data retrieved");
}

/**
 * Get historical data with pagination
 */
function getHistoricalData($db) {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? min(100, (int)$_GET['per_page']) : 20;
    $offset = ($page - 1) * $perPage;

    // Get total count
    $totalCount = $db->fetchOne("SELECT COUNT(*) as count FROM sensor_data");
    $total = (int)$totalCount['count'];
    $totalPages = ceil($total / $perPage);

    // Get paginated data
    $query = "
        SELECT * FROM sensor_data
        ORDER BY timestamp DESC
        LIMIT ? OFFSET ?
    ";

    $data = $db->fetchAll($query, [$perPage, $offset], "ii");

    sendSuccess([
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ],
        'data' => array_map('formatSensorData', $data)
    ], "Historical data retrieved");
}
?>
