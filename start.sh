#!/bin/bash
set -e

echo "Starting Laravel application..."

# Install and build frontend assets
echo "Installing Node.js dependencies..."
npm install

echo "Building frontend assets..."
npm run build

# Run migrations
echo "Running migrations..."
php artisan migrate --force
php artisan db:seed

# Optimize application
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache

# Start server
echo "Starting server on http://0.0.0.0:8000"
php artisan serve --host=0.0.0.0 --port=8000
