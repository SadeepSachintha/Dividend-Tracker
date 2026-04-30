import requests
import json

URL = "https://www.cse.lk/api/smd/getAnnouncementData"
PAYLOAD = '{"selectedCompany":[],"selectedCategory":["CORPORATE DISCLOSURE"],"fromDate":"","toDate":""}'

HEADERS = {
    "Content-Type": "application/json",
    "x-api-key": "Q3NlMTIzQXBp",
    "Origin": "https://www.cse.lk",
    "Referer": "https://www.cse.lk/announcements",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "X-Requested-With": "XMLHttpRequest"
}

try:
    print("Trying with raw JSON string and no spaces...")
    response = requests.post(URL, data=PAYLOAD, headers=HEADERS, timeout=10)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        print("SUCCESS!")
        print(response.text[:200])
    else:
        print(f"FAILED: {response.text}")
except Exception as e:
    print(f"EXCEPTION: {e}")
