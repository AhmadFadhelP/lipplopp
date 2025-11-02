#!/bin/bash

echo "═══════════════════════════════════════════════════════════"
echo "  Testing All 6 IoT Topics"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Starting monitor..."
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v &
MONITOR_PID=$!
sleep 2

echo ""
echo "Publishing to all 6 sensor topics..."
echo ""

docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "75.5"
echo "  [1/6] ✓ Water Level"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/turbidity" -m "25.3"
echo "  [2/6] ✓ Water Turbidity"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/voltage" -m "12.4"
echo "  [3/6] ✓ Battery Voltage"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/battery/current" -m "2.8"
echo "  [4/6] ✓ Battery Current"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/voltage" -m "13.2"
echo "  [5/6] ✓ Solar Voltage"

docker exec mosquitto mosquitto_pub -h localhost -t "iot/solar/current" -m "3.5"
echo "  [6/6] ✓ Solar Current"

sleep 2
kill $MONITOR_PID 2>/dev/null

echo ""
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "✅ All 6 topics tested!"
echo ""
echo "If you saw all 6 messages above, all topics are working!"
echo ""
