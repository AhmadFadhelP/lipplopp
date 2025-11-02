#!/bin/bash

###############################################################################
# MQTT Topic Monitor
# Purpose: Monitor all MQTT topics in real-time (what backend receives)
###############################################################################

echo "=========================================="
echo "MQTT Topic Monitor"
echo "=========================================="
echo ""
echo "Monitoring all IoT topics: iot/#"
echo "This shows exactly what your backend will receive from ESP32"
echo ""
echo "Press Ctrl+C to stop"
echo ""
echo "Waiting for messages..."
echo "=========================================="
echo ""

# Monitor all topics with verbose output
mosquitto_sub -h localhost -p 1883 -t "iot/#" -v -F "%I %t %p"
