import os
import time
import requests
from bs4 import BeautifulSoup
from datetime import datetime
import mysql.connector
from dotenv import load_dotenv
import re

# Load environment variables
load_dotenv()

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")
DB_NAME = os.getenv("DB_DATABASE", "dividend_tracker")

CSE_ANNOUNCEMENTS_URL = "https://www.cse.lk/api/smd/getAnnouncementData"
CSE_API_KEY = "Q3NlMTIzQXBp"

def get_db_connection():
    max_retries = 5
    for i in range(max_retries):
        try:
            return mysql.connector.connect(
                host=DB_HOST,
                user=DB_USER,
                password=DB_PASSWORD,
                database=DB_NAME
            )
        except mysql.connector.Error as err:
            if i < max_retries - 1:
                print(f"Database connection failed, retrying in 5s... ({i+1}/{max_retries})")
                time.sleep(5)
            else:
                print(f"Error connecting to database after {max_retries} attempts: {err}")
                return None

def fetch_announcements():
    try:
        payload = {
            "selectedCompany": [],
            "selectedCategory": ["CORPORATE DISCLOSURE"],
            "fromDate": "",
            "toDate": ""
        }
        headers = {
            "Content-Type": "application/json",
            "x-api-key": CSE_API_KEY,
            "Origin": "https://www.cse.lk",
            "Referer": "https://www.cse.lk/announcements",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
            "X-Requested-With": "XMLHttpRequest"
        }
        response = requests.post(CSE_ANNOUNCEMENTS_URL, json=payload, headers=headers, timeout=15)
        response.raise_for_status()
        return response.json()
    except requests.RequestException as e:
        print(f"Error fetching CSE announcements from API: {e}")
        if hasattr(e, 'response') and e.response:
            print(f"Response status: {e.response.status_code}")
        return None

def parse_dividends(data):
    dividends = []
    
    if not isinstance(data, list):
        print("Unexpected API response format (not a list).")
        return dividends
        
    for item in data:
        # The API returns an array of objects
        # Use securityCode as symbol if available, fallback to companyName
        symbol = item.get('securityCode', item.get('companyName', '')).strip()
        date_raw = item.get('announcementDate', '').strip()
        title = item.get('announcementTitle', '').strip().lower()
        description = item.get('announcementDescription', '').strip().lower()
        
        full_text = f"{title} {description}"
        
        # Convert DD-MM-YYYY to YYYY-MM-DD for MySQL
        try:
            date_obj = datetime.strptime(date_raw, '%d-%m-%Y')
            mysql_date = date_obj.strftime('%Y-%m-%d')
        except (ValueError, TypeError):
            mysql_date = None
            
        # Check for dividend keywords
        if 'dividend' in full_text or 'scrip' in full_text or 'xd' in full_text:
            print(f"Found Potential Dividend Announcement for {symbol}: {description}")
            
            # Basic extraction heuristics
            amount_per_share = 0.0
            dividend_type = "Interim"
            if 'final' in full_text:
                dividend_type = "Final"
            
            # Attempt to extract amount (e.g., Rs. 5.00)
            match = re.search(r'(rs\.?\s*|rupees?\s*)(\d+\.?\d*)', full_text)
            if match:
                try:
                    amount_per_share = float(match.group(2))
                except ValueError:
                    amount_per_share = 0.0
                    
            dividends.append({
                'symbol': symbol,
                'amount_per_share': amount_per_share,
                'type': dividend_type,
                'raw_text': description[:250],
                'date': mysql_date
            })
                
    return dividends

def store_dividends(dividends):
    conn = get_db_connection()
    if not conn:
        return
        
    cursor = conn.cursor()
    
    for div in dividends:
        # Insert announcement
        sql_ann = "INSERT INTO announcements (symbol, announcement_type, date, raw_text, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())"
        val_ann = (div['symbol'], 'Dividend', div['date'], div['raw_text'])
        
        # Insert processed dividend
        sql_div = "INSERT INTO dividends (symbol, amount_per_share, type, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())"
        val_div = (div['symbol'], div['amount_per_share'], div['type'])
        
        try:
            cursor.execute(sql_ann, val_ann)
            cursor.execute(sql_div, val_div)
            print(f"Saved {div['symbol']} to DB.")
        except mysql.connector.Error as err:
            print(f"Error saving {div['symbol']}: {err}")
    
    conn.commit()
    cursor.close()
    conn.close()

def run():
    print("Starting dividend scraper (API mode)...")
    data = fetch_announcements()
    if data:
        dividends = parse_dividends(data)
        if dividends:
            store_dividends(dividends)
            print(f"Stored {len(dividends)} dividends.")
        else:
            print("No dividend announcements found in current API response.")
    else:
        print("Failed to fetch data from CSE API.")

if __name__ == "__main__":
    import sys
    
    # Allow running once via command line argument
    if len(sys.argv) > 1 and sys.argv[1] == '--once':
        run()
    else:
        # Continuous polling mode
        print("Scraper service started. Polling every hour...")
        while True:
            try:
                run()
            except Exception as e:
                print(f"Unexpected error during scrape: {e}")
            
            print("Sleeping for 1 hour before next scrape...")
            time.sleep(3600)
