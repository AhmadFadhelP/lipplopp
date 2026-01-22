# Project Workflow

This document outlines the development, testing, and deployment workflow for the IoT Fish Feeder web application.

## Project Overview

The project is a web application for monitoring and controlling an IoT-based fish feeding system. It is built with PHP and uses a MySQL database. The entire application is containerized using Docker.

The application consists of three main services defined in `docker-compose.yml`:
- `app`: The PHP web application.
- `db`: The MySQL database.
- `mosquitto`: An MQTT broker for communication with IoT devices.

## Development Workflow

1.  **Prerequisites:**
    *   Docker
    *   Docker Compose

2.  **Running the Application Locally:**
    *   Clone the repository.
    *   Run `docker-compose up -d` to start the application in detached mode.
    *   The web application will be accessible at `http://localhost:8080`.
    *   The MQTT broker will be accessible on port `1883`.

3.  **File Structure:**
    *   `frontend/`: Contains the user-facing PHP files (dashboard, etc.).
    *   `backend/`: Contains the backend logic (login, register, etc.).
    *   `database/`: Contains the database initialization script (`init.sql`).
    *   `assets/`: Contains static assets like CSS, JavaScript, and images.
    *   `tests/`: Contains tests for the application.

4.  **Database:**
    *   The database is named `db_tugasakhir`.
    *   The schema is defined in `database/init.sql` and is automatically imported when the `db` service is started for the first time.

## Testing

*   The `tests/` directory contains PHP-based tests.
*   `run_test.php` is likely the main script for running the tests. To execute the tests, you can run the following command:
    ```bash
    docker-compose exec app php tests/run_test.php
    ```

## Deployment

The `what_should_be_improved.md` file provides detailed instructions for deploying the application on a Virtual Private Server (VPS). The general steps are:

1.  **Prerequisites:**
    *   A VPS with Docker and Docker Compose installed.
    *   A domain name pointed to the server's IP address.

2.  **Deployment Steps:**
    1.  Clone the project repository onto the VPS.
    2.  Set up a reverse proxy with Nginx to forward requests to the Docker container.
    3.  Enable HTTPS using Let's Encrypt and Certbot.
    4.  Run `docker-compose up -d --build` to start the application.

For more detailed instructions, refer to the `what_should_be_improved.md` file.
