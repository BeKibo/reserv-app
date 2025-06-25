# PowerShell script for starting the application on Windows
# This script provides the same functionality as start.sh but is compatible with Windows

# Check if Docker is installed
if (-not (Get-Command "docker" -ErrorAction SilentlyContinue)) {
    Write-Host "Docker is not installed. Please install Docker and try again."
    exit 1
}

# Check if Docker Compose is available
$dockerComposeAvailable = $false
if (Get-Command "docker-compose" -ErrorAction SilentlyContinue) {
    $DOCKER_COMPOSE = "docker-compose"
    $dockerComposeAvailable = $true
} elseif (docker compose version 2>&1) {
    $DOCKER_COMPOSE = "docker compose"
    $dockerComposeAvailable = $true
}

if (-not $dockerComposeAvailable) {
    Write-Host "Docker Compose is not installed. Please install Docker Compose and try again."
    exit 1
}

Write-Host "Starting the application..."

# Stop existing containers
Invoke-Expression "$DOCKER_COMPOSE -f docker-compose.yml down"

# Start the containers
Invoke-Expression "$DOCKER_COMPOSE -f docker-compose.yml up -d"

# Wait for the application to be ready
Write-Host "Waiting for the application to be ready..."
Start-Sleep -Seconds 10

# Check if the containers are running
Write-Host "Checking container status..."
# Check each container individually to provide better error messages
$appRunning = (Invoke-Expression "$DOCKER_COMPOSE -f docker-compose.yml ps -q app" | Measure-Object -Line).Lines
$dbRunning = (Invoke-Expression "$DOCKER_COMPOSE -f docker-compose.yml ps -q database" | Measure-Object -Line).Lines
$mailerRunning = (Invoke-Expression "$DOCKER_COMPOSE -f docker-compose.yml ps -q mailer" | Measure-Object -Line).Lines

if ($appRunning -lt 1) {
    Write-Host "Error: App container is not running. Please check the logs with '$DOCKER_COMPOSE logs app'."
    exit 1
}

if ($dbRunning -lt 1) {
    Write-Host "Error: Database container is not running. Please check the logs with '$DOCKER_COMPOSE logs database'."
    exit 1
}

if ($mailerRunning -lt 1) {
    Write-Host "Error: Mailer container is not running. Please check the logs with '$DOCKER_COMPOSE logs mailer'."
    exit 1
}

Write-Host "Application is ready!"
Write-Host "You can access the application at: http://localhost:8080"
Write-Host "Login with the following credentials:"
Write-Host "  - Admin: admin@example.com / password"
Write-Host "  - User: user@example.com / password"
Write-Host ""
Write-Host "To stop the application, run: $DOCKER_COMPOSE -f docker-compose.yml down"
