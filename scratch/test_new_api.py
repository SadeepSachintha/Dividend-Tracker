import requests
import json

URL = "https://www.cse.lk/api/smdAnnouncement"
PAYLOAD = {
    "allCategories": True,
    "allCompanies": True,
    "companyIds": [],
    "categories": [],
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
        print("SUCCESS! Data keys:", data.keys())
        # Print a sample item
        if isinstance(data, list) and len(data) > 0:
            print("First item:", json.dumps(data[0], indent=2))
        elif isinstance(data, dict):
            # Check for common keys
            for key in data.keys():
                if isinstance(data[key], list) and len(data[key]) > 0:
                    print(f"Sample from '{key}':", json.dumps(data[key][0], indent=2))
                    break
    else:
        print(f"FAILED: {response.text}")
except Exception as e:
    print(f"EXCEPTION: {e}")
