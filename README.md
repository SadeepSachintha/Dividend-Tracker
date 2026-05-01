# Dividend Tracker 📈

![CI/CD Pipeline](https://github.com/SadeepSachintha/Dividend-Tracker/actions/workflows/ci-cd.yml/badge.svg)
![Laravel Version](https://img.shields.io/badge/laravel-12.x-red.svg)
![PHP Version](https://img.shields.io/badge/php-8.2+-777bb4.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

A high-performance, real-time dividend tracking system designed to monitor **Colombo Stock Exchange (CSE)** announcements. It automatically scrapes data, manages user holdings, and broadcasts real-time alerts via WebSockets.

---

## 🏗️ Architecture Overview

The system utilizes a robust **Producer-Consumer** architecture containerized with Docker:

-   **The Producer (Python Scraper)**: An autonomous background service that interacts directly with the **CSE API endpoint**. It fetches corporate disclosures, parses JSON responses, and synchronizes dividend events into a shared MySQL database.
-   **The Broker (Redis)**: Manages communication and real-time broadcasting queues.
-   **The Consumer (Laravel 12)**: A modern web interface that processes user holdings, calculates upcoming payouts, and broadcasts live alerts.
-   **Real-Time Engine (Laravel Reverb)**: Powers the WebSocket server for instantaneous dashboard updates without page refreshes.
-   **Database Manager (phpMyAdmin)**: Provides a web interface for database administration on port `8080`.

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
    ```bash
    ./run.sh
    ```
    *This script handles starting containers, running migrations, and initiating the scraper.*

3.  Access the applications:
    - **Dashboard**: [http://localhost:8001](http://localhost:8001)
    - **phpMyAdmin**: [http://localhost:8080](http://localhost:8080) (User: `root`, Pass: `secret`)

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
