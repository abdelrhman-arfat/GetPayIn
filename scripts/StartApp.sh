#!/bin/bash

# Run this script from the project root directory 

set -e  # Exit immediately if a command exits with a non-zero status
echo "=============================="
echo "🚀 Starting Laravel Application Setup"
echo "=============================="

# Install dependencies
echo "Installing Composer dependencies..."
composer install

# Run migrations
echo "Running migrations..."
php artisan migrate

# Run seeders
echo "Seeding database..."
php artisan db:seed --class=DatabaseSeeder

# Clear and cache config (optional but recommended)
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache

# Generate application key
echo "Generating application key..."
php artisan key:generate

echo "Copy .env.example to .env"
cp .env.example .env

# Ask developer for port
read -p "Enter port to run Laravel server [default: 8000]: " PORT
PORT=${PORT:-8000}  # Use 8000 if user presses Enter without typing

# Start Laravel server
echo "Starting Laravel development server on port $PORT..."
php artisan serve --port=$PORT

echo "Laravel setup completed successfully!"
echo "You can now access your Laravel application at http://localhost:$PORT"