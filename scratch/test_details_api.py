import requests
import json

ID = "36830" # From subagent's report
URLS = [
    f"https://www.cse.lk/api/getAnnouncementDetails?id={ID}",
    f"https://www.cse.lk/api/announcementDetails?id={ID}",
    f"https://www.cse.lk/api/getAnnouncementDetail?id={ID}",
    f"https://www.cse.lk/api/smd/getAnnouncementDetails?id={ID}"
]

HEADERS = {
    "Content-Type": "application/json",
    "Referer": "https://www.cse.lk/announcements",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
}

for url in URLS:
    try:
        print(f"Testing URL: {url}")
        response = requests.get(url, headers=HEADERS, timeout=10)
        print(f"Status: {response.status_code}")
        if response.status_code == 200:
            print("SUCCESS!")
            print(json.dumps(response.json(), indent=2)[:500])
            break
        else:
            print(f"FAILED: {response.text[:100]}")
    except Exception as e:
        print(f"EXCEPTION: {e}")
