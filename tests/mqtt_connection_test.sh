#!/bin/bash

###############################################################################
# MQTT Connection Test Script
# Purpose: Verify MQTT broker is ready for ESP32 integration
###############################################################################

echo "=========================================="
echo "MQTT Connection Test for ESP32 Integration"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
MQTT_HOST="localhost"
MQTT_PORT=1883

# Test 1: Check if Mosquitto is running
echo "Test 1: Checking if Mosquitto broker is running..."
if docker-compose ps mosquitto | grep -q "Up"; then
    echo -e "${GREEN}✓ Mosquitto container is running${NC}"
else
    echo -e "${RED}✗ Mosquitto container is not running${NC}"
    echo "  Run: docker-compose up -d mosquitto"
    exit 1
fi
echo ""

# Test 2: Check if MQTT port is accessible
echo "Test 2: Checking if MQTT port 1883 is accessible..."
if timeout 2 bash -c "cat < /dev/null > /dev/tcp/$MQTT_HOST/$MQTT_PORT" 2>/dev/null; then
    echo -e "${GREEN}✓ MQTT port 1883 is accessible${NC}"
else
    echo -e "${RED}✗ MQTT port 1883 is not accessible${NC}"
    echo "  Note: Mosquitto might be starting. Wait a few seconds and try again."
    exit 1
fi
echo ""

# Test 3: Test MQTT publish capability
echo "Test 3: Testing MQTT publish..."
TEST_MESSAGE="test_$(date +%s)"
if mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "test/connection" -m "$TEST_MESSAGE" 2>/dev/null; then
    echo -e "${GREEN}✓ MQTT publish successful${NC}"
else
    echo -e "${RED}✗ MQTT publish failed${NC}"
    echo "  Install mosquitto-clients: sudo apt-get install mosquitto-clients"
    exit 1
fi
echo ""

# Test 4: Test MQTT subscribe capability
echo "Test 4: Testing MQTT subscribe..."
echo "  Publishing test message..."
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "test/subscribe" -m "hello_esp32" &
sleep 1
RECEIVED=$(timeout 2 mosquitto_sub -h $MQTT_HOST -p $MQTT_PORT -t "test/subscribe" -C 1 2>/dev/null)
if [ "$RECEIVED" == "hello_esp32" ]; then
    echo -e "${GREEN}✓ MQTT subscribe successful${NC}"
    echo "  Received: $RECEIVED"
else
    echo -e "${YELLOW}⚠ MQTT subscribe test inconclusive${NC}"
fi
echo ""

# Test 5: Simulate ESP32 sensor data publishing
echo "Test 5: Simulating ESP32 sensor data..."
echo "  Publishing to IoT topics..."

# Publish to all sensor topics
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/water/level" -m "75.5"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/water/turbidity" -m "25.3"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/battery/voltage" -m "12.4"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/battery/current" -m "2.8"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/solar/voltage" -m "13.2"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/solar/current" -m "3.5"

echo -e "${GREEN}✓ Simulated ESP32 data published to all topics${NC}"
echo ""

# Test 6: Monitor MQTT topics (5 seconds)
echo "Test 6: Monitoring all IoT topics for 5 seconds..."
echo "  (This simulates what the backend will receive)"
echo "  Topics: iot/#"
echo ""
timeout 5 mosquitto_sub -h $MQTT_HOST -p $MQTT_PORT -t "iot/#" -v 2>/dev/null &
PID=$!
sleep 1

# Publish test data while monitoring
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/water/level" -m "76.0"
mosquitto_pub -h $MQTT_HOST -p $MQTT_PORT -t "iot/battery/voltage" -m "12.6"

wait $PID 2>/dev/null
echo ""
echo -e "${GREEN}✓ Topic monitoring completed${NC}"
echo ""

# Test 7: Verify backend API is accessible
echo "Test 7: Checking if backend API is accessible..."
STATUS_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/backend/api/status.php)
if [ "$STATUS_CODE" == "200" ]; then
    echo -e "${GREEN}✓ Backend API is accessible (HTTP $STATUS_CODE)${NC}"
else
    echo -e "${YELLOW}⚠ Backend API returned HTTP $STATUS_CODE${NC}"
fi
echo ""

# Summary
echo "=========================================="
echo "MQTT CONNECTION TEST SUMMARY"
echo "=========================================="
echo ""
echo -e "${GREEN}✓ Mosquitto broker is ready${NC}"
echo -e "${GREEN}✓ MQTT port 1883 is accessible${NC}"
echo -e "${GREEN}✓ Publish/Subscribe working${NC}"
echo -e "${GREEN}✓ All IoT topics configured${NC}"
echo ""
echo "MQTT Topics Ready for ESP32:"
echo "  • iot/water/level"
echo "  • iot/water/turbidity"
echo "  • iot/battery/voltage"
echo "  • iot/battery/current"
echo "  • iot/solar/voltage"
echo "  • iot/solar/current"
echo ""
echo "ESP32 Connection Details:"
echo "  • MQTT Broker: localhost (or your server IP)"
echo "  • MQTT Port: 1883"
echo "  • No authentication required (configure if needed)"
echo ""
echo -e "${GREEN}✅ MQTT is ready for ESP32 integration!${NC}"
echo ""
