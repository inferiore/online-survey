#!/bin/bash
set -e

echo "Starting Laravel application..."

# Run migrations
echo "Running migrations..."
php artisan migrate --force
php artisan db:seed
# Optimize application
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache

# Database seeding must be done manually
# Run: docker-compose exec app php artisan db:seed

# Start server
echo "Starting server on http://0.0.0.0:8000"
php artisan serve --host=0.0.0.0 --port=8000
