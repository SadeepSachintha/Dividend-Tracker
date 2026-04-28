FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Run the script periodically or use a scheduler like cron
CMD ["python", "scraper.py"]
