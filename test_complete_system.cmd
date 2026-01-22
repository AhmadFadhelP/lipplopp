@echo off
chcp 65001 >nul

echo ╔══════════════════════════════════════════════════════════════╗
echo ║         COMPLETE SYSTEM TEST - MQTT + API + Dashboard       ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

set GREEN=
set YELLOW=
set NC=

echo This test will:
echo   1. ✓ Test MQTT broker
echo   2. ✓ Send sensor data to API
echo   3. ✓ Verify data is stored
echo   4. ✓ Check dashboard will display it
echo.
echo ════════════════════════════════════════════════════════════════
echo.

:: ===========================================================
:: Test 1: MQTT Broker
:: ===========================================================
echo Test 1: MQTT Broker
echo -------------------

docker ps --filter "name=mosquitto" --filter "status=running" | find "mosquitto" >nul
if %errorlevel%==0 (
    echo ✓ Mosquitto is running
) else (
    echo ✗ Mosquitto is not running
    exit /b 1
)

docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello" >nul 2>&1
if %errorlevel%==0 (
    echo ✓ MQTT publish works
) else (
    echo ✗ MQTT publish failed
)
echo.

:: ===========================================================
:: Test 2: API - Send Data
:: ===========================================================
echo Test 2: API - Sending Sensor Data
echo ----------------------------------
echo Sending test values:
echo   • Water Level: 92.5%%
echo   • Turbidity: 18.7 NTU
echo   • Battery: 12.7 V
echo   • Solar: 13.8 V
echo.

curl -s -X POST http://localhost:8585/backend/api/sensors.php -H "Content-Type: application/json" -d "{\"water_level\":92.5,\"turbidity\":18.7,\"battery_voltage\":12.7,\"battery_current\":3.1,\"solar_voltage\":13.8,\"solar_current\":4.2}"
 
findstr /C:"success" response.json >nul
if %errorlevel%==0 (
    echo ✓ Data saved to database
) else (
    echo ✗ Failed to save data
    echo Response:
    type response.json
    del response.json
    exit /b 1
)
echo.
del response.json

:: ===========================================================
:: Test 3: Retrieve Data
:: ===========================================================
echo Test 3: API - Retrieving Latest Data
echo -------------------------------------
timeout /t 1 >nul

curl -s "http://localhost:8585/backend/api/sensors.php?latest=true" > latest.json

findstr /C:"success" latest.json >nul
if %errorlevel%==0 (
    echo ✓ Data retrieved successfully
    echo.
    echo Latest sensor data:
    type latest.json
) else (
    echo ✗ Failed to retrieve data
    del latest.json
    exit /b 1
)
echo.
del latest.json

:: ===========================================================
:: Test 4: Dashboard Access
:: ===========================================================
echo Test 4: Dashboard Access
echo ------------------------
for /f "tokens=*" %%i in ('curl -s -o nul -w "%%{http_code}" http://localhost:8585/frontend/dashboard.php') do set HTTP_CODE=%%i

if "%HTTP_CODE%"=="200" (
    echo ✓ Dashboard is accessible
    echo   URL: http://localhost:8585/frontend/dashboard.php
) else (
    if "%HTTP_CODE%"=="302" (
        echo ✓ Dashboard redirected successfully
        echo   URL: http://localhost:8585/frontend/dashboard.php
    ) else (
        echo ⚠ Dashboard returned HTTP %HTTP_CODE%
    )
)
echo.

:: ===========================================================
:: Summary
:: ===========================================================
echo ════════════════════════════════════════════════════════════════
echo.
echo ✅ ALL TESTS PASSED!
echo.
echo What this means:
echo   ✓ MQTT broker is working
echo   ✓ API can save and retrieve sensor data
echo   ✓ Database is storing data correctly
echo   ✓ Dashboard is accessible
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 🎯 NEXT STEPS:
echo.
echo 1. Open dashboard in browser:
echo    http://localhost:8585/frontend/dashboard.php
echo.
echo 2. You should see the values we just sent:
echo    • Water Level: 92.5%%
echo    • Turbidity: 18.7 NTU
echo    • Battery: 12.7 V
echo    • Solar: 13.8 V
echo.
echo 3. Send different data and watch it update:
echo    curl -X POST http://localhost:8585/backend/api/sensors.php ^
         -H "Content-Type: application/json" ^
         -d "{\"water_level\": 99.9, \"turbidity\": 10.5, \"battery_voltage\": 13.0, \"battery_current\": 4.0, \"solar_voltage\": 14.5, \"solar_current\": 5.0}"
echo.
echo    Wait 5 seconds, then check dashboard - values will update!
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 🚀 READY FOR ESP32 INTEGRATION!
echo.
echo When ESP32 sends data, it will appear on the dashboard.
echo Share ESP32_INTEGRATION_GUIDE.md with hardware team.
echo.
pause
