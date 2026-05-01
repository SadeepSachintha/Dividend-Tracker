import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": [],
    "fromDate": "2025-01-01",
    "toDate": "2026-05-01"
}

HEADERS = {
    "Content-Type": "application/json",
    "Referer": "https://www.cse.lk/announcements",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
}

try:
    response = requests.post(URL, json=PAYLOAD, headers=HEADERS, timeout=15)
    if response.status_code == 200:
        data = response.json()
        items = data.get('approvedAnnouncements', [])
        
        # Look for dividend announcements
        div_items = [i for i in items if 'DIVIDEND' in i.get('announcementCategory', '').upper()]
        print(f"Found {len(div_items)} dividend items.")
        
        if div_items:
            # Print unique keys across all dividend items
            keys = set()
            for i in div_items:
                keys.update(i.keys())
            print("Unique keys in dividend items:", keys)
            
            # Print first 2 dividend items
            for i in div_items[:2]:
                print(json.dumps(i, indent=2))
        else:
            print("Still no dividends found. Printing first 2 categories found:")
            cats = set(i.get('announcementCategory') for i in items)
            print(list(cats)[:10])
                
except Exception as e:
    print(f"EXCEPTION: {e}")
