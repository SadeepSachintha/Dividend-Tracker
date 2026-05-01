import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": [],
    "fromDate": "2026-04-01",
    "toDate": "2026-05-01"
}

HEADERS = {
    "Content-Type": "application/json",
    "Referer": "https://www.cse.lk/announcements",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
}

try:
    print(f"Testing URL: {URL}")
    response = requests.post(URL, json=PAYLOAD, headers=HEADERS, timeout=15)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        data = response.json()
        print("SUCCESS!")
        # Print first 2 items to see structure
        if isinstance(data, list):
            print(f"Found {len(data)} items.")
            if len(data) > 0:
                print("First item structure:", json.dumps(data[0], indent=2))
        else:
            print("Response is not a list. Structure:", type(data))
            print(json.dumps(data, indent=2)[:500])
    else:
        print(f"FAILED: {response.text}")
except Exception as e:
    print(f"EXCEPTION: {e}")
