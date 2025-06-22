#!/bin/sh
# Cross-platform entrypoint script for Docker
# This script runs inside the Docker container and initializes the application
# Exit on error (compatible with all POSIX shells)
set -e 2>/dev/null || echo "Note: Your shell doesn't support 'set -e', continuing without it"

# Always install Composer dependencies to ensure autoload_runtime.php is generated
echo "Installing Composer dependencies..."
composer install --no-interaction --no-progress

# Verify that autoload_runtime.php exists
if [ ! -f "vendor/autoload_runtime.php" ]; then
    echo "Error: autoload_runtime.php is still missing after composer install"
    echo "Trying to generate it explicitly..."
    composer require symfony/runtime --no-interaction --no-progress

    # Run Composer dump-autoload to ensure all autoload files are generated
    echo "Running composer dump-autoload..."
    composer dump-autoload --no-interaction --optimize

    # Check again
    if [ ! -f "vendor/autoload_runtime.php" ]; then
        echo "Fatal: Could not generate autoload_runtime.php"
        # Try to debug the issue
        echo "Debugging vendor directory:"
        ls -la vendor/
        echo "Checking for symfony/runtime package:"
        composer show symfony/runtime
        exit 1
    fi
fi

# Ensure the autoload_runtime.php file is accessible
echo "Ensuring autoload_runtime.php is accessible..."
if [ -f "vendor/autoload_runtime.php" ]; then
    echo "autoload_runtime.php exists at `pwd`/vendor/autoload_runtime.php"
    # Make sure it's readable
    chmod 644 vendor/autoload_runtime.php
else
    echo "Warning: autoload_runtime.php still not found after all attempts"
fi

# Copy Docker environment file if it doesn't exist
if [ ! -f .env.local ]; then
    echo "Creating .env.local from .env.docker"
    cp .env.docker .env.local
fi

# Wait for database to be ready
echo "Waiting for database to be ready..."
ATTEMPTS=0
while ! php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    # Use expr for arithmetic which is more widely supported
    ATTEMPTS=`expr $ATTEMPTS + 1`
    if [ $ATTEMPTS -gt 30 ]; then
        echo "Database is not available, giving up"
        exit 1
    fi
    echo "Database not ready yet, retrying in 10 seconds..."
    sleep 10
done
echo "Database is ready!"

# Run migrations
echo "Checking database status..."
# Check if a specific table exists
# Use a more compatible approach for command substitution
TABLE_EXISTS=`php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>/dev/null || echo "0"`

if [ "$TABLE_EXISTS" = "0" ]; then
    echo "Database is empty, running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction
else
    echo "Database already has tables, skipping migrations"
    # Mark migrations as executed
    php bin/console doctrine:migrations:version --add --all --no-interaction
fi

# Load fixtures if database is empty
if [ "$APP_ENV" = "dev" ]; then
    # Check if the user table exists
    TABLE_EXISTS=`php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>/dev/null || echo "0"`

    if [ "$TABLE_EXISTS" = "0" ]; then
        echo "Creating database schema..."
        php bin/console doctrine:schema:create --no-interaction

        echo "Loading fixtures..."
        php bin/console doctrine:fixtures:load --no-interaction
    else
        # Check if there are any users in the user table
        USER_RECORDS=`php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\"" 2>/dev/null || echo "0"`

        if [ "$USER_RECORDS" = "0" ]; then
            echo "User table exists but is empty, loading fixtures..."
            php bin/console doctrine:fixtures:load --no-interaction
        else
            echo "Database already has data, skipping fixtures"
        fi
    fi
fi

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear

# Fix permissions
echo "Fixing permissions..."
# Create var directory if it doesn't exist
if [ ! -d "var" ]; then
    mkdir -p var
fi

# Set permissions (this works in Linux containers regardless of host OS)
chmod -R 777 var/

echo "Application is ready!"

# Execute the passed command
exec "$@"
