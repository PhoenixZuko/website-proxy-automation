# Vorte Proxies - Platforma Automatizata pentru Validarea Proxy-urilor

**Vorte Proxies** este un proiect inovator care automatizeaza procesul de gestionare si validare a listelor de proxy. Folosind **Python, Cron Jobs, SSH, si baze de date SQL**, platforma permite utilizatorilor sa acceseze proxy-uri verificate si sa beneficieze de functionalitati avansate pentru administrare.

 **Proiect disponibil live:** [Vorte Proxies](https://vorte.eu/vorte.eu/proxies/)

---

## ** Statusul Curent al Proiectului**

 **Partial functional** – Utilizatorii pot deja sa isi creeze conturi si sa se autentifice cu urmatoarele credentiale de test:
   - **Username:** test
   - **Parola:** 1  
 **Dezvoltarea este in curs** – Se adauga constant noi functionalitati pentru imbunatatirea platformei.

---

## **🔹 Fluxul Automatizat de Procesare**

### **1️ Initiere prin Cron Jobs**
-  **Frecventa:** La fiecare **3 ore**, un **Cron Job** porneste procesul.
-  **Comanda executata:**
  ```sh
  ssh root@87.120.114.174 "python3 /root/Bubu/main.py"
  ```
-  **Autentificare securizata:** Foloseste **SSH Key Authentication** pentru conectare fara parola.

### **2️ Scriptul Principal – main.py**
- Acesta orchestreaza executia altor scripturi, asigurându-se ca fiecare are suficient timp sa finalizeze:
  - `get_proxy.py` – **300 secunde (5 min)**
  - `check_proxy.py` – **1800 secunde (30 min)**
  - `json_vorte.py` – **300 secunde (5 min)**
  - `transfer_proxy.py` – **300 secunde (5 min)**

---

## ** Procesul de Validare a Proxy-urilor**

### **1 get_proxy.py – Descarcarea listelor de proxy**
-  Obtine liste din surse publice si le salveaza local.
-  **Tipuri de proxy descarcate:**
  - SOCKS5
  - SOCKS4
  - HTTP
  - HTTPS

### **2️ check_proxy.py – Validarea proxy-urilor**
-  Verifica functionalitatea proxy-urilor descarcate.
-  Asigura ca exista un numar suficient de proxy-uri valabile pentru teste.

### **3️ json_vorte.py – Procesare si imbogatire a datelor**
-  Adauga metadate pentru fiecare proxy folosind:
  - **MaxMind MMDB** pentru geolocatie (tara, oras, ASN).
  - **ASN-INFO.txt** pentru numele organizatiei.
-  **Datele sunt salvate in format JSON:**
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

### **4️ transfer_proxy.py – Transferul datelor**
-  Transfera fisierul JSON generat catre serverul **cPanel** pentru integrare in baza de date SQL.
-  Conexiune securizata prin **SSH Key Authentication**.

---

## ** Fluxul pe cPanel**

### **1️ Importarea datelor in SQL**
-  **Cron Job:**
  ```sh
  curl -s https://vorte.eu/proxies/cached_proxies.php > /dev/null
  ```
-  **Functie:** Importa proxy-urile din JSON in baza de date SQL `proxies`.

### **2️ Actualizare si selectie proxy random**
-  **Cron Job:**
  ```sh
  curl -s https://vorte.eu/proxies/update_proxies.php > /dev/null
  ```
-  **Functie:** Selecteaza aleatoriu **40 proxy-uri** la fiecare ora si le salveaza in `cached_proxies` pentru afisare.

---

## ** Functionalitati pentru Utilizatorii inregistrati**

 **Vizualizare lista completa de proxy-uri**
-  **Filtrare avansata** dupa tip, tara, oras, ASN, organizatie.

**Descarcare gratuita a pâna la 30 proxy-uri zilnic**
-  **Functie de shopping cart** pentru selectie si descarcare.

**Acces la „My Proxy List”**
-  **Proxy-urile descarcate sunt afisate fara mascarea IP-urilor.**

---

## ** Tehnologii Folosite**
- **Backend:** Python, Flask
- **Automatizare:** Cron Jobs
- **Securitate:** SSH Key Authentication
- **Baze de date:** MySQL, JSON
- **Geolocatie:** MaxMind MMDB
- **Interfata utilizator:** HTML, CSS, JavaScript (in dashboard)
- **Hosting:** cPanel

---

## ** Licenta & Drepturi**
- **Proiect in dezvoltare activa** – Se vor adauga noi functionalitati.
- **Acces public limitat** – Utilizatorii trebuie sa se inregistreze pentru a beneficia de toate functionalitatile.

 **Vorte Proxies nu este doar un simplu site, ci un sistem complet de gestionare automatizata a proxy-urilor!** 

