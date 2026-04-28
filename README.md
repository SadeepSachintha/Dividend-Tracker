# Dividend Tracker 📈

A precision-focused, real-time dividend tracking system designed to monitor corporate actions and map them directly to your specific stock holdings.

Built with a **Producer-Consumer** architecture:
* **The Producer (Python Scraper)**: A background task that fetches real-time corporate disclosures from the CSE (Colombo Stock Exchange), parses them for dividends, and injects them into the database.
* **The Consumer (Laravel 12)**: A robust PHP engine that manages your holdings, calculates your Yield on Cost, and broadcasts real-time WebSocket alerts to a dynamic Tailwind CSS dashboard.

## 🏗️ System Architecture

* **Backend & Real-time**: Laravel 12 + Laravel Reverb
* **Data Ingestion**: Python 3.11 (`requests`, `beautifulsoup4`)
* **Database**: MySQL 8.0
* **Frontend**: Blade + Tailwind CSS + Alpine.js
* **Deployment**: Fully Dockerized for Ubuntu VM

## 🚀 Deployment Instructions (Ubuntu VM)

This repository is pre-configured with `docker-compose` to completely bypass local environment issues and ensure a zero-touch, containerized deployment on your Ubuntu server.

### 1. Start the Cluster
Navigate to the root directory and spin up the containers:
```bash
docker-compose up -d --build
```
This will start 5 services:
- `dividend_mysql`
- `dividend_redis`
- `dividend_laravel_app`
- `dividend_webserver` (Nginx on Port 8000)
- `dividend_scraper`

### 2. Initialize Laravel Setup
Once the containers are running, execute the final Laravel setup commands inside the app container:
```bash
# Enter the Laravel container
docker exec -it dividend_laravel_app bash

# Inside the container, install PHP dependencies
composer install

# Generate application key
php artisan key:generate

# Run the database migrations
php artisan migrate

# Install Laravel Breeze (to scaffold auth and Tailwind)
composer require laravel/breeze --dev
php artisan breeze:install blade

# Exit the container
exit
```

### 3. Access the Dashboard
Visit `http://<your-vm-ip>:8000` in your browser.
You will be greeted with the rich glassmorphism dashboard displaying your Total Investment, Estimated Annual Income, and Yield on Cost.

## 📂 Project Structure

- `/laravel_app` - The Laravel 12 application codebase.
  - `app/Models` - Contains `Holding`, `Announcement`, and `Dividend` models.
  - `app/Events` - Contains `DividendReceived.php` for WebSockets.
  - `app/Http/Controllers` - Contains `DashboardController.php` for yield math.
  - `resources/views` - Contains the Tailwind `dashboard.blade.php`.
- `/scraper` - The Python background job.
  - `scraper.py` - Parses HTML tables from CSE and applies regex heuristics.
- `docker-compose.yml` - Orchestration configuration.
- `nginx.conf` - Web server routing for Laravel.
