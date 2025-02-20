"""
Proxy Updater
Author: [Andrei Sorin] (https://github.com/PhoenixZuko)

Description:
This script downloads the latest SOCKS5, SOCKS4, HTTP, and HTTPS proxies from the
KangProxy project and saves them in a structured folder for easier management.

Acknowledgments:
Special thanks to officialputuid for providing the proxy lists.
GitHub: https://github.com/officialputuid/KangProxy
"""

import os
import requests
import logging

# Specifică noul folder pentru log-uri
log_dir = "logs"  # Poate fi orice nume sau locație dorită
os.makedirs(log_dir, exist_ok=True)  # Creează directorul dacă nu există

# Creează calea completă pentru fișierul de log
log_file_path = os.path.join(log_dir, "get_proxy_debug.log")

# Configurează logging-ul
logging.basicConfig(
    filename=log_file_path,
    level=logging.DEBUG,
    format="%(asctime)s %(levelname)s %(message)s"
)

# Exemplu de logare pentru a verifica funcționalitatea
logging.info("Logging system initialized successfully.")

# URLs to download proxies from
socks5_url = "https://raw.githubusercontent.com/officialputuid/KangProxy/refs/heads/KangProxy/socks5/socks5.txt"
socks4_url = "https://raw.githubusercontent.com/officialputuid/KangProxy/refs/heads/KangProxy/socks4/socks4.txt"
http_url = "https://raw.githubusercontent.com/officialputuid/KangProxy/refs/heads/KangProxy/http/http.txt"
https_url = "https://raw.githubusercontent.com/officialputuid/KangProxy/refs/heads/KangProxy/https/https.txt"


# Directory to save the downloaded proxy files
output_dir = "PROXY_UPDATED"
os.makedirs(output_dir, exist_ok=True)

# Filenames to save the downloaded content
socks5_file = os.path.join(output_dir, "socks5_proxies.txt")
socks4_file = os.path.join(output_dir, "socks4_proxies.txt")
http_file = os.path.join(output_dir, "http_proxies.txt")
https_file = os.path.join(output_dir, "https_proxies.txt")

def update_proxies(url, filename):
    """
    Downloads proxies from a given URL and saves them to a specified file.

    Args:
        url (str): The URL to download the proxies from.
        filename (str): The file where the proxies will be saved.
    """
    try:
        response = requests.get(url)
        response.raise_for_status()
        with open(filename, "w") as file:
            file.write(response.text)
        print(f"Updated {filename} successfully.")
    except requests.exceptions.RequestException as e:
        print(f"Failed to update {filename}: {e}")

# Update the proxy files
update_proxies(socks5_url, socks5_file)
update_proxies(socks4_url, socks4_file)
update_proxies(http_url, http_file)
update_proxies(https_url, https_file)