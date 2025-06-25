# PowerShell script for Docker entrypoint
# This script provides the same functionality as docker-entrypoint.sh but is compatible with Windows

# Stop on errors
$ErrorActionPreference = "Stop"

# Always install Composer dependencies to ensure autoload_runtime.php is generated
Write-Host "Installing Composer dependencies..."
composer install --no-interaction --no-progress

# Verify that autoload_runtime.php exists
if (-not (Test-Path "vendor/autoload_runtime.php")) {
    Write-Host "Error: autoload_runtime.php is still missing after composer install"
    Write-Host "Trying to generate it explicitly..."
    composer require symfony/runtime --no-interaction --no-progress

    # Run Composer dump-autoload to ensure all autoload files are generated
    Write-Host "Running composer dump-autoload..."
    composer dump-autoload --no-interaction --optimize

    # Check again
    if (-not (Test-Path "vendor/autoload_runtime.php")) {
        Write-Host "Fatal: Could not generate autoload_runtime.php"
        # Try to debug the issue
        Write-Host "Debugging vendor directory:"
        Get-ChildItem -Path vendor/ -Force
        Write-Host "Checking for symfony/runtime package:"
        composer show symfony/runtime
        exit 1
    }
}

# Ensure the autoload_runtime.php file is accessible
Write-Host "Ensuring autoload_runtime.php is accessible..."
if (Test-Path "vendor/autoload_runtime.php") {
    Write-Host "autoload_runtime.php exists at $(Get-Location)/vendor/autoload_runtime.php"
    # In PowerShell, we don't need to set permissions explicitly
} else {
    Write-Host "Warning: autoload_runtime.php still not found after all attempts"
}

# Copy Docker environment file if it doesn't exist
if (-not (Test-Path ".env.local")) {
    Write-Host "Creating .env.local from .env.docker"
    Copy-Item .env.docker .env.local
}

# Wait for database to be ready
Write-Host "Waiting for database to be ready..."
$ATTEMPTS = 0
$ready = $false

while (-not $ready) {
    try {
        $result = php bin/console doctrine:query:sql "SELECT 1" 2>&1
        if ($LASTEXITCODE -eq 0) {
            $ready = $true
        }
    } catch {
        # Nothing to do, just retry
    }

    if (-not $ready) {
        $ATTEMPTS++
        if ($ATTEMPTS -gt 30) {
            Write-Host "Database is not available, giving up"
            exit 1
        }
        Write-Host "Database not ready yet, retrying in 10 seconds..."
        Start-Sleep -Seconds 10
    }
}

Write-Host "Database is ready!"

# Run migrations
Write-Host "Checking database status..."
# Check if a specific table exists
try {
    $TABLE_EXISTS = php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>&1
    if ($TABLE_EXISTS -eq "0") {
        Write-Host "Database is empty, running migrations..."
        php bin/console doctrine:migrations:migrate --no-interaction
    } else {
        Write-Host "Database already has tables, skipping migrations"
        # Mark migrations as executed
        php bin/console doctrine:migrations:version --add --all --no-interaction
    }
} catch {
    Write-Host "Database is empty, running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction
}

# Load fixtures if database is empty
$APP_ENV = [Environment]::GetEnvironmentVariable("APP_ENV")
if ($APP_ENV -eq "dev") {
    # Check if the user table exists
    try {
        $TABLE_EXISTS = php bin/console doctrine:query:sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'user'" 2>&1

        if ($TABLE_EXISTS -eq "0") {
            Write-Host "Creating database schema..."
            php bin/console doctrine:schema:create --no-interaction

            Write-Host "Loading fixtures..."
            php bin/console doctrine:fixtures:load --no-interaction
        } else {
            # Check if there are any users in the user table
            $USER_RECORDS = php bin/console doctrine:query:sql "SELECT COUNT(*) FROM `"user`"" 2>&1

            if ($USER_RECORDS -eq "0") {
                Write-Host "User table exists but is empty, loading fixtures..."
                php bin/console doctrine:fixtures:load --no-interaction
            } else {
                Write-Host "Database already has data, skipping fixtures"
            }
        }
    } catch {
        Write-Host "Creating database schema..."
        php bin/console doctrine:schema:create --no-interaction

        Write-Host "Loading fixtures..."
        php bin/console doctrine:fixtures:load --no-interaction
    }
}

# Clear cache
Write-Host "Clearing cache..."
php bin/console cache:clear

# Fix permissions
Write-Host "Fixing permissions..."
# On Windows, we don't need chmod, but we can ensure the var directory exists
if (-not (Test-Path "var")) {
    New-Item -ItemType Directory -Path "var"
}

Write-Host "Application is ready!"

# Execute the passed command
if ($args.Count -gt 0) {
    & $args[0] $args[1..$args.Count]
}
