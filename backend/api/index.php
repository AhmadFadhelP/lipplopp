<?php
/**
 * API Index - Entry point and documentation
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

$endpoints = [
    'api_info' => [
        'version' => API_VERSION,
        'timezone' => API_TIMEZONE,
        'documentation' => '/backend/api/README.md'
    ],
    'endpoints' => [
        'status' => [
            'path' => '/backend/api/status.php',
            'methods' => ['GET'],
            'description' => 'System health check and status information'
        ],
        'sensors' => [
            'path' => '/backend/api/sensors.php',
            'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'description' => 'Manage sensor data (water quality, battery, solar panel)',
            'parameters' => [
                'GET' => ['id', 'latest', 'limit', 'from', 'to'],
                'POST' => ['water_level', 'turbidity', 'battery_voltage', 'battery_current', 'solar_voltage', 'solar_current'],
                'PUT' => ['id', 'field_to_update'],
                'DELETE' => ['id']
            ]
        ],
        'alerts' => [
            'path' => '/backend/api/alerts.php',
            'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'description' => 'Manage system alerts and notifications',
            'parameters' => [
                'GET' => ['id', 'unresolved', 'type', 'level', 'limit'],
                'POST' => ['alert_type', 'alert_level', 'message'],
                'PUT' => ['id', 'resolve'],
                'DELETE' => ['id']
            ]
        ],
        'dashboard' => [
            'path' => '/backend/api/dashboard.php',
            'methods' => ['GET'],
            'description' => 'Get aggregated dashboard data and statistics',
            'parameters' => [
                'GET' => ['action (summary|charts|history)', 'hours', 'page', 'per_page']
            ]
        ]
    ],
    'mqtt_topics' => [
        'water_level' => MQTT_TOPIC_WATER_LEVEL,
        'water_turbidity' => MQTT_TOPIC_WATER_TURBIDITY,
        'battery_voltage' => MQTT_TOPIC_BATTERY_VOLTAGE,
        'battery_current' => MQTT_TOPIC_BATTERY_CURRENT,
        'solar_voltage' => MQTT_TOPIC_SOLAR_VOLTAGE,
        'solar_current' => MQTT_TOPIC_SOLAR_CURRENT
    ],
    'examples' => [
        'get_latest_sensor_data' => [
            'method' => 'GET',
            'url' => '/backend/api/sensors.php?latest=true',
            'description' => 'Get the most recent sensor reading'
        ],
        'post_sensor_data' => [
            'method' => 'POST',
            'url' => '/backend/api/sensors.php',
            'body' => [
                'water_level' => 75.5,
                'turbidity' => 25.3,
                'battery_voltage' => 12.4,
                'battery_current' => 2.8,
                'solar_voltage' => 13.2,
                'solar_current' => 3.5
            ],
            'description' => 'Submit new sensor readings'
        ],
        'get_dashboard_summary' => [
            'method' => 'GET',
            'url' => '/backend/api/dashboard.php?action=summary',
            'description' => 'Get complete dashboard summary'
        ],
        'get_unresolved_alerts' => [
            'method' => 'GET',
            'url' => '/backend/api/alerts.php?unresolved=true',
            'description' => 'Get all unresolved alerts'
        ]
    ],
    'response_format' => [
        'success' => [
            'success' => true,
            'message' => 'Operation successful',
            'data' => '...'
        ],
        'error' => [
            'success' => false,
            'message' => 'Error message',
            'errors' => []
        ]
    ]
];

echo json_encode($endpoints, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
