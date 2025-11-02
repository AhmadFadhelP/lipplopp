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
        if (!isset($data[$field]) || empty($data[$field])) {
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
        sendError("{$fieldName} must be a numeric value", 400);
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
 * Rate limiting check (simple implementation)
 */
function checkRateLimit($identifier, $maxRequests = 100, $timeWindow = 3600) {
    $cacheFile = __DIR__ . '/../../cache/rate_limit_' . md5($identifier) . '.txt';

    if (!file_exists(dirname($cacheFile))) {
        mkdir(dirname($cacheFile), 0755, true);
    }

    $currentTime = time();
    $requests = [];

    if (file_exists($cacheFile)) {
        $content = file_get_contents($cacheFile);
        $requests = json_decode($content, true) ?: [];

        // Remove old requests outside time window
        $requests = array_filter($requests, function($timestamp) use ($currentTime, $timeWindow) {
            return ($currentTime - $timestamp) < $timeWindow;
        });
    }

    if (count($requests) >= $maxRequests) {
        sendError("Rate limit exceeded. Try again later.", 429);
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
        'water_level' => (float)$rawData['water_level'],
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
 * Calculate battery percentage from voltage
 * Assuming 12V battery system (10.5V = 0%, 12.6V = 100%)
 */
function calculateBatteryPercentage($voltage) {
    $minVoltage = 10.5;
    $maxVoltage = 12.6;

    $percentage = (($voltage - $minVoltage) / ($maxVoltage - $minVoltage)) * 100;
    $percentage = max(0, min(100, $percentage)); // Clamp between 0-100

    return round($percentage, 1);
}

/**
 * Determine battery status
 */
function getBatteryStatus($voltage, $current) {
    $percentage = calculateBatteryPercentage($voltage);

    if ($percentage >= 95) {
        return 'Full';
    } elseif ($percentage <= 20 && $current <= 0) {
        return 'Low';
    } elseif ($current > 0) {
        return 'Charging';
    } else {
        return 'Normal';
    }
}

/**
 * Validate sensor data ranges
 */
function validateSensorData($data) {
    $errors = [];

    if (isset($data['water_level'])) {
        if ($data['water_level'] < 0 || $data['water_level'] > 100) {
            $errors[] = "Water level must be between 0 and 100";
        }
    }

    if (isset($data['turbidity'])) {
        if ($data['turbidity'] < 0) {
            $errors[] = "Turbidity cannot be negative";
        }
    }

    if (isset($data['battery_voltage'])) {
        if ($data['battery_voltage'] < 0 || $data['battery_voltage'] > 20) {
            $errors[] = "Battery voltage must be between 0 and 20V";
        }
    }

    if (isset($data['battery_current'])) {
        if ($data['battery_current'] < -50 || $data['battery_current'] > 50) {
            $errors[] = "Battery current must be between -50 and 50A";
        }
    }

    if (!empty($errors)) {
        sendError("Sensor data validation failed", 400, $errors);
    }

    return true;
}
?>
