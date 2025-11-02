<?php
/**
 * Alerts API Endpoint
 * Handles system alerts and notifications
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helper.php';

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
 * GET - Retrieve alerts
 * Query params:
 * - id: Get specific alert
 * - unresolved: Get only unresolved alerts
 * - type: Filter by alert type
 * - level: Filter by alert level (info, warning, critical)
 * - limit: Number of records (default 50)
 */
function handleGet($db) {
    // Get specific alert by ID
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT * FROM sensor_alerts WHERE id = ?";
        $data = $db->fetchOne($query, [$id], "i");

        if ($data) {
            sendSuccess($data, "Alert retrieved");
        } else {
            sendError("Alert not found", 404);
        }
        return;
    }

    // Build query with filters
    $conditions = [];
    $params = [];
    $types = "";

    // Filter by resolved status
    if (isset($_GET['unresolved']) && $_GET['unresolved'] === 'true') {
        $conditions[] = "is_resolved = 0";
    }

    // Filter by alert type
    if (isset($_GET['type'])) {
        $conditions[] = "alert_type = ?";
        $params[] = sanitizeString($_GET['type']);
        $types .= "s";
    }

    // Filter by alert level
    if (isset($_GET['level'])) {
        $level = sanitizeString($_GET['level']);
        if (in_array($level, ['info', 'warning', 'critical'])) {
            $conditions[] = "alert_level = ?";
            $params[] = $level;
            $types .= "s";
        }
    }

    // Date range filters
    if (isset($_GET['from'])) {
        $conditions[] = "created_at >= ?";
        $params[] = $_GET['from'];
        $types .= "s";
    }

    if (isset($_GET['to'])) {
        $conditions[] = "created_at <= ?";
        $params[] = $_GET['to'];
        $types .= "s";
    }

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $limit = min($limit, 500); // Max 500 records

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
    $query = "SELECT * FROM sensor_alerts {$whereClause} ORDER BY created_at DESC LIMIT {$limit}";

    $data = $db->fetchAll($query, $params, $types);

    // Get counts by level
    $stats = $db->fetchOne("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN is_resolved = 0 THEN 1 ELSE 0 END) as unresolved,
            SUM(CASE WHEN alert_level = 'info' AND is_resolved = 0 THEN 1 ELSE 0 END) as info,
            SUM(CASE WHEN alert_level = 'warning' AND is_resolved = 0 THEN 1 ELSE 0 END) as warning,
            SUM(CASE WHEN alert_level = 'critical' AND is_resolved = 0 THEN 1 ELSE 0 END) as critical
        FROM sensor_alerts
    ");

    sendSuccess([
        'count' => count($data),
        'limit' => $limit,
        'statistics' => $stats,
        'alerts' => $data
    ], "Alerts retrieved");
}

/**
 * POST - Create new alert
 */
function handlePost($db) {
    $input = getJsonInput();

    // Validate required fields
    validateRequired($input, ['alert_type', 'message']);

    $alertType = sanitizeString($input['alert_type']);
    $alertLevel = isset($input['alert_level']) ? sanitizeString($input['alert_level']) : 'info';
    $message = sanitizeString($input['message']);
    $sensorValue = $input['sensor_value'] ?? null;
    $thresholdValue = $input['threshold_value'] ?? null;

    // Validate alert level
    if (!in_array($alertLevel, ['info', 'warning', 'critical'])) {
        sendError("Invalid alert level. Must be: info, warning, or critical", 400);
    }

    $query = "INSERT INTO sensor_alerts
              (alert_type, alert_level, message, sensor_value, threshold_value)
              VALUES (?, ?, ?, ?, ?)";

    $id = $db->insert($query, [
        $alertType,
        $alertLevel,
        $message,
        $sensorValue,
        $thresholdValue
    ], "sssdd");

    if ($id) {
        logMessage("Alert created: [{$alertLevel}] {$alertType} - {$message}", "ALERT");

        sendSuccess([
            'id' => $id,
            'alert_type' => $alertType,
            'alert_level' => $alertLevel
        ], "Alert created successfully", 201);
    } else {
        sendError("Failed to create alert", 500);
    }
}

/**
 * PUT - Update alert (mainly for resolving)
 */
function handlePut($db) {
    $input = getJsonInput();

    if (!isset($input['id'])) {
        sendError("ID is required for update", 400);
    }

    $id = (int)$input['id'];

    // Check if alert exists
    $existing = $db->fetchOne("SELECT * FROM sensor_alerts WHERE id = ?", [$id], "i");
    if (!$existing) {
        sendError("Alert not found", 404);
    }

    // Resolve alert
    if (isset($input['resolve']) && $input['resolve'] === true) {
        $query = "UPDATE sensor_alerts
                  SET is_resolved = 1, resolved_at = NOW()
                  WHERE id = ?";

        $stmt = $db->executeQuery($query, [$id], "i");

        if ($stmt) {
            logMessage("Alert #{$id} resolved", "ALERT");
            sendSuccess(['id' => $id], "Alert resolved successfully");
        } else {
            sendError("Failed to resolve alert", 500);
        }
        return;
    }

    sendError("No valid update action provided", 400);
}

/**
 * DELETE - Remove alert
 */
function handleDelete($db) {
    if (!isset($_GET['id'])) {
        sendError("ID is required for deletion", 400);
    }

    $id = (int)$_GET['id'];

    // Check if alert exists
    $existing = $db->fetchOne("SELECT * FROM sensor_alerts WHERE id = ?", [$id], "i");
    if (!$existing) {
        sendError("Alert not found", 404);
    }

    $query = "DELETE FROM sensor_alerts WHERE id = ?";
    $stmt = $db->executeQuery($query, [$id], "i");

    if ($stmt) {
        sendSuccess(['id' => $id], "Alert deleted successfully");
    } else {
        sendError("Failed to delete alert", 500);
    }
}
?>
