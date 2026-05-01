import requests
import json

URL = "https://www.cse.lk/api/approvedAnnouncement"
PAYLOAD = {
    "selectedCompany": [],
    "selectedCategory": ["CASH DIVIDEND"],
    "fromDate": "",
    "toDate": ""
}

HEADERS = {
    "Accept": "application/json, text/plain, */*",
    "Content-Type": "application/json",
    "Origin": "https://www.cse.lk",
    "Referer": "https://www.cse.lk/announcements",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
}

try:
    print(f"Sending request to {URL}...")
    response = requests.post(URL, json=PAYLOAD, headers=HEADERS, timeout=15)
    print(f"Status: {response.status_code}")
    if response.status_code == 200:
        data = response.json()
        # The subagent said it returns a list of announcements. 
        # But earlier I saw a dict with 'approvedAnnouncements'.
        if isinstance(data, dict):
            items = data.get('approvedAnnouncements', [])
        else:
            items = data
            
        print(f"Total items received: {len(items)}")
        
        # Filter for dividends in Python just in case
        div_items = [i for i in items if i.get('announcementCategory') == 'CASH DIVIDEND']
        print(f"Items with category 'CASH DIVIDEND': {len(div_items)}")
        
        if div_items:
            for i in div_items[:3]:
                print(json.dumps(i, indent=2))
        elif items:
            print("First 3 items received (regardless of category):")
            for i in items[:3]:
                print(f"- {i.get('company')} | {i.get('announcementCategory')}")
                
except Exception as e:
    print(f"EXCEPTION: {e}")
