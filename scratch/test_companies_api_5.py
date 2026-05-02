import requests

endpoints = [
    "https://www.cse.lk/api/allCompany",
    "https://www.cse.lk/api/companyList",
    "https://www.cse.lk/api/companySummary",
    "https://www.cse.lk/api/sectors",
    "https://www.cse.lk/api/tradeSummary",
    "https://www.cse.lk/api/marketSummary",
    "https://www.cse.lk/api/marketStatus"
]
headers = {"User-Agent": "Mozilla/5.0"}

for url in endpoints:
    try:
        res = requests.get(url, headers=headers, timeout=5)
        if res.status_code == 200:
            data = res.json()
            if isinstance(data, list):
                print(f"GET {url} - OK - List length: {len(data)}")
            else:
                print(f"GET {url} - OK - Type: {type(data)}")
        elif res.status_code == 405:
            res = requests.post(url, json={}, headers=headers, timeout=5)
            if res.status_code == 200:
                data = res.json()
                if isinstance(data, list):
                    print(f"POST {url} - OK - List length: {len(data)}")
                else:
                    print(f"POST {url} - OK - Type: {type(data)}")
            else:
                print(f"POST {url} - Failed: {res.status_code}")
        else:
            print(f"GET {url} - Failed: {res.status_code}")
    except Exception as e:
        print(f"{url} - Error: {e}")
