# Dividend Tracker 📈

[![CI/CD Pipeline](https://github.com/SadeepSachintha/Dividend-Tracker/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/SadeepSachintha/Dividend-Tracker/actions/workflows/ci-cd.yml)

A high-performance, real-time dividend tracking system designed to monitor **Colombo Stock Exchange (CSE)** announcements. It automatically scrapes data, manages user holdings, and broadcasts real-time alerts via WebSockets.

---

## 🏗️ Architecture Overview

The system utilizes a robust **Producer-Consumer** architecture containerized with Docker:

-   **The Producer (Python Scraper)**: A dedicated service that polls the CSE website, parses complex announcement tables, and synchronizes the latest dividend events into a shared MySQL database.
-   **The Broker (Redis)**: Manages communication and caching between services.
-   **The Consumer (Laravel 12)**: A modern web interface that processes user holdings, calculates upcoming payouts, and broadcasts live alerts.
-   **Real-Time Engine (Laravel Reverb)**: Powers the WebSocket server for instantaneous dashboard updates without page refreshes.

---

## 🛠️ Tech Stack

-   **Backend**: [Laravel 12](https://laravel.com/) (PHP 8.2)
-   **Scraper**: [Python 3.11](https://www.python.org/) (Requests, BeautifulSoup)
-   **Real-Time**: [Laravel Reverb](https://reverb.laravel.com/) (WebSockets)
-   **Database**: [MySQL 8.0](https://www.mysql.com/)
-   **In-Memory Store**: [Redis](https://redis.io/)
-   **Web Server**: [Nginx](https://www.nginx.com/)
-   **Infrastructure**: [Docker](https://www.docker.com/), Docker Compose
-   **Networking**: [Tailscale](https://tailscale.com/) (Secure Private Tunneling)

---

## 🚀 Getting Started

### 1. Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.
- [Tailscale](https://tailscale.com/download) installed (for remote deployment access).

### 2. Local Installation (Windows)
1.  Clone the repository:
    ```bash
    git clone https://github.com/SadeepSachintha/Dividend-Tracker.git
    cd Dividend-Tracker
    ```
2.  Launch the system using the provided automation script:
    ```bash
    ./run.bat
    ```
    *This will build images, start containers, run migrations, and initiate the scraper.*

3.  Access the dashboard:
    - **URL**: [http://localhost:8000](http://localhost:8000)

---

## ☁️ Automated Deployment (CI/CD)

The project features a fully automated pipeline using **GitHub Actions** and **Tailscale** for secure deployment to an Ubuntu VM, even behind residential CGNAT/Firewalls.

### 1. Required GitHub Secrets
Navigate to **Settings > Secrets and variables > Actions** and add:

| Secret | Description |
| :--- | :--- |
| `VM_HOST` | The **Tailscale IP** of your Ubuntu VM (e.g., `100.x.y.z`). |
| `VM_USERNAME` | Your VM login username (e.g., `sadeep`). |
| `VM_SSH_KEY` | Your OpenSSH Private Key. |
| `TAILSCALE_AUTHKEY` | A reusable Auth Key from the Tailscale Admin Console. |

### 2. Triggering Deployment
Simply push your changes to the main branch:
```bash
git push origin main
```
*The workflow will verify code quality, connect to your private Tailscale network, and update the production containers on your VM.*

---

## 📝 License
This project is licensed under the MIT License.
