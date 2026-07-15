#!/bin/bash

# Docker setup script for PayCan (SQLite version)

echo "🚀 Setting up PayCan with Docker (SQLite)..."

# Create docker directory if it doesn't exist
mkdir -p docker

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Created .env file from .env.example"
else
    echo "ℹ️  .env file already exists"
fi

# Build and start containers
echo "🏗️  Building Docker containers..."
docker-compose up -d --build

# Wait for services to start
echo "⏳ Waiting for services to start..."
sleep 10

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
docker-compose exec app composer install --optimize-autoloader --no-dev

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Set proper permissions
echo "🔒 Setting file permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/bootstrap/cache

# Create SQLite database file
echo "🗄️  Creating SQLite database..."
docker-compose exec app touch database/database.sqlite

# Run database migrations
echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

echo ""
echo "🎉 PayCan Docker setup complete!"
echo ""
echo "🌐 Access the application at: http://localhost:8000"
echo ""
echo "Next steps:"
echo "1. Visit http://localhost:8000/install to complete the web-based setup"
echo "2. Or create an admin user: docker-compose exec app php artisan make:admin-user"
echo "3. Access the admin panel at: http://localhost:8000/admin"
echo ""
echo "To stop the containers: docker-compose down"
echo "To view logs: docker-compose logs -f"