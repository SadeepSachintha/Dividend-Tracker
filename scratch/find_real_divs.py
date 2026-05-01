import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": [], # Fetch all and filter in Python
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
        
        # Look for items with xd date
        xd_items = [i for i in items if i.get('xd') is not None]
        print(f"Found {len(xd_items)} items with XD date.")
        
        if xd_items:
            for i in xd_items[:3]:
                print(json.dumps(i, indent=2))
        else:
            # Look for items with DIVIDEND in category
            div_items = [i for i in items if 'DIVIDEND' in i.get('announcementCategory', '').upper()]
            print(f"Found {len(div_items)} dividend items by category string.")
            if div_items:
                for i in div_items[:3]:
                    print(json.dumps(i, indent=2))
                
except Exception as e:
    print(f"EXCEPTION: {e}")
