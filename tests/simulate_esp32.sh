#!/bin/bash

###############################################################################
# ESP32 Simulation Script
# Purpose: Continuously simulate ESP32 sending sensor data via MQTT
###############################################################################

echo "=========================================="
echo "ESP32 Sensor Data Simulator"
echo "=========================================="
echo ""
echo "This script simulates an ESP32 device sending"
echo "sensor data to the MQTT broker continuously."
echo ""
echo "Press Ctrl+C to stop"
echo ""

# Configuration
MQTT_HOST="localhost"
MQTT_PORT=1883
INTERVAL=5  # seconds between readings

# Function to generate random sensor values
generate_water_level() {
    echo $(awk -v min=60 -v max=95 'BEGIN{srand(); print min+rand()*(max-min)}')
}

generate_turbidity() {
    echo $(awk -v min=15 -v max=45 'BEGIN{srand(); print min+rand()*(max-min)}')
}

generate_battery_voltage() {
    echo $(awk -v min=11.5 -v max=12.8 'BEGIN{srand(); print min+rand()*(max-min)}')
}

generate_battery_current() {
    echo $(awk -v min=1.5 -v max=4.5 'BEGIN{srand(); print min+rand()*(max-min)}')
}

generate_solar_voltage() {
    echo $(awk -v min=12.0 -v max=14.5 'BEGIN{srand(); print min+rand()*(max-min)}')
}

generate_solar_current() {
    echo $(awk -v min=2.0 -v max=5.0 'BEGIN{srand(); print min+rand()*(max-min)}')
}

# Counter
count=0

# Main loop
while true; do
    count=$((count + 1))

    # Generate sensor values
    water_level=$(generate_water_level)
    turbidity=$(generate_turbidity)
    battery_voltage=$(generate_battery_voltage)
    battery_current=$(generate_battery_current)
    solar_voltage=$(generate_solar_voltage)
    solar_current=$(generate_solar_current)

    # Display readings
    echo "Reading #$count at $(date '+%H:%M:%S')"
    echo "  Water Level:      $water_level %"
    echo "  Turbidity:        $turbidity NTU"
    echo "  Battery Voltage:  $battery_voltage V"
    echo "  Battery Current:  $battery_current A"
    echo "  Solar Voltage:    $solar_voltage V"
    echo "  Solar Current:    $solar_current A"

    # Publish to MQTT topics (simulating ESP32)
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/water/level" -m "$water_level" -q 1
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/water/turbidity" -m "$turbidity" -q 1
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/battery/voltage" -m "$battery_voltage" -q 1
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/battery/current" -m "$battery_current" -q 1
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/solar/voltage" -m "$solar_voltage" -q 1
    mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/solar/current" -m "$solar_current" -q 1

    # Also send via API
    curl -s -X POST http://localhost:8080/backend/api/sensors.php \
      -H "Content-Type: application/json" \
      -d "{
        \"water_level\": $water_level,
        \"turbidity\": $turbidity,
        \"battery_voltage\": $battery_voltage,
        \"battery_current\": $battery_current,
        \"solar_voltage\": $solar_voltage,
        \"solar_current\": $solar_current
      }" > /dev/null

    echo "  ✓ Data published to MQTT and API"
    echo ""

    # Wait before next reading
    sleep $INTERVAL
done
