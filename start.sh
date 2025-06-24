#!/bin/bash

# Make sure the script is executable
if [ ! -x "$0" ]; then
    echo "Making script executable..."
    chmod +x "$0"
fi

# Make sure docker-entrypoint.sh is executable
if [ ! -x "docker-entrypoint.sh" ]; then
    echo "Making docker-entrypoint.sh executable..."
    chmod +x docker-entrypoint.sh
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
if [ "$($DOCKER_COMPOSE -f docker-compose.yml ps -q | wc -l)" -lt 3 ]; then
    echo "Error: Not all containers are running. Please check the logs with 'docker-compose logs'."
    exit 1
fi

echo "Application is ready!"
echo "You can access the application at: http://localhost:8080"
echo "Login with the following credentials:"
echo "  - Admin: admin@example.com / password"
echo "  - User: user@example.com / password"
echo ""
echo "To stop the application, run: $DOCKER_COMPOSE -f docker-compose.yml down"
