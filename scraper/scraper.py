import os
import time
import requests
from datetime import datetime, timedelta
import mysql.connector
from dotenv import load_dotenv
import re

# Load environment variables
load_dotenv()

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "secret")
DB_NAME = os.getenv("DB_DATABASE", "dividend_tracker")

# Updated API endpoint discovered via browser inspection
CSE_ANNOUNCEMENTS_URL = "https://www.cse.lk/api/approvedAnnouncement"

def get_db_connection():
    max_retries = 3
    for i in range(max_retries):
        try:
            return mysql.connector.connect(
                host=DB_HOST,
                user=DB_USER,
                password=DB_PASSWORD,
                database=DB_NAME
            )
        except mysql.connector.Error as err:
            # Fallback for local dev if 'db' host (Docker) fails
            if i == 0 and DB_HOST == "db":
                try:
                    return mysql.connector.connect(host="127.0.0.1", user=DB_USER, password=DB_PASSWORD, database=DB_NAME)
                except: pass
            
            if i < max_retries - 1:
                time.sleep(2)
            else:
                print(f"Error connecting to database: {err}")
                return None

def fetch_announcements():
    try:
        # Fetch wide range to ensure we get historical data for the dashboard
        from_date = (datetime.now() - timedelta(days=120)).strftime('%Y-%m-%d')
        to_date = datetime.now().strftime('%Y-%m-%d')
        
        payload = {
            "selectedCompany": [],
            "selectedCategory": ["CASH DIVIDEND"], # The API might ignore this but we send it
            "fromDate": from_date,
            "toDate": to_date
        }
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json, text/plain, */*",
            "Origin": "https://www.cse.lk",
            "Referer": "https://www.cse.lk/announcements",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
        }
        response = requests.post(CSE_ANNOUNCEMENTS_URL, json=payload, headers=headers, timeout=15)
        response.raise_for_status()
        return response.json()
    except requests.RequestException as e:
        print(f"Error fetching CSE announcements: {e}")
        return None

def parse_dividends(data):
    dividends = []
    items = data.get('approvedAnnouncements', [])
    print(f"Total items received from API: {len(items)}")
    
    if not items:
        return dividends
        
    for item in items:
        category = item.get('announcementCategory', '').upper()
        # We only care about dividends
        if 'DIVIDEND' not in category:
            continue
            
        print(f"Checking potential dividend: {item.get('company')} | {category}")
        
        symbol = item.get('symbol')
        if not symbol:
            # Fallback: Many announcements have the symbol as the first part of the company name or a separate field
            # For now, let's just log it
            print(f"  Skipping: No symbol found for {item.get('company')}")
            continue
            
        date_raw = item.get('dateOfAnnouncement', '') # e.g. "30 Apr 2026"
        xd_date_raw = item.get('xd') # e.g. "2026-05-10"
        description = item.get('remarks', '') or category
        
        # Convert dateOfAnnouncement
        try:
            date_obj = datetime.strptime(date_raw, '%d %b %Y')
            mysql_date = date_obj.strftime('%Y-%m-%d')
        except:
            mysql_date = datetime.now().strftime('%Y-%m-%d')

        # Amount extraction from category or remarks
        amount_per_share = 0.0
        # Typical format: "Rs. 5.00 per share"
        match = re.search(r'(rs\.?\s*|rupees?\s*)(\d+\.?\d*)', (description + " " + category).lower())
        if match:
            try:
                amount_per_share = float(match.group(2))
            except ValueError:
                amount_per_share = 0.0
        
        dividends.append({
            'symbol': symbol,
            'amount_per_share': amount_per_share,
            'type': category.title(),
            'raw_text': description[:250],
            'date': mysql_date,
            'xd_date': xd_date_raw
        })
                
    return dividends

def store_dividends(dividends):
    conn = get_db_connection()
    if not conn: return
    cursor = conn.cursor()
    
    for div in dividends:
        try:
            # Check if exists to avoid duplicates
            cursor.execute("SELECT id FROM dividends WHERE symbol = %s AND amount_per_share = %s AND xd_date = %s", 
                          (div['symbol'], div['amount_per_share'], div['xd_date']))
            if cursor.fetchone():
                continue

            # Insert announcement
            sql_ann = "INSERT INTO announcements (symbol, announcement_type, date, raw_text, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())"
            cursor.execute(sql_ann, (div['symbol'], 'Dividend', div['date'], div['raw_text']))
            
            # Insert processed dividend
            sql_div = "INSERT INTO dividends (symbol, amount_per_share, type, xd_date, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())"
            cursor.execute(sql_div, (div['symbol'], div['amount_per_share'], div['type'], div['xd_date']))
            
            print(f"Saved {div['symbol']} dividend: Rs. {div['amount_per_share']}")
        except mysql.connector.Error as err:
            print(f"Error saving {div['symbol']}: {err}")
    
    conn.commit()
    cursor.close()
    conn.close()

def run():
    print(f"[{datetime.now()}] Starting dividend scraper...")
    data = fetch_announcements()
    if data:
        dividends = parse_dividends(data)
        if dividends:
            store_dividends(dividends)
            print(f"Processed {len(dividends)} dividends.")
        else:
            print("No new dividend announcements found.")
    else:
        print("Failed to fetch data.")

if __name__ == "__main__":
    import sys
    if len(sys.argv) > 1 and sys.argv[1] == '--once':
        run()
    else:
        while True:
            try: run()
            except Exception as e: print(f"Error: {e}")
            time.sleep(3600)
