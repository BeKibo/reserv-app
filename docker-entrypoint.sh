#!/bin/sh
# Cross-platform entrypoint script for Docker
# This script runs inside the Docker container and initializes the application
# Simple error handling that works in minimal shells
# Ensure this file has LF line endings (not CRLF) when used on Windows
# This script is designed to be compatible with minimal POSIX shells like dash or busybox sh

# Define functions using the most basic POSIX-compatible syntax
log_message() {
    echo "$(date) - $1"
}
handle_error() {
    echo "ERROR: $1"
    exit 1
}

# Install Composer dependencies if they don't exist
if [ ! -d "vendor" ]; then
    log_message "Installing Composer dependencies..."
    composer install --no-interaction --no-progress
fi

# Copy Docker environment file if it doesn't exist
if [ ! -f .env.local ]; then
    log_message "Creating .env.local from .env.docker"
    cp .env.docker .env.local
fi

# Wait for database to be ready
log_message "Waiting for database to be ready..."
ATTEMPTS=0
while ! php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    ATTEMPTS=$(expr $ATTEMPTS + 1)
    if [ $ATTEMPTS -gt 30 ]; then
        handle_error "Database is not available after 30 attempts, giving up"
    fi
    log_message "Database not ready yet, retrying in 10 seconds... (Attempt $ATTEMPTS/30)"
    sleep 10
done
log_message "Database is ready!"

# Run migrations
log_message "Checking database status..."
# Check if a specific table exists
TABLE_EXISTS=$(php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>/dev/null || echo "0")

if [ "$TABLE_EXISTS" = "0" ]; then
    log_message "Database is empty, running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction || handle_error "Failed to run migrations"
else
    log_message "Database already has tables, skipping migrations"
    # Mark migrations as executed
    php bin/console doctrine:migrations:version --add --all --no-interaction || log_message "Warning: Failed to mark migrations as executed, but continuing anyway"
fi

# Load fixtures if database is empty
if [ "$APP_ENV" = "dev" ]; then
    # Check if the user table exists
    TABLE_EXISTS=$(php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>/dev/null || echo "0")

    if [ "$TABLE_EXISTS" = "0" ]; then
        log_message "Creating database schema..."
        php bin/console doctrine:schema:create --no-interaction || handle_error "Failed to create database schema"

        log_message "Loading fixtures..."
        php bin/console doctrine:fixtures:load --no-interaction || handle_error "Failed to load fixtures"
    else
        # Check if there are any users in the user table
        USER_RECORDS=$(php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\"" 2>/dev/null || echo "0")

        if [ "$USER_RECORDS" = "0" ]; then
            log_message "User table exists but is empty, loading fixtures..."
            php bin/console doctrine:fixtures:load --no-interaction || handle_error "Failed to load fixtures"
        else
            log_message "Database already has data, skipping fixtures"
        fi
    fi
fi

# Clear cache
log_message "Clearing cache..."
php bin/console cache:clear || log_message "Warning: Failed to clear cache, but continuing anyway"

# Fix permissions
log_message "Fixing permissions..."
# Create var directory if it doesn't exist
if [ ! -d "var" ]; then
    mkdir -p var
fi
chmod -R 777 var/ || log_message "Warning: Failed to set permissions on var directory, but continuing anyway"

log_message "Application is ready!"

# Execute the passed command
exec "$@"
