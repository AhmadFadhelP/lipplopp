# Docker Tutorial for Project Setup

This tutorial will guide you through setting up and running the project using Docker and Docker Compose on both Linux/macOS and Windows.

## Introduction to Docker

Docker is a platform that allows you to develop, deploy, and run applications in containers. Containers are lightweight, portable, and self-sufficient units that package an application and all its dependencies, ensuring it runs consistently across different environments. Docker Compose is a tool for defining and running multi-container Docker applications.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

*   **Docker:**
    *   **Linux:** Follow the official Docker installation guide for your distribution: https://docs.docker.com/engine/install/
    *   **macOS & Windows:** Install Docker Desktop: https://www.docker.com/products/docker-desktop

*   **Docker Compose:** Docker Desktop for Windows and macOS includes Docker Compose. For Linux, it's usually installed alongside Docker Engine or as a separate package. Verify its installation by running `docker-compose --version`.

## Starting the Project

### For Linux/macOS Users

1.  **Navigate to the project directory:**
    Open your terminal and change your current directory to the root of this project where the `docker-compose.yml` file is located.
    ```bash
    cd /path/to/your/project
    ```

2.  **Start the services:**
    Run the following command to build the Docker images (if not already built) and start all the services defined in `docker-compose.yml` in detached mode (in the background).
    ```bash
    docker-compose up -d --build
    ```
    *   `up`: Starts the services.
    *   `-d`: Runs containers in detached mode.
    *   `--build`: Rebuilds images before starting containers (useful after code changes).

3.  **Access the application:**
    Once the services are up and running, you can access the application in your web browser. By default, it should be available at:
    ```
    http://localhost:8080
    ```

### For Windows Users

1.  **Open PowerShell or Command Prompt:**
    Navigate to the project directory using PowerShell or Command Prompt.
    ```cmd
    cd C:\path\to\your\project
    ```

2.  **Start Docker Desktop:**
    Ensure Docker Desktop is running on your Windows machine.

3.  **Start the services:**
    Run the same command as for Linux/macOS to build and start the services:
    ```cmd
    docker-compose up -d --build
    ```

4.  **Access the application:**
    The application will be available in your web browser at:
    ```
    http://localhost:8080
    ```

## Stopping the Project

### For Linux/macOS Users

To stop and remove the containers, networks, and volumes created by `docker-compose up`, run the following command from the project directory:

```bash
    docker-compose down
    ```

### For Windows Users

Similarly, to stop and remove the services, run the following command from the project directory:

```cmd
    docker-compose down
    ```

## Troubleshooting

*   **"Address already in use" error:** If you encounter an error indicating that a port (e.g., 8080) is already in use, it means another application on your system is using that port. You can either stop the conflicting application or change the port mapping in your `docker-compose.yml` file.
*   **Database not starting/tables not found:** If you previously ran the project and the database volume (`db_data`) was created without the necessary tables, you might need to remove the volume to force a re-initialization. First, stop the project (`docker-compose down`), then remove the volume:
    ```bash
    docker volume rm <your-project-name>_db_data
    ```
    (Replace `<your-project-name>` with the actual name, usually the directory name where `docker-compose.yml` is located, e.g., `lipplopp_db_data`). After removing the volume, start the project again with `docker-compose up -d --build`.
*   **Container logs:** If a service isn't starting correctly, check its logs for errors:
    ```bash
    docker logs <container-name>
    ```
    (e.g., `docker logs lipplopp-app-1` or `docker logs lipplopp-db-1`)
