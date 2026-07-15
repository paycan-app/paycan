#!/bin/bash
set -e

# Change to application directory
cd /var/www/html

echo "=== PayCan Application Setup ==="

# SCENARIO 1: Handle .env file intelligently
if [ ! -f ".env" ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
    # Set default SQLite configuration
    sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=database.sqlite/' .env
else
    echo ".env file already exists - preserving user configuration"
fi

# SCENARIO 2: Install dependencies only if needed
if [ ! -d "vendor" ]; then
    echo "Installing PHP dependencies..."
    composer install --optimize-autoloader --no-dev --prefer-dist
else
    echo "Dependencies already installed"
fi

# SCENARIO 3: Preserve application key between rebuilds
current_key=$(grep '^APP_KEY=' .env 2>/dev/null | cut -d= -f2-)
if [ -z "$current_key" ] || [ "$current_key" = "" ] || [ "$current_key" = "SomeRandomString" ]; then
    echo "Generating application key..."
    php artisan key:generate
else
    echo "Application key already set: ${current_key:0:10}..."
    echo "Key preserved between rebuilds ✓"
fi

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 storage bootstrap/cache

# SCENARIO 4: Handle different database types intelligently
db_connection=$(grep '^DB_CONNECTION=' .env | cut -d= -f2)
echo "Using database: $db_connection"

if [ "$db_connection" = "sqlite" ]; then
    # Only create SQLite database if file doesn't exist
    if [ ! -f "database/database.sqlite" ]; then
        echo "Creating SQLite database..."
        touch database/database.sqlite
        chown www-data:www-data database/database.sqlite
        chmod 664 database/database.sqlite
    else
        echo "SQLite database already exists - data preserved ✓"
    fi
else
    echo "Using external database ($db_connection) - no local file creation needed"
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link if needed
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
else
    echo "Storage link already exists"
fi

echo "=== Setup Complete ==="
echo "Starting PHP-FPM..."

# Start PHP-FPM
exec php-fpm "$@"