#!/bin/bash
# Cross-platform script for starting the application
# For Windows users: It's recommended to use Git Bash or WSL to run this script
# Alternatively, you can use the start.ps1 script if you prefer PowerShell

# Make the scripts executable (this will be skipped on Windows)
if [ "$(uname)" != "MINGW"* ] && [ "$(uname)" != "CYGWIN"* ] && [ "$(uname)" != "MSYS"* ]; then
    if [ ! -x "$0" ]; then
        echo "Making script executable..."
        chmod +x "$0"
    fi

    if [ ! -x "docker-entrypoint.sh" ]; then
        echo "Making docker-entrypoint.sh executable..."
        chmod +x docker-entrypoint.sh
    fi
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install Docker and try again."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null && ! command -v docker compose &> /dev/null; then
    echo "Docker Compose is not installed. Please install Docker Compose and try again."
    exit 1
fi

echo "Starting the application..."

# Determine which Docker Compose command to use
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    DOCKER_COMPOSE="docker compose"
fi

# Start the containers
$DOCKER_COMPOSE -f docker-compose.yml down
$DOCKER_COMPOSE -f docker-compose.yml up -d

# Wait for the application to be ready
echo "Waiting for the application to be ready..."
sleep 10

# Check if the containers are running
echo "Checking container status..."
# Check each container individually to provide better error messages
APP_RUNNING=$($DOCKER_COMPOSE -f docker-compose.yml ps -q app | wc -l)
DB_RUNNING=$($DOCKER_COMPOSE -f docker-compose.yml ps -q database | wc -l)
MAILER_RUNNING=$($DOCKER_COMPOSE -f docker-compose.yml ps -q mailer | wc -l)

if [ "$APP_RUNNING" -lt 1 ]; then
    echo "Error: App container is not running. Please check the logs with '$DOCKER_COMPOSE logs app'."
    exit 1
fi

if [ "$DB_RUNNING" -lt 1 ]; then
    echo "Error: Database container is not running. Please check the logs with '$DOCKER_COMPOSE logs database'."
    exit 1
fi

if [ "$MAILER_RUNNING" -lt 1 ]; then
    echo "Error: Mailer container is not running. Please check the logs with '$DOCKER_COMPOSE logs mailer'."
    exit 1
fi

echo "Application is ready!"
echo "You can access the application at: http://localhost:8080"
echo "Login with the following credentials:"
echo "  - Admin: admin@example.com / password"
echo "  - User: user@example.com / password"
echo ""
echo "To stop the application, run: $DOCKER_COMPOSE -f docker-compose.yml down"
