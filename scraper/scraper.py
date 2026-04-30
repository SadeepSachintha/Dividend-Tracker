import os
import time
import requests
from bs4 import BeautifulSoup
from datetime import datetime
import mysql.connector
from dotenv import load_dotenv

# Load environment variables (we will share the .env with Laravel or use a separate one)
load_dotenv()

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")
DB_NAME = os.getenv("DB_DATABASE", "laravel")

CSE_ANNOUNCEMENTS_URL = "https://www.cse.lk/announcements/?category=CORPORATE%20DISCLOSURE"

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
        response = requests.get(CSE_ANNOUNCEMENTS_URL, timeout=10)
        response.raise_for_status()
        return response.content
    except requests.RequestException as e:
        print(f"Error fetching CSE announcements: {e}")
        return None

def parse_dividends(html_content):
    soup = BeautifulSoup(html_content, 'html.parser')
    dividends = []
    
    # CSE typically puts corporate disclosures in a table with class 'table' or similar
    table = soup.find('table')
    if not table:
        print("No announcement table found.")
        return dividends
        
    rows = table.find_all('tr')
    for row in rows[1:]: # Skip header
        cols = row.find_all('td')
        if len(cols) >= 3:
            symbol = cols[0].text.strip()
            date_raw = cols[1].text.strip()
            # Convert DD-MM-YYYY to YYYY-MM-DD for MySQL
            try:
                date_obj = datetime.strptime(date_raw, '%d-%m-%Y')
                mysql_date = date_obj.strftime('%Y-%m-%d')
            except ValueError:
                mysql_date = None # Or handle appropriately
            title = cols[2].text.strip().lower()
            
            # Check for dividend keywords
            if 'dividend' in title or 'scrip' in title or 'xd' in title:
                print(f"Found Dividend Announcement for {symbol}: {title}")
                
                # Basic extraction heuristics
                amount_per_share = 0.0
                dividend_type = "Interim"
                if 'final' in title:
                    dividend_type = "Final"
                
                # Attempt to extract amount (e.g., Rs. 5.00)
                import re
                match = re.search(r'(rs\.?\s*|rupees?\s*)(\d+\.?\d*)', title)
                if match:
                    amount_per_share = float(match.group(2))
                    
                dividends.append({
                    'symbol': symbol,
                    'amount_per_share': amount_per_share,
                    'type': dividend_type,
                    'raw_text': title,
                    'date': mysql_date
                })
                
    return dividends

def store_dividends(dividends):
    conn = get_db_connection()
    if not conn:
        return
        
    cursor = conn.cursor()
    
    # In a real app, we'd trigger a webhook to Laravel here to broadcast the event
    # e.g. requests.post('http://app/api/webhook/dividend', json=dividend)
    
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
    print("Starting dividend scraper...")
    html_content = fetch_announcements()
    if html_content:
        dividends = parse_dividends(html_content)
        if dividends:
            store_dividends(dividends)
            print(f"Stored {len(dividends)} dividends.")
        else:
            print("No dividend announcements found.")

if __name__ == "__main__":
    import sys
    
    # Allow running once via command line argument (e.g., for manual triggers)
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
