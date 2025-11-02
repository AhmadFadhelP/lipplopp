<?php
/**
 * MQTT Communication Handler
 * Handles MQTT publish/subscribe operations
 *
 * Note: This uses the mosquitto-php extension if available,
 * otherwise provides a REST API wrapper for MQTT operations.
 *
 * For production use, consider installing php-mqtt/client via Composer:
 * composer require php-mqtt/client
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helper.php';

class MQTTHandler {
    private $host;
    private $port;
    private $username;
    private $password;
    private $clientId;
    private $client = null;

    public function __construct() {
        $this->host = MQTT_HOST;
        $this->port = MQTT_PORT;
        $this->username = MQTT_USERNAME;
        $this->password = MQTT_PASSWORD;
        $this->clientId = MQTT_CLIENT_ID;
    }

    /**
     * Publish message to MQTT topic
     * This is a simple implementation using shell commands
     * For production, use a proper MQTT library
     */
    public function publish($topic, $message, $qos = 0, $retain = false) {
        try {
            // Convert message to JSON if it's an array
            if (is_array($message)) {
                $message = json_encode($message);
            }

            // Build mosquitto_pub command
            $cmd = sprintf(
                'mosquitto_pub -h %s -p %d -t %s -m %s -q %d %s 2>&1',
                escapeshellarg($this->host),
                (int)$this->port,
                escapeshellarg($topic),
                escapeshellarg($message),
                (int)$qos,
                $retain ? '-r' : ''
            );

            // Add authentication if set
            if (!empty($this->username)) {
                $cmd .= sprintf(
                    ' -u %s -P %s',
                    escapeshellarg($this->username),
                    escapeshellarg($this->password)
                );
            }

            // Execute command
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                logMessage("MQTT publish failed: " . implode("\n", $output), "ERROR");
                return false;
            }

            logMessage("MQTT published to {$topic}: {$message}", "INFO");
            return true;

        } catch (Exception $e) {
            logMessage("MQTT publish error: " . $e->getMessage(), "ERROR");
            return false;
        }
    }

    /**
     * Publish sensor data to respective topics
     */
    public function publishSensorData($data) {
        $results = [];

        if (isset($data['water_level'])) {
            $results['water_level'] = $this->publish(
                MQTT_TOPIC_WATER_LEVEL,
                $data['water_level']
            );
        }

        if (isset($data['turbidity'])) {
            $results['turbidity'] = $this->publish(
                MQTT_TOPIC_WATER_TURBIDITY,
                $data['turbidity']
            );
        }

        if (isset($data['battery_voltage'])) {
            $results['battery_voltage'] = $this->publish(
                MQTT_TOPIC_BATTERY_VOLTAGE,
                $data['battery_voltage']
            );
        }

        if (isset($data['battery_current'])) {
            $results['battery_current'] = $this->publish(
                MQTT_TOPIC_BATTERY_CURRENT,
                $data['battery_current']
            );
        }

        if (isset($data['solar_voltage'])) {
            $results['solar_voltage'] = $this->publish(
                MQTT_TOPIC_SOLAR_VOLTAGE,
                $data['solar_voltage']
            );
        }

        if (isset($data['solar_current'])) {
            $results['solar_current'] = $this->publish(
                MQTT_TOPIC_SOLAR_CURRENT,
                $data['solar_current']
            );
        }

        return $results;
    }

    /**
     * Subscribe to MQTT topic
     * Note: This requires a background process to continuously listen
     * For real-time updates, consider using WebSockets or Server-Sent Events
     */
    public function subscribe($topic, $callback, $qos = 0) {
        try {
            // Build mosquitto_sub command
            $cmd = sprintf(
                'mosquitto_sub -h %s -p %d -t %s -q %d -C 1 2>&1',
                escapeshellarg($this->host),
                (int)$this->port,
                escapeshellarg($topic),
                (int)$qos
            );

            // Add authentication if set
            if (!empty($this->username)) {
                $cmd .= sprintf(
                    ' -u %s -P %s',
                    escapeshellarg($this->username),
                    escapeshellarg($this->password)
                );
            }

            // Execute command and get single message
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && !empty($output)) {
                $message = $output[0];
                logMessage("MQTT received from {$topic}: {$message}", "INFO");

                if (is_callable($callback)) {
                    call_user_func($callback, $topic, $message);
                }

                return $message;
            }

            return null;

        } catch (Exception $e) {
            logMessage("MQTT subscribe error: " . $e->getMessage(), "ERROR");
            return null;
        }
    }

    /**
     * Get last message from topic
     */
    public function getLastMessage($topic) {
        return $this->subscribe($topic, null, 0);
    }

    /**
     * Check if MQTT broker is accessible
     */
    public function testConnection() {
        $testTopic = 'test/connection';
        $testMessage = 'ping_' . time();

        $published = $this->publish($testTopic, $testMessage);

        if (!$published) {
            return [
                'success' => false,
                'message' => 'Failed to publish to MQTT broker'
            ];
        }

        // Try to read back the message
        sleep(1); // Wait a bit for the message to be available
        $received = $this->getLastMessage($testTopic);

        return [
            'success' => ($received === $testMessage),
            'message' => ($received === $testMessage)
                ? 'MQTT connection successful'
                : 'MQTT publish succeeded but subscribe failed',
            'published' => $testMessage,
            'received' => $received
        ];
    }
}

/**
 * Get MQTT handler instance
 */
function getMQTT() {
    return new MQTTHandler();
}

/**
 * Example: Handle incoming MQTT data and save to database
 * This should be run as a background service/daemon
 */
function startMQTTListener() {
    require_once __DIR__ . '/db.php';

    $mqtt = getMQTT();
    $db = getDB();

    $topics = [
        MQTT_TOPIC_WATER_LEVEL => 'water_level',
        MQTT_TOPIC_WATER_TURBIDITY => 'turbidity',
        MQTT_TOPIC_BATTERY_VOLTAGE => 'battery_voltage',
        MQTT_TOPIC_BATTERY_CURRENT => 'battery_current',
        MQTT_TOPIC_SOLAR_VOLTAGE => 'solar_voltage',
        MQTT_TOPIC_SOLAR_CURRENT => 'solar_current',
    ];

    echo "Starting MQTT listener...\n";

    while (true) {
        foreach ($topics as $topic => $field) {
            $message = $mqtt->getLastMessage($topic);

            if ($message !== null) {
                echo "Received {$field}: {$message}\n";

                // Update or insert sensor data
                // This is a simple example - you may want more sophisticated logic
                $query = "INSERT INTO sensor_data ({$field}, timestamp)
                         VALUES (?, NOW())
                         ON DUPLICATE KEY UPDATE {$field} = ?, timestamp = NOW()";

                $db->executeQuery($query, [$message, $message], "dd");
            }
        }

        sleep(5); // Poll every 5 seconds
    }
}

// If this file is run directly (for testing or as a service)
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    echo "MQTT Handler - Running in CLI mode\n";
    echo "Use: php mqtt.php [test|listen]\n\n";

    $command = $argv[1] ?? 'test';

    if ($command === 'test') {
        echo "Testing MQTT connection...\n";
        $mqtt = getMQTT();
        $result = $mqtt->testConnection();
        print_r($result);
    } elseif ($command === 'listen') {
        startMQTTListener();
    } else {
        echo "Unknown command: {$command}\n";
    }
}
?>
