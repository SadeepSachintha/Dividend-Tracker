import mysql.connector
import os
from dotenv import load_dotenv

load_dotenv()

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")
DB_NAME = os.getenv("DB_DATABASE", "dividend_tracker") # Try the name from laravel .env

try:
    conn = mysql.connector.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME
    )
    cursor = conn.cursor()
    cursor.execute("DESCRIBE announcements")
    print("Announcements Table:")
    for row in cursor.fetchall():
        print(row)
        
    cursor.execute("SELECT symbol FROM announcements LIMIT 5")
    print("\nExisting Symbols:")
    for row in cursor.fetchall():
        print(row)
        
    conn.close()
except Exception as e:
    print(f"FAILED to connect: {e}")
