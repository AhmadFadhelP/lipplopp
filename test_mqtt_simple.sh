#!/bin/bash

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║            MQTT CONNECTION TEST - Simple Demo                ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "Step 1: Starting monitor in background..."
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v &
MONITOR_PID=$!
sleep 2

echo ""
echo "Step 2: Publishing test messages..."
echo ""

docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "75.5"
echo "  ► Published: water level = 75.5%"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "12.4"
echo "  ► Published: battery voltage = 12.4V"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "3.5"
echo "  ► Published: solar current = 3.5A"

sleep 2
kill $MONITOR_PID 2>/dev/null

echo ""
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "✅ If you saw messages above like:"
echo "   iot/water/level 75.5"
echo "   iot/battery/voltage 12.4"
echo ""
echo "Then MQTT is WORKING! 🎉"
echo ""
