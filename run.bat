@echo off
echo [1/4] Starting Docker containers...
docker-compose up -d --build

echo [2/4] Waiting for database to be ready...
timeout /t 10

echo [3/4] Running database migrations...
docker exec dividend_laravel_app php artisan migrate --force

echo [4/4] Fixing permissions...
docker exec dividend_laravel_app chown -R www-data:www-data storage bootstrap/cache

echo.
echo System is up and running!
echo Dashboard: http://localhost:8001
echo Tailscale: http://100.102.103.8:8001 (if applicable)
echo phpMyAdmin: http://localhost (if using Docker port 80/3306)
pause
