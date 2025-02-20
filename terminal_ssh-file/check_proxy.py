"""
Proxy Checker
Author: [Andrei Sorin] (https://github.com/PhoenixZuko)

Description:
This script reads proxy files from the PROXY_UPDATED folder, performs basic 
checks on the proxies, and saves the valid results into the PROXY_CHECKED folder.

Acknowledgments:
1. Proxy lists sourced from the KangProxy project by officialputuid.
   GitHub: https://github.com/officialputuid/KangProxy
2. Assisted by OpenAI's ChatGPT for code implementation and project structuring.
"""

import os
import requests
from datetime import datetime
from concurrent.futures import ThreadPoolExecutor, as_completed

# Directories for input and output
input_dir = "PROXY_UPDATED"
output_dir = "PROXY_CHECKED"
os.makedirs(output_dir, exist_ok=True)  # Create the output directory if it doesn't exist

# Timeout and threading settings
timeout = 5  # Timeout for checking proxies (in seconds)
max_threads = 30  # Number of threads to run in parallel

def log_message(message, log_file="proxy_checker.log"):
    """
    Log a message to a file and print it to the console.
    """
    with open(log_file, "a") as file:
        file.write(f"{datetime.now()}: {message}\n")
    print(message)

def check_proxy(proxy, protocol):
    """
    Verifică dacă un proxy permite o conexiune la un URL de test.

    Args:
        proxy (str): Proxy-ul de testat (e.g., "127.0.0.1:8080").
        protocol (str): Protocolul utilizat (e.g., "http", "https", "socks5").

    Returns:
        tuple: (proxy, bool) unde bool este True dacă proxy-ul este valid.
    """
    test_url = "http://example.com"  # URL simplu pentru testare
    proxies = {protocol: f"{protocol}://{proxy}"}

    try:
        response = requests.get(test_url, proxies=proxies, timeout=5)
        if response.status_code == 200:
            print(f"Valid proxy: {proxy}")
            return proxy, True
        else:
            print(f"Proxy {proxy} returned status code: {response.status_code}")
    except requests.exceptions.ProxyError:
        print(f"ProxyError: Proxy {proxy} failed.")
    except requests.exceptions.ConnectTimeout:
        print(f"ConnectTimeout: Proxy {proxy} timed out.")
    except requests.exceptions.RequestException as e:
        print(f"RequestException: Proxy {proxy} failed with error {e}")
    return proxy, False


def determine_protocol(filename):
    """
    Determines the proxy protocol based on the file name.

    Args:
        filename (str): The name of the proxy file.

    Returns:
        str: The protocol to use (e.g., "http", "https", "socks4", "socks5").
    """
    if "http" in filename.lower():
        return "http"
    elif "https" in filename.lower():
        return "https"
    elif "socks4" in filename.lower():
        return "socks4"
    elif "socks5" in filename.lower():
        return "socks5"
    else:
        log_message(f"Protocol not specified in {filename}. Defaulting to HTTP.")
        return "http"

def filter_already_checked(proxies, output_path):
    """
    Filters out proxies that have already been checked.

    Args:
        proxies (list): List of proxies to check.
        output_path (str): Path to the output file for previously checked proxies.

    Returns:
        list: Filtered list of proxies that haven't been checked yet.
    """
    if os.path.exists(output_path):
        with open(output_path, "r") as outfile:
            already_checked = set(line.strip() for line in outfile)
        return [proxy for proxy in proxies if proxy not in already_checked]
    return proxies

def process_proxy_file(filename):
    """
    Processes a single proxy file, checking each proxy for validity in parallel.

    Args:
        filename (str): The name of the file to process.

    Returns:
        None
    """
    input_path = os.path.join(input_dir, filename)
    output_path = os.path.join(output_dir, f"checked_{filename}")

    protocol = determine_protocol(filename)

    proxies = []
    with open(input_path, "r") as infile:
        for line in infile:
            proxy = line.strip()
            if ":" in proxy:  # Ensure valid proxy format
                proxies.append(proxy)

    proxies = filter_already_checked(proxies, output_path)

    if not proxies:
        log_message(f"No new proxies to check in {filename}.")
        return

    valid_proxies = []

    # Use ThreadPoolExecutor for parallel proxy checking
    with ThreadPoolExecutor(max_threads) as executor:
        future_to_proxy = {executor.submit(check_proxy, proxy, protocol): proxy for proxy in proxies}

        for future in as_completed(future_to_proxy):
            proxy, is_valid = future.result()
            if is_valid:
                valid_proxies.append(proxy)

    # Write valid proxies to output file
    with open(output_path, "w") as outfile:
        outfile.write("\n".join(valid_proxies))
    log_message(f"Finished processing {filename}. Valid proxies saved to {output_path}.")

def main():
    """
    Main function to process all proxy files in the input directory.
    """
    if not os.path.exists(input_dir):
        log_message(f"Input directory '{input_dir}' does not exist. Exiting.")
        return

    for filename in os.listdir(input_dir):
        if filename.endswith(".txt"):
            log_message(f"Processing file: {filename}")
            process_proxy_file(filename)

if __name__ == "__main__":
    main()  