<?php
/**
 * API Configuration File
 * Contains database and MQTT broker settings
 */

// Database Configuration
define('DB_HOST', 'db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'db_tugasakhir');

// MQTT Broker Configuration
define('MQTT_HOST', 'mosquitto');
define('MQTT_PORT', 1883);
define('MQTT_USERNAME', ''); // Set if authentication is enabled
define('MQTT_PASSWORD', ''); // Set if authentication is enabled
define('MQTT_CLIENT_ID', 'php_backend_' . uniqid());

// MQTT Topics
define('MQTT_TOPIC_WATER_LEVEL', 'iot/water/level');
define('MQTT_TOPIC_WATER_TURBIDITY', 'iot/water/turbidity');
define('MQTT_TOPIC_BATTERY_VOLTAGE', 'iot/battery/voltage');
define('MQTT_TOPIC_BATTERY_CURRENT', 'iot/battery/current');
define('MQTT_TOPIC_SOLAR_VOLTAGE', 'iot/solar/voltage');
define('MQTT_TOPIC_SOLAR_CURRENT', 'iot/solar/current');

// API Settings
define('API_VERSION', 'v1');
define('API_TIMEZONE', 'Asia/Jakarta');

// Set timezone
date_default_timezone_set(API_TIMEZONE);

// CORS Settings (Allow frontend to access API)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
