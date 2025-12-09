# Vorte Proxies – Automated Proxy Validation & Management Platform

Vorte Proxies is an automated system designed to collect, validate, enrich, and organize large volumes of proxies.
The project demonstrates a complete ETL-style automation pipeline using Python, Cron Jobs, SSH orchestration,
GeoIP enrichment, and MySQL integration.

This repository serves as a technical presentation of the system architecture and workflow.

---

## Demo Video

A presentation video created for clients, showing the internal workflow and the overall logic of the platform:

Full Project Overview & Workflow:
https://www.youtube.com/watch?v=r5iQPuOCLUc

---

## 1. Overview

The automated pipeline:

- downloads proxy lists from public sources  
- validates connectivity through controlled network checks  
- enriches each proxy with GeoIP (country, city) and ASN/ISP metadata  
- generates structured JSON output  
- transfers processed data securely via SSH  
- imports everything into MySQL on the production server  
- updates cached proxy sets automatically on a schedule  

The full version of the platform includes a dashboard for browsing, filtering, and downloading proxies.

---

## 2. System Architecture

                 ┌────────────────────────────┐
                 │        Cron Scheduler       │
                 │         (every 3 hours)     │
                 └──────────────┬──────────────┘
                                │  SSH
                                ▼
                       ┌─────────────────────┐
                       │       main.py       │
                       │    (orchestrator)   │
                       └──────────┬──────────┘
                                  │
        ┌─────────────────────────┼───────────────────────────┐
        │                         │                           │
        ▼                         ▼                           ▼
 ┌──────────────┐        ┌────────────────┐          ┌───────────────────┐
 │ get_proxy.py │        │ check_proxy.py │          │ json_vorte.py     │
 │ downloads    │        │ validates       │          │ enriches with     │
 │ lists        │        │ proxies        │          │ GeoIP + ASN       │
 └──────────────┘        └────────────────┘          └───────────────────┘

                                  │
                                  ▼
                       ┌──────────────────────┐
                       │  transfer_proxy.py   │
                       │      (SSH upload)    │
                       └──────────┬───────────┘
                                  │
                                  ▼
         cPanel Import → JSON → MySQL → Cached Rotation (40 proxies/hour)
                                  │
                                  ▼
                        User Dashboard (HTML / JS)


## 3. Key Features

### Automated Backend Pipeline
- Scheduled proxy acquisition  
- Supports SOCKS5 / SOCKS4 / HTTP / HTTPS  
- Real-time validation with timeout  
- GeoIP enrichment (MaxMind MMDB)  
- ASN + organization lookup  
- JSON ETL generation  
- Secure SSH-based deployment  

### User Platform (Full Version)
- Account system  
- Proxy browser with filters  
- Daily download quota  
- Selection-based export  
- “My Proxy List” view  

### Security
- SSH key authentication  
- Isolated automation pipeline  
- Protected SQL import/update endpoints  

---

## 4. Technology Stack

Backend & Automation:
- Python 3  
- Cron Jobs  
- Flask  
- SSH automation  
- MaxMind GeoIP2  
- JSON ETL pipeline  

Database:
- MySQL  

Frontend:
- HTML / CSS / JavaScript  

Hosting:
- cPanel  

---

## 5. Example Output

{
  "type": "SOCKS5",
  "country": "South Korea",
  "city": "Seoul",
  "ip": "8.213.129.15",
  "port": "9992",
  "asn": "45102",
  "organization": "Alibaba US Technology"
}

---

## 6. Project Status

This repository represents a functional demonstration of the Vorte Proxies automation pipeline.
Development is ongoing, and additional features will be added in future versions.

---

## 7. License

This project is provided for technical demonstration purposes only.
Commercial use or redistribution requires explicit permission.

---

## 8. Summary

Vorte Proxies showcases a complete automated workflow for large-scale proxy processing:
ETL automation, proxy validation, metadata enrichment, secure deployment, and a user-facing interface.

