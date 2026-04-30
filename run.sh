#!/bin/bash

# Make sure the script stops on error
set -e

echo "[1/4] Starting Docker containers..."
docker compose up -d --build

echo "[2/4] Waiting for database to be ready..."
# Give MySQL some time to initialize
sleep 10

echo "[3/4] Installing Laravel dependencies..."
docker exec dividend_laravel_app composer install

echo "[4/4] Running database migrations..."
docker exec dividend_laravel_app php artisan migrate --force

echo "[5/5] Fixing permissions..."
docker exec dividend_laravel_app chown -R www-data:www-data storage bootstrap/cache

echo -e "\nSystem is up and running!"
echo "Dashboard: http://localhost:8001"
echo "Tailscale: http://100.102.103.8:8001 (if applicable)"
echo "phpMyAdmin: http://localhost:8080 (if configured)"
