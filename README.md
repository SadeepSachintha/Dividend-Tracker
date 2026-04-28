# Dividend Tracker 📈

A real-time dividend tracking system designed to monitor CSE (Colombo Stock Exchange) announcements and manage user holdings with real-time alerts.

## 🏗️ Architecture
The system follows a producer-consumer architecture:
- **Scraper (Python)**: Periodically monitors CSE announcements, extracts dividend data, and stores it in the database.
- **Web App (Laravel 12)**: Provides a dashboard to track holdings, view upcoming dividends, and manage alerts.
- **Database (MySQL 8.0)**: Shared storage for announcements, dividends, and user data.
- **Containerization (Docker)**: Entire stack is containerized for seamless deployment between Windows (development) and Ubuntu (production).

## 🚀 Getting Started (Windows)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Composer](https://getcomposer.org/) (optional, for local development)

### Quick Start
1. **Clone the repository**:
   ```bash
   git clone <your-repo-url>
   cd "Dividend Tracker"
2. **Run the system**:
   Double-click the `run.bat` file in the root directory. This will:
   - Start all Docker containers (`app`, `db`, `scraper`, `redis`, `webserver`).
   - Run database migrations.
   - Execute the scraper to fetch the latest dividends.
3. **Access the App**:
   - Dashboard: [http://localhost:8000](http://localhost:8000)

## ☁️ Deployment (Ubuntu Server)

The project includes a GitHub Actions CI/CD pipeline for automatic deployment.

### 1. Setup GitHub Secrets
Add the following secrets to your GitHub repository (**Settings > Secrets > Actions**):
- `VM_HOST`: Your server's Public IP.
- `VM_USERNAME`: Your SSH username (e.g., `ubuntu`).
- `VM_SSH_KEY`: Your private SSH key content.

### 2. Configuration
Update the deployment path in `.github/workflows/ci-cd.yml`:
```yaml
- cd /home/your-user/dividend-tracker
```

### 3. Deploy
Simply push your changes to the `main` branch:
```bash
git add .
git commit -m "Your message"
git push origin main
```

## 🛠️ Tech Stack
- **Backend**: Laravel 12, PHP 8.2
- **Scraper**: Python 3.11 (BeautifulSoup, Requests)
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis
- **Server**: Nginx
- **DevOps**: Docker, Docker Compose, GitHub Actions

## 📝 License
MIT
