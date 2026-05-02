# Dividend Tracker 📈

![CI/CD Pipeline](https://github.com/SadeepSachintha/Dividend-Tracker/actions/workflows/ci-cd.yml/badge.svg)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-red.svg)
![PHP Version](https://img.shields.io/badge/php-8.2+-777bb4.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

A high-performance, real-time dividend tracking system designed to monitor **Colombo Stock Exchange (CSE)** announcements. It automatically scrapes data, manages user holdings, and broadcasts real-time alerts via WebSockets.

---

## 🏗️ Architecture Overview

The system utilizes a robust **Producer-Consumer** architecture containerized with Docker:

-   **The Producer (Python Scraper)**: An autonomous background service that interacts directly with the **CSE API**. It maintains a master list of all ~290 listed companies, fetches corporate disclosures, parses JSON responses, and synchronizes dividend events into a shared MySQL database.
-   **The Broker (Redis)**: Manages communication and real-time broadcasting queues.
-   **The Consumer (Laravel 12)**: A modern web interface that processes user holdings, calculates upcoming payouts, and broadcasts live alerts.
-   **Real-Time Engine (Laravel Reverb)**: Powers the WebSocket server for instantaneous dashboard updates without page refreshes.
-   **Database Manager (phpMyAdmin)**: Provides a web interface for database administration on port `8080`.

---

## ✨ Key Features

-   **Autonomous CSE Scraping**: Leverages the latest **CSE API v2** for high-frequency dividend monitoring and company data synchronization.
-   **Market Watch**: A comprehensive directory of all listed companies on the CSE, featuring real-time price tracking and dividend yield calculations.
-   **Intelligent Parsing**: Automatically extracts dividend amounts, types (Interim/Final), and XD dates from disclosures.
-   **Portfolio Analytics**: Real-time calculation of Yield on Cost (YoC) and Estimated Annual Income.
-   **Manual & Autonomous Sync**: Trigger a manual scraper run directly from the dashboard or rely on the autonomous background service.
-   **Corporate Actions Feed**: A dedicated dashboard section showing the latest 5 corporate announcements found by the scraper.
-   **Zero-Config Demo Mode**: Automatically displays a curated sample portfolio if no user holdings are detected, ensuring immediate visual feedback.
-   **Hardened CI/CD**: Pre-built image strategy with GHCR and Tailscale for secure, resource-efficient remote deployments.

---

## 🛠️ Tech Stack

| Category | Technology |
| :--- | :--- |
| **Backend** | [Laravel 12](https://laravel.com/) (PHP 8.2) |
| **Scraper** | [Python 3.11](https://www.python.org/) (BeautifulSoup, Requests) |
| **Real-Time** | [Laravel Reverb](https://reverb.laravel.com/) (Native WebSockets) |
| **Database** | [MySQL 8.0](https://www.mysql.com/) |
| **DB Management** | [phpMyAdmin](https://www.phpmyadmin.net/) |
| **Cache/Broker** | [Redis](https://redis.io/) |
| **Server** | [Nginx](https://www.nginx.com/) |
| **DevOps** | [Docker](https://www.docker.com/), [GitHub Actions](https://github.com/features/actions) |
| **Network** | [Tailscale](https://tailscale.com/) (Secure Private Networking) |

---

## 🚀 Getting Started

### 1. Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed.
- [Tailscale](https://tailscale.com/download) installed (for secure remote access).

### 2. Local Installation
1.  Clone the repository:
    ```bash
    git clone https://github.com/SadeepSachintha/Dividend-Tracker.git
    cd Dividend-Tracker
    ```
2.  Launch the automated setup:
    - **Windows**:
      ```bash
      run.bat
      ```
    - **Linux/macOS**:
      ```bash
      ./run.sh
      ```
    *These scripts handle starting containers, running migrations, and initiating the scraper.*

4.  **Data Initialization**:
    Populate your portfolio and fetch real market data to activate the dashboard:
    - **Step 1: Create Portfolio Holdings** (Removes Demo Mode):
      ```bash
      docker exec dividend_laravel_app php artisan db:seed
      ```
    - **Step 2: Seed Real Historical Dividends** (Optional):
      ```bash
      docker exec dividend_laravel_app php artisan db:seed --class=RealDividendSeeder
      ```
    - **Step 3: Sync Master Company List**:
      ```bash
      docker exec dividend_scraper python scraper.py --once
      ```

3.  **Access & Management**:
    - **Dashboard**: [http://localhost:8001](http://localhost:8001)
    - **Market Watch**: [http://localhost:8001/market](http://localhost:8001/market)
    - **phpMyAdmin**: [http://localhost:8080](http://localhost:8080) (Docker) or [http://localhost/phpmyadmin](http://localhost/phpmyadmin) (XAMPP)
    - **Manual Sync**: Click the "Sync" button on the UI to trigger an immediate scraper run (Announcements + Company Prices).

---

## ☁️ Automated Deployment (CI/CD)

The project features a high-performance CI/CD pipeline using **GitHub Actions**, **GHCR (GitHub Container Registry)**, and **Tailscale**.

### 1. Build & Push Strategy
To prevent resource exhaustion on small target VMs, Docker images are pre-built and compiled on GitHub's infrastructure and pushed to **GHCR**. The VM simply pulls the latest pre-compiled images, resulting in near-instant deployments with minimal CPU/RAM usage.

### 2. Required GitHub Secrets
| Secret | Description |
| :--- | :--- |
| `VM_HOST` | The **Tailscale IP** of your Ubuntu VM. |
| `VM_USERNAME` | Your VM login username. |
| `VM_SSH_KEY` | Your OpenSSH Private Key. |
| `TAILSCALE_AUTHKEY` | A reusable Tailscale Auth Key. |

---

## 🔧 Troubleshooting & Development

### PHP Extensions
The Laravel application requires several specific PHP extensions to handle the database and real-time features. These are pre-installed in the Docker container:
- `pdo_mysql`, `bcmath`, `gd`, `intl`, `zip`, and `redis`.

### Build Issues
If the build fails on your local machine, ensure you are using **LF** line endings for the `Dockerfile`. If a container hangs, verify that your machine has enough allocated RAM (minimum 2GB recommended).

### Scraper Restarting
If you see an error stating "Container is restarting, wait until the container is running", it means the scraper script has exited. The scraper is now configured to run in a continuous loop (`while True:`) to prevent this.

---

## 📝 License
This project is licensed under the MIT License.
