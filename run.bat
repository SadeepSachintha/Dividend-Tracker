@echo off
echo [1/4] Starting Docker containers...
docker-compose up -d --build

echo [2/4] Waiting for database to be ready...
timeout /t 10

echo [3/4] Running database migrations...
docker exec dividend_laravel_app php artisan migrate --force

echo [4/4] Scraper service is running in the background...

echo.
echo System is up and running!
echo Dashboard: http://localhost:8000
echo phpMyAdmin: http://localhost (if using Docker port 80/3306)
pause
