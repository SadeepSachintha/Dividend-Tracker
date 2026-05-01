import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": [],
    "fromDate": "2025-10-01",
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
        cats = set(i.get('announcementCategory') for i in items)
        print("Total items:", len(items))
        print("Unique categories found:", sorted(list(cats)))
except Exception as e:
    print(f"EXCEPTION: {e}")
