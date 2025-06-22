# PowerShell script to copy the vendor directory from the host to the container
# This script should be run after the container is started

# Check if the container is running
if (-not (docker ps | Select-String -Pattern "reserv-app")) {
    Write-Host "Error: Container reserv-app is not running"
    exit 1
}

# Check if the vendor directory exists on the host
if (-not (Test-Path "vendor")) {
    Write-Host "Error: Vendor directory not found on host"
    exit 1
}

# Check if the autoload_runtime.php file exists on the host
if (-not (Test-Path "vendor/autoload_runtime.php")) {
    Write-Host "Warning: autoload_runtime.php not found on host, running composer install"
    composer install --no-interaction --no-progress

    if (-not (Test-Path "vendor/autoload_runtime.php")) {
        Write-Host "Error: Failed to generate autoload_runtime.php on host"
        exit 1
    }
}

Write-Host "Copying vendor directory to container..."
# Create a temporary directory
$tempDir = [System.IO.Path]::GetTempPath() + [System.Guid]::NewGuid().ToString()
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Create a zip archive of the vendor directory
$zipFile = "$tempDir\vendor.zip"
Compress-Archive -Path "vendor" -DestinationPath $zipFile

# Copy the zip archive to the container
docker cp $zipFile reserv-app:/app/vendor.zip

# Extract the zip archive in the container
docker exec reserv-app bash -c "cd /app && apt-get update && apt-get install -y unzip && unzip -o vendor.zip && rm vendor.zip"

# Verify that the autoload_runtime.php file exists in the container
$result = docker exec reserv-app bash -c "test -f /app/vendor/autoload_runtime.php && echo 'success' || echo 'failure'"
if ($result -eq "success") {
    Write-Host "Success: autoload_runtime.php found in container"
} else {
    Write-Host "Error: autoload_runtime.php not found in container after copy"
    exit 1
}

# Clean up
Remove-Item -Path $tempDir -Recurse -Force

Write-Host "Vendor directory successfully copied to container"
