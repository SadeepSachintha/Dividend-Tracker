import requests

url = "https://www.cse.lk/api/todaySharePrice"
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json, text/plain, */*",
    "User-Agent": "Mozilla/5.0"
}

# Test empty payload
response = requests.post(url, json={}, headers=headers)
data = response.json()
print(f"Empty payload returned {len(data)} items")

# Test with high size
response = requests.post(url, json={"size": 1000}, headers=headers)
data = response.json()
if isinstance(data, list):
    print(f"Size 1000 payload returned {len(data)} items")
elif isinstance(data, dict) and 'content' in data:
    print(f"Size 1000 payload returned {len(data['content'])} items in 'content'")
else:
    print(f"Size 1000 payload returned unexpected structure: {type(data)}")
