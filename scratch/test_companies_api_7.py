import requests
import json

url = "https://www.cse.lk/api/tradeSummary"
headers = {"User-Agent": "Mozilla/5.0"}
res = requests.post(url, json={}, headers=headers)
data = res.json()

req = data.get('reqTradeSummery', [])
print(f"reqTradeSummery length: {len(req)}")
if len(req) > 0:
    print("First item keys:", list(req[0].keys()))
    print("First item symbol:", req[0].get('symbol'))
    print("First item companyName:", req[0].get('name'))
