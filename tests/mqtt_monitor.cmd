@echo off
REM ===========================================================
REM MQTT Topic Monitor (Docker version for Windows)
REM ===========================================================

set CONTAINER_NAME=mosquitto

echo ==========================================
echo MQTT Topic Monitor via Docker
echo ==========================================
echo Monitoring all IoT topics: iot/#
echo.
echo Press Ctrl+C to stop
echo.
docker exec -it %CONTAINER_NAME% mosquitto_sub -h localhost -p 1883 -t "iot/#" -v
