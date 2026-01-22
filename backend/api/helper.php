<?php
/**
 * Helper Functions
 * Utility functions for API operations
 */

/**
 * Send JSON response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Send success response
 */
function sendSuccess($data = [], $message = "Success") {
    sendResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], 200);
}

/**
 * Send error response
 */
function sendError($message = "An error occurred", $statusCode = 400, $errors = []) {
    sendResponse([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}

/**
 * Validate request method
 */
function validateMethod($allowedMethods = []) {
    $method = $_SERVER['REQUEST_METHOD'];

    if (!in_array($method, $allowedMethods)) {
        sendError("Method not allowed. Allowed methods: " . implode(', ', $allowedMethods), 405);
    }

    return $method;
}

/**
 * Get JSON input from request body
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError("Invalid JSON input", 400);
    }

    return $data ?: [];
}

/**
 * Sanitize string input
 */
function sanitizeString($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate required fields
 */
function validateRequired($data, $requiredFields) {
    $errors = [];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            $errors[] = "Field '{$field}' is required";
        }
    }

    if (!empty($errors)) {
        sendError("Validation failed", 400, $errors);
    }

    return true;
}

/**
 * Validate numeric value
 */
function validateNumeric($value, $fieldName = "Value") {
    if (!is_numeric($value)) {
        sendError("{$fieldName} must be numeric", 400);
    }
    return floatval($value);
}

/**
 * Validate range
 */
function validateRange($value, $min, $max, $fieldName = "Value") {
    $numValue = validateNumeric($value, $fieldName);

    if ($numValue < $min || $numValue > $max) {
        sendError("{$fieldName} must be between {$min} and {$max}", 400);
    }

    return $numValue;
}

/**
 * Get current timestamp
 */
function getCurrentTimestamp() {
    date_default_timezone_set("Asia/Jakarta");
    return date('Y-m-d H:i:s');
}

/**
 * Log message to file
 */
function logMessage($message, $type = "INFO") {
    $logDir = __DIR__ . '/../../logs';

    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/api_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;

    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Verify API key (if needed)
 */
function verifyApiKey($providedKey) {
    $validKey = getenv('API_KEY') ?: 'your_secure_api_key_here';

    if ($providedKey !== $validKey) {
        sendError("Invalid API key", 401);
    }

    return true;
}

/**
 * Rate limiting check
 */
function checkRateLimit($identifier, $maxRequests = 100, $timeWindow = 3600) {
    $cacheFile = __DIR__ . '/../../cache/rate_limit_' . md5($identifier) . '.txt';

    if (!file_exists(dirname($cacheFile))) {
        mkdir(dirname($cacheFile), 0755, true);
    }

    $currentTime = time();
    $requests = [];

    if (file_exists($cacheFile)) {
        $requests = json_decode(file_get_contents($cacheFile), true) ?: [];

        // Remove old requests
        $requests = array_filter($requests, function($timestamp) use ($currentTime, $timeWindow) {
            return ($currentTime - $timestamp) < $timeWindow;
        });
    }

    if (count($requests) >= $maxRequests) {
        sendError("Rate limit exceeded", 429);
    }

    $requests[] = $currentTime;
    file_put_contents($cacheFile, json_encode($requests));

    return true;
}

/**
 * Format sensor data for response
 */
function formatSensorData($rawData) {
    return [
        'id' => (int)$rawData['id'],
        'water_level' => isset($rawData['water_level']) ? (float)$rawData['water_level'] : null,
        'turbidity' => (float)$rawData['turbidity'],
        'battery_voltage' => (float)$rawData['battery_voltage'],
        'battery_current' => (float)$rawData['battery_current'],
        'solar_voltage' => (float)$rawData['solar_voltage'],
        'solar_current' => (float)$rawData['solar_current'],
        'timestamp' => $rawData['timestamp'],
        'created_at' => $rawData['created_at']
    ];
}

/**
 * Battery percentage (12V system)
 */
function calculateBatteryPercentage($voltage) {
    $minVoltage = 10.5;
    $maxVoltage = 12.6;

    $percentage = (($voltage - $minVoltage) / ($maxVoltage - $minVoltage)) * 100;
    return round(max(0, min(100, $percentage)), 1);
}

/**
 * Battery status
 */
function getBatteryStatus($voltage, $current) {
    $percentage = calculateBatteryPercentage($voltage);

    if ($percentage >= 95) return 'Full';
    if ($percentage <= 20 && $current <= 0) return 'Low';
    if ($current > 0) return 'Charging';
    return 'Normal';
}

/**
 * ================================
 * VALIDATE SENSOR DATA (FINAL FIX)
 * ================================
 */
function validateSensorData($data) {
    $required = ['turbidity', 'battery_voltage', 'battery_current', 'solar_voltage', 'solar_current'];

    // Cek field wajib
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            sendError("Missing field: $field", 400);
        }
        if (!is_numeric($data[$field])) {
            sendError("$field must be numeric", 400);
        }
    }

    // water_level boleh kosong
    if (isset($data['water_level']) && $data['water_level'] !== null) {
        if (!is_numeric($data['water_level'])) {
            sendError("water_level must be numeric", 400);
        }
    }

    return true;
}

?>
