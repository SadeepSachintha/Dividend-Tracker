import requests

url = "https://www.cse.lk/api/todaySharePrice"
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json, text/plain, */*",
    "User-Agent": "Mozilla/5.0"
}

payloads = [
    {"limit": 1000},
    {"page": 1, "size": 1000},
    {"start": 0, "length": 1000},
    {"pageSize": 1000},
]

for p in payloads:
    res = requests.post(url, json=p, headers=headers)
    data = res.json()
    if isinstance(data, list):
        print(f"Payload {p} returned {len(data)} items")
    elif isinstance(data, dict):
        print(f"Payload {p} returned dict: {list(data.keys())}")
    else:
        print(f"Payload {p} returned {type(data)}")
