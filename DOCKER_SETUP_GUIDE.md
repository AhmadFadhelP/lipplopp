# Docker Setup Guide - IoT Monitoring System

Complete guide to setup and run the IoT Water Monitoring System using Docker on Windows and Linux.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Windows Setup](#windows-setup)
3. [Linux Setup](#linux-setup)
4. [Starting the Application](#starting-the-application)
5. [Verifying Installation](#verifying-installation)
6. [Common Commands](#common-commands)
7. [Troubleshooting](#troubleshooting)
8. [Project Structure](#project-structure)

---

## Prerequisites

### What You Need

- **Docker**: Container platform
- **Docker Compose**: Multi-container orchestration
- **Git**: Version control (optional, for cloning)
- **Terminal/Command Prompt**: To run commands
- **Web Browser**: To access the dashboard

### Minimum System Requirements

| Component | Requirement |
|-----------|-------------|
| RAM | 4 GB minimum, 8 GB recommended |
| Disk Space | 2 GB free space |
| OS | Windows 10/11 or Linux (Ubuntu, Debian, etc.) |
| Network | Internet connection for initial setup |

---

## Windows Setup

### Step 1: Install Docker Desktop

1. **Download Docker Desktop**
   - Go to: https://www.docker.com/products/docker-desktop
   - Click "Download for Windows"
   - Download the installer (Docker Desktop Installer.exe)

2. **Install Docker Desktop**
   ```
   - Double-click Docker Desktop Installer.exe
   - Follow the installation wizard
   - Enable WSL 2 if prompted (recommended)
   - Restart computer when installation completes
   ```

3. **Verify Docker Installation**
   - Open PowerShell or Command Prompt as Administrator
   ```powershell
   docker --version
   docker-compose --version
   ```
   - You should see version numbers for both commands

4. **Start Docker Desktop**
   - Launch Docker Desktop from Start Menu
   - Wait for Docker to start (whale icon in system tray)
   - Ensure Docker is running (green indicator)

### Step 2: Clone or Download Project

**Option A: Using Git**
```powershell
cd C:\Users\YourUsername\Documents
git clone <repository-url>
cd lipplopp
```

**Option B: Download ZIP**
- Download the project ZIP file
- Extract to a folder (e.g., `C:\Users\YourUsername\Documents\lipplopp`)
- Open PowerShell in that folder:
  ```powershell
  cd C:\Users\YourUsername\Documents\lipplopp
  ```

### Step 3: Configure Environment (Windows)

1. **Check Docker Compose File**
   - Ensure `docker-compose.yml` exists in project root
   - No modifications needed for basic setup

2. **Check Mosquitto Configuration**
   - The `mosquitto/` folder should contain configuration files
   - Configuration is pre-configured for the project

### Step 4: Start the Application (Windows)

```powershell
# Navigate to project directory
cd C:\Users\YourUsername\Documents\lipplopp

# Start all containers
docker-compose up -d

# Wait for containers to start (about 30 seconds)
# Check status
docker-compose ps
```

Expected output:
```
NAME                  COMMAND                  SERVICE   STATUS    PORTS
lipplopp-app-1        "docker-php-entrypoi…"   app       running   0.0.0.0:8080->80/tcp
lipplopp-db-1         "docker-entrypoint.s…"   db        running   3306/tcp
mosquitto             "/docker-entrypoint.…"   mosquitto running   0.0.0.0:1883->1883/tcp
```

---

## Linux Setup

### Step 1: Install Docker and Docker Compose

#### For Ubuntu/Debian

```bash
# Update package index
sudo apt update

# Install required packages
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Install Docker Compose standalone (if not included)
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add your user to docker group (to run without sudo)
sudo usermod -aG docker $USER

# Apply group changes (logout/login or run)
newgrp docker

# Verify installation
docker --version
docker-compose --version
```

#### For CentOS/RHEL/Fedora

```bash
# Install Docker
sudo dnf install -y docker docker-compose

# Start and enable Docker
sudo systemctl start docker
sudo systemctl enable docker

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker-compose --version
```

### Step 2: Clone or Download Project (Linux)

```bash
# Navigate to home directory
cd ~

# Option A: Clone with Git
git clone <repository-url>
cd lipplopp

# Option B: If you have ZIP file
unzip lipplopp.zip
cd lipplopp
```

### Step 3: Set Permissions (Linux)

```bash
# Make sure Docker has access to project files
chmod -R 755 .

# Ensure test scripts are executable
chmod +x test_complete_system.sh
chmod +x tests/*.sh
```

### Step 4: Start the Application (Linux)

```bash
# Navigate to project directory
cd ~/lipplopp

# Start all containers
docker-compose up -d

# Wait for containers to start
sleep 10

# Check status
docker-compose ps
```

---

## Starting the Application

### First Time Setup

After running `docker-compose up -d`, the system will:

1. **Download Docker images** (~500MB, one-time download)
   - PHP with Apache
   - MySQL database
   - Mosquitto MQTT broker

2. **Initialize database** (automatic)
   - Create tables
   - Setup initial schema

3. **Start services**
   - Web application on port 8080
   - MQTT broker on port 1883
   - Database on internal network

This process takes 1-3 minutes depending on your internet connection.

### Accessing the Dashboard

1. **Open Web Browser**
   - Navigate to: `http://localhost:8080/frontend/index.php`

2. **Register New Account**
   - Click "Daftar" (Register)
   - Fill in:
     - Username: `admin`
     - Password: `password123`
     - Email: `admin@example.com`
   - Click "Daftar"

3. **Login**
   - Username: `admin`
   - Password: `password123`
   - Click "Login"

4. **View Dashboard**
   - You'll be redirected to: `http://localhost:8080/frontend/dashboard.php`
   - Initially shows placeholder data
   - Updates automatically when ESP32 sends data

---

## Verifying Installation

### Run System Test

**Windows (PowerShell):**
```powershell
bash test_complete_system.sh
```

**Linux:**
```bash
bash test_complete_system.sh
```

Expected output:
```
✓ MQTT broker is running
✓ MQTT publish works
✓ Data saved to database
✓ Data retrieved successfully
✓ Dashboard is accessible
✅ ALL TESTS PASSED!
```

### Manual Verification

1. **Check Containers are Running**
   ```bash
   docker-compose ps
   ```
   All 3 containers should show "running"

2. **Check Application Logs**
   ```bash
   # View all logs
   docker-compose logs

   # View specific service logs
   docker-compose logs app
   docker-compose logs db
   docker-compose logs mosquitto
   ```

3. **Test MQTT Broker**
   ```bash
   # Linux
   docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello"

   # Windows PowerShell
   docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello"
   ```

4. **Test API Endpoint**
   ```bash
   # Linux/Mac
   curl http://localhost:8080/backend/api/sensors.php?latest=true

   # Windows PowerShell
   Invoke-WebRequest -Uri "http://localhost:8080/backend/api/sensors.php?latest=true"
   ```

---

## Common Commands

### Container Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Restart all services
docker-compose restart

# Stop all services and remove volumes (⚠️ deletes database)
docker-compose down -v

# View container status
docker-compose ps

# View logs
docker-compose logs -f

# View logs for specific service
docker-compose logs -f app
docker-compose logs -f mosquitto
```

### Database Management

```bash
# Access MySQL command line
docker-compose exec db mysql -u iot_user -piot_password iot_monitoring

# Backup database
docker-compose exec db mysqldump -u iot_user -piot_password iot_monitoring > backup.sql

# Restore database
docker-compose exec -T db mysql -u iot_user -piot_password iot_monitoring < backup.sql

# View database tables
docker-compose exec db mysql -u iot_user -piot_password iot_monitoring -e "SHOW TABLES;"
```

### MQTT Testing

```bash
# Subscribe to all topics (monitor incoming data)
docker exec mosquitto mosquitto_sub -h localhost -t "iot/#" -v

# Publish test data
docker exec mosquitto mosquitto_pub -h localhost -t "iot/water/level" -m "85.5"
```

### Debugging

```bash
# Enter container shell
docker-compose exec app bash
docker-compose exec db bash

# View container resource usage
docker stats

# Inspect container details
docker inspect <container-name>

# Remove all stopped containers
docker container prune

# Remove all unused images
docker image prune -a
```

---

## Troubleshooting

### Issue: Port Already in Use

**Error**: `Bind for 0.0.0.0:8080 failed: port is already allocated`

**Solution**:
```bash
# Find what's using the port
# Linux
sudo lsof -i :8080

# Windows
netstat -ano | findstr :8080

# Option 1: Stop the other service
# Option 2: Change port in docker-compose.yml
ports:
  - "8081:80"  # Change 8080 to 8081
```

### Issue: Containers Not Starting

**Error**: Container exits immediately

**Solution**:
```bash
# Check logs for errors
docker-compose logs

# Recreate containers
docker-compose down
docker-compose up -d

# Check Docker is running
docker info
```

### Issue: Database Connection Failed

**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**:
```bash
# Wait for database to fully start (takes 30-60 seconds)
docker-compose logs db

# Restart database container
docker-compose restart db

# Verify database is healthy
docker-compose exec db mysqladmin ping -h localhost -u iot_user -piot_password
```

### Issue: Permission Denied (Linux)

**Error**: `Got permission denied while trying to connect to the Docker daemon socket`

**Solution**:
```bash
# Add user to docker group
sudo usermod -aG docker $USER

# Logout and login, or run
newgrp docker

# Restart Docker service
sudo systemctl restart docker
```

### Issue: Cannot Access Dashboard

**Error**: Browser shows "Can't reach this page"

**Solution**:
```bash
# 1. Verify container is running
docker-compose ps

# 2. Check if port is accessible
curl http://localhost:8080

# 3. Check firewall (Linux)
sudo ufw status
sudo ufw allow 8080/tcp

# 4. Check Windows Firewall
# Windows Security → Firewall → Allow app → Docker Desktop

# 5. Try different browser or clear cache
```

### Issue: MQTT Not Receiving Data

**Error**: ESP32 cannot connect or data not appearing

**Solution**:
```bash
# 1. Verify Mosquitto is running
docker-compose ps mosquitto

# 2. Test MQTT locally
docker exec mosquitto mosquitto_pub -h localhost -t "test" -m "hello"
docker exec mosquitto mosquitto_sub -h localhost -t "test" -v

# 3. Check MQTT logs
docker-compose logs mosquitto

# 4. Verify port 1883 is accessible
# Linux
nc -zv localhost 1883

# Windows
Test-NetConnection -ComputerName localhost -Port 1883

# 5. Check firewall allows port 1883
```

### Issue: Slow Performance

**Solution**:
```bash
# 1. Check Docker resource usage
docker stats

# 2. Increase Docker resources (Docker Desktop)
# Settings → Resources → Increase Memory/CPU

# 3. Clean up unused resources
docker system prune -a

# 4. Optimize database
docker-compose exec db mysqlcheck -u iot_user -piot_password --optimize --all-databases
```

---

## Project Structure

```
lipplopp/
├── docker-compose.yml          # Docker orchestration config
├── Dockerfile                   # Application container definition
├── backend/
│   ├── api/                    # REST API endpoints
│   │   ├── sensors.php         # Sensor data CRUD
│   │   ├── mqtt.php            # MQTT integration
│   │   └── config.php          # API configuration
│   ├── login.php               # User authentication
│   ├── register.php            # User registration
│   └── logout.php              # Logout handler
├── frontend/
│   ├── index.php               # Login page
│   ├── dashboard.php           # Main dashboard
│   ├── pembukaan_pakan.php     # Feed control
│   └── riwayat_pakan.php       # Feed history
├── database/
│   └── init.sql                # Database schema
├── mosquitto/
│   ├── config/
│   │   └── mosquitto.conf      # MQTT broker config
│   └── data/                   # MQTT persistence data
├── tests/
│   ├── mqtt_connection_test.sh # Test MQTT connectivity
│   ├── mqtt_monitor.sh         # Monitor MQTT topics
│   └── simulate_esp32.sh       # Simulate ESP32 data
├── test_complete_system.sh     # Full system test
├── DOCKER_SETUP_GUIDE.md       # This file
└── ESP32_INTEGRATION_GUIDE.md  # Hardware integration guide
```

---

## Accessing from Other Devices

### On Local Network

To access dashboard from phone/tablet/other computer on same network:

1. **Find Server IP Address**

   **Linux:**
   ```bash
   hostname -I
   # or
   ip addr show
   ```

   **Windows:**
   ```powershell
   ipconfig
   # Look for IPv4 Address
   ```

2. **Configure Firewall**

   **Linux (Ubuntu):**
   ```bash
   sudo ufw allow 8080/tcp
   sudo ufw allow 1883/tcp
   ```

   **Windows:**
   - Windows Security → Firewall & network protection
   - Advanced settings → Inbound Rules
   - New Rule → Port → TCP → 8080, 1883 → Allow

3. **Access Dashboard**
   - From other device: `http://SERVER_IP:8080/frontend/index.php`
   - Example: `http://192.168.1.100:8080/frontend/index.php`

### For ESP32 Connection

Update ESP32 code with server IP:
```cpp
const char* mqtt_server = "192.168.1.100";  // Your server IP
```

---

## Updating the Application

### Pull Latest Changes

```bash
# Stop containers
docker-compose down

# Update code (if using Git)
git pull

# Rebuild containers
docker-compose build

# Start updated containers
docker-compose up -d
```

### Update Docker Images

```bash
# Pull latest images
docker-compose pull

# Restart with new images
docker-compose up -d
```

---

## Backup and Restore

### Backup Everything

**Linux:**
```bash
# Create backup directory
mkdir -p backups/$(date +%Y%m%d)

# Backup database
docker-compose exec -T db mysqldump -u iot_user -piot_password iot_monitoring > backups/$(date +%Y%m%d)/database.sql

# Backup MQTT data
cp -r mosquitto/data backups/$(date +%Y%m%d)/mqtt_data

# Backup application files (if modified)
tar -czf backups/$(date +%Y%m%d)/app_files.tar.gz backend/ frontend/
```

**Windows (PowerShell):**
```powershell
# Create backup directory
$date = Get-Date -Format "yyyyMMdd"
New-Item -ItemType Directory -Path "backups\$date"

# Backup database
docker-compose exec -T db mysqldump -u iot_user -piot_password iot_monitoring > "backups\$date\database.sql"

# Backup MQTT data
Copy-Item -Recurse mosquitto\data "backups\$date\mqtt_data"
```

### Restore from Backup

```bash
# Restore database
docker-compose exec -T db mysql -u iot_user -piot_password iot_monitoring < backups/20250102/database.sql

# Restore MQTT data
docker-compose down
cp -r backups/20250102/mqtt_data mosquitto/data
docker-compose up -d
```

---

## Production Deployment

### Security Recommendations

1. **Change Default Passwords**
   - Update database credentials in `docker-compose.yml`
   - Use strong passwords

2. **Enable HTTPS**
   - Add SSL certificate
   - Configure reverse proxy (nginx/Apache)

3. **Secure MQTT**
   - Enable authentication in `mosquitto.conf`
   - Use TLS/SSL for MQTT (port 8883)

4. **Environment Variables**
   - Move sensitive data to `.env` file
   - Don't commit `.env` to version control

5. **Firewall Configuration**
   - Only expose necessary ports
   - Use VPN for remote access

6. **Regular Updates**
   - Keep Docker images updated
   - Monitor security advisories

### Performance Tuning

```yaml
# docker-compose.yml additions for production
services:
  db:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G
```

---

## Support & Next Steps

### Getting Help

1. Check logs: `docker-compose logs`
2. Review troubleshooting section above
3. Check Docker documentation: https://docs.docker.com
4. Verify system requirements

### Next Steps

1. ✅ Docker setup complete
2. ✅ System test passed
3. ✅ Dashboard accessible
4. 📖 Read `ESP32_INTEGRATION_GUIDE.md` for hardware integration
5. 🚀 Start sending data from ESP32

---

## Quick Reference

### Essential Commands

```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Logs
docker-compose logs -f

# Status
docker-compose ps

# Test
bash test_complete_system.sh
```

### Access Points

- **Dashboard**: http://localhost:8080/frontend/dashboard.php
- **API**: http://localhost:8080/backend/api/sensors.php
- **MQTT**: localhost:1883
- **Database**: localhost:3306 (internal only)

### Default Credentials

- **Web Login**: Create your own during registration
- **Database**: iot_user / iot_password (configured in docker-compose.yml)
- **MQTT**: No authentication (can be enabled)

---

## Success!

If you can access the dashboard and the system test passes, your Docker setup is complete! 🎉

The system is now ready to receive data from ESP32 devices.

**Next**: Read the ESP32 Integration Guide to connect your hardware.

---

*Last Updated: 2025-11-02*
