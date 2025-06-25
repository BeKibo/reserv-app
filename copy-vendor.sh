#!/bin/bash
# Script to copy the vendor directory from the host to the container
# This script should be run after the container is started

# Check if the container is running
if ! docker ps | grep -q reserv-app; then
    echo "Error: Container reserv-app is not running"
    exit 1
fi

# Check if the vendor directory exists on the host
if [ ! -d "vendor" ]; then
    echo "Error: Vendor directory not found on host"
    exit 1
fi

# Check if the autoload_runtime.php file exists on the host
if [ ! -f "vendor/autoload_runtime.php" ]; then
    echo "Warning: autoload_runtime.php not found on host, running composer install"
    composer install --no-interaction --no-progress

    if [ ! -f "vendor/autoload_runtime.php" ]; then
        echo "Error: Failed to generate autoload_runtime.php on host"
        exit 1
    fi
fi

echo "Copying vendor directory to container..."
# Create a tar archive of the vendor directory
tar -cf vendor.tar vendor

# Copy the tar archive to the container
docker cp vendor.tar reserv-app:/app/

# Extract the tar archive in the container
docker exec reserv-app bash -c "cd /app && tar -xf vendor.tar && rm vendor.tar"

# Verify that the autoload_runtime.php file exists in the container
if docker exec reserv-app test -f /app/vendor/autoload_runtime.php; then
    echo "Success: autoload_runtime.php found in container"
else
    echo "Error: autoload_runtime.php not found in container after copy"
    exit 1
fi

echo "Vendor directory successfully copied to container"
