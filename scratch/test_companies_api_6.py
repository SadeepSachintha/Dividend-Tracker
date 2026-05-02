import requests
import json

url = "https://www.cse.lk/api/tradeSummary"
headers = {"User-Agent": "Mozilla/5.0"}
res = requests.post(url, json={}, headers=headers)
data = res.json()

print(f"Type: {type(data)}")
if isinstance(data, dict):
    print(f"Keys: {list(data.keys())}")
    req = data.get('reqTradeSummary', [])
    print(f"reqTradeSummary type: {type(req)}")
    if isinstance(req, list):
        print(f"reqTradeSummary length: {len(req)}")
        if len(req) > 0:
            print("First item keys:", list(req[0].keys()))
            print("First item symbol:", req[0].get('symbol'))
