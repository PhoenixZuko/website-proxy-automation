# Vorte Proxies - Automated Proxy Validation Platform

Vorte Proxies is an innovative project that automates the management and validation of proxy lists. Using Python, Cron Jobs, SSH, and SQL databases, the platform allows users to access verified proxies and benefit from advanced administrative features.

Live project: Vorte Proxies

---

## **Current Project Status**
- **Partially functional** – Users can already create accounts and log in using the following test credentials:
  
  **Username:** test  
  **Password:** 1  
- Development is ongoing – New features are being added constantly to improve the platform.

---

## **Automated Processing Workflow**

### **1. Initiation via Cron Jobs**
- **Frequency:** Every 3 hours, a Cron Job starts the process.
- **Executed command:**
   ```sh
   ssh root@87.120.114.174 "python3 /root/Bubu/main.py"
   ```
- **Secure authentication:** Uses SSH Key Authentication for password-less login.

### **2. Main Script – main.py**
This script orchestrates the execution of other scripts, ensuring each has enough time to complete:

- **get_proxy.py** – 300 seconds (5 min)
- **check_proxy.py** – 1800 seconds (30 min)
- **json_vorte.py** – 300 seconds (5 min)
- **transfer_proxy.py** – 300 seconds (5 min)

---

## **Proxy Validation Process**

### **1. get_proxy.py – Downloading Proxy Lists**
- Retrieves lists from public sources and saves them locally.
- Types of downloaded proxies:
  - SOCKS5
  - SOCKS4
  - HTTP
  - HTTPS

### **2. check_proxy.py – Proxy Validation**
- Verifies the functionality of the downloaded proxies.
- Ensures there are enough valid proxies available for testing.

### **3. json_vorte.py – Data Processing and Enrichment**
- Adds metadata for each proxy using:
  - MaxMind MMDB for geolocation (country, city, ASN).
  - ASN-INFO.txt for the organization name.
- Data is stored in JSON format:
  ```json
  {
    "type": "SOCKS5",
    "country": "South Korea",
    "city": "Seoul",
    "ip": "8.213.129.15",
    "port": "9992",
    "asn": "45102",
    "organization": "Alibaba US Technology"
  }
  ```

### **4. transfer_proxy.py – Data Transfer**
- Transfers the generated JSON file to the cPanel server for SQL database integration.
- Secure connection via SSH Key Authentication.

---

## **cPanel Workflow**

### **1. Importing Data into SQL**
- **Cron Job:**
  ```sh
  curl -s https://vorte.eu/proxies/cached_proxies.php > /dev/null
  ```
- **Function:** Imports proxies from JSON into the `proxies` SQL database.

### **2. Proxy Update and Random Selection**
- **Cron Job:**
  ```sh
  curl -s https://vorte.eu/proxies/update_proxies.php > /dev/null
  ```
- **Function:** Selects 40 random proxies every hour and saves them in `cached_proxies` for display.

---

## **Features for Registered Users**
- **View complete proxy list**
- **Advanced filtering** by type, country, city, ASN, and organization.
- **Free download of up to 30 proxies daily**
  - Shopping cart functionality for selection and download.
- **Access to "My Proxy List"**
  - Downloaded proxies are displayed without IP masking.

---

## **Technologies Used**
- **Backend:** Python, Flask
- **Automation:** Cron Jobs
- **Security:** SSH Key Authentication
- **Databases:** MySQL, JSON
- **Geolocation:** MaxMind MMDB
- **User Interface:** HTML, CSS, JavaScript (dashboard)
- **Hosting:** cPanel

---

## **License & Rights**
- **Project under active development** – New features will be added.
- **Limited public access** – Users must register to access all functionalities.

  **Vorte Proxies is not just a simple website but a complete automated proxy management system!**

