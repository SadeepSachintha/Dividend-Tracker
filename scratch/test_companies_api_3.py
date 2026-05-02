import requests

url = "https://www.cse.lk/api/todaySharePrice"
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json, text/plain, */*",
    "User-Agent": "Mozilla/5.0"
}

res = requests.post(url, json={}, headers=headers)
print(res.json())
