import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": ["CASH DIVIDEND"],
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
        print(f"Found {len(items)} items for CASH DIVIDEND.")
        if items:
            print("First item:", json.dumps(items[0], indent=2))
        else:
            print("No items found. Trying with empty category list to see all...")
            PAYLOAD["selectedCategory"] = []
            response2 = requests.post(URL, json=PAYLOAD, headers=HEADERS, timeout=15)
            data2 = response2.json()
            items2 = data2.get('approvedAnnouncements', [])
            print(f"Found {len(items2)} total items.")
            cats = set(i.get('announcementCategory') for i in items2)
            print("Available categories in response:", list(cats)[:20])
            
except Exception as e:
    print(f"EXCEPTION: {e}")
