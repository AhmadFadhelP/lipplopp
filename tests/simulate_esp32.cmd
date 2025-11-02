@echo off
REM ###########################################################################
REM ESP32 Sensor Data Simulator (Windows Version)
REM Purpose: Continuously simulate ESP32 sending sensor data via MQTT & API
REM ###########################################################################

setlocal ENABLEDELAYEDEXPANSION

echo ==========================================
echo ESP32 Sensor Data Simulator
echo ==========================================
echo.
echo Simulating sensor data to MQTT broker and API...
echo Press Ctrl+C to stop
echo ==========================================
echo.

REM ===========================
REM Configuration
REM ===========================
set MQTT_HOST=localhost
set MQTT_PORT=1883
set INTERVAL=5
set CONTAINER=mosquitto
set API_URL=http://localhost:8585/backend/api/sensors.php

REM ===========================
REM Random generator helper
REM ===========================
REM Using PowerShell for random floating values
for /l %%n in () do (
    REM Generate random sensor values
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 60 -Maximum 95"') do set water_level=%%a
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 15 -Maximum 45"') do set turbidity=%%a
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 11.5 -Maximum 12.8"') do set battery_voltage=%%a
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 1.5 -Maximum 4.5"') do set battery_current=%%a
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 12.0 -Maximum 14.5"') do set solar_voltage=%%a
    for /f "delims=" %%a in ('powershell -Command "Get-Random -Minimum 2.0 -Maximum 5.0"') do set solar_current=%%a

    echo [%time%] Sending data...
    echo   Water Level:     !water_level! %%
    echo   Turbidity:       !turbidity! NTU
    echo   Battery Voltage: !battery_voltage! V
    echo   Battery Current: !battery_current! A
    echo   Solar Voltage:   !solar_voltage! V
    echo   Solar Current:   !solar_current! A

    REM ===========================
    REM Publish to MQTT via Docker Mosquitto
    REM ===========================
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/water/level" -m "!water_level!" -q 1
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/water/turbidity" -m "!turbidity!" -q 1
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/battery/voltage" -m "!battery_voltage!" -q 1
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/battery/current" -m "!battery_current!" -q 1
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/solar/voltage" -m "!solar_voltage!" -q 1
    docker exec %CONTAINER% mosquitto_pub -h %MQTT_HOST% -p %MQTT_PORT% -t "iot/solar/current" -m "!solar_current!" -q 1

    REM ===========================
    REM Send to API
    REM ===========================
    powershell -Command ^
      "$body = @{water_level=!water_level!; turbidity=!turbidity!; battery_voltage=!battery_voltage!; battery_current=!battery_current!; solar_voltage=!solar_voltage!; solar_current=!solar_current!} | ConvertTo-Json; Invoke-RestMethod -Uri '%API_URL%' -Method POST -Body $body -ContentType 'application/json' | Out-Null"

    echo   ✓ Data published to MQTT and API
    echo ------------------------------------------
    timeout /t %INTERVAL% >nul
)

endlocal
