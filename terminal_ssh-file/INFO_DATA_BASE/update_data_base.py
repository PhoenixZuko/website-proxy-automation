import requests
import os

# Dicționar care conține link-urile și fișierele locale
files_to_download = {
    'https://raw.githubusercontent.com/sapics/ip-location-db/main/geolite2-country/geolite2-country-ipv4.csv': 'geolite2-country-ipv4.csv',
    'https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-ASN.mmdb': 'GeoLite2-ASN.mmdb',
    'https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-City.mmdb': 'GeoLite2-City.mmdb',
    'https://git.io/GeoLite2-Country.mmdb': 'GeoLite2-Country.mmdb'
}

# Funcție pentru descărcarea fișierului
def download_file(url, dest):
    response = requests.get(url)
    if response.status_code == 200:
        with open(dest, 'wb') as file:
            file.write(response.content)
        print(f'Fișierul {dest} a fost actualizat cu succes!')
    else:
        print(f'Eroare la descărcarea fișierului {dest}: {response.status_code}')

# Funcție pentru actualizarea fișierelor
def update_files():
    for url, file_name in files_to_download.items():
        if os.path.exists(file_name):
            print(f'Actualizare fișier {file_name}...')
            download_file(url, file_name)
        else:
            print(f'Fișierul {file_name} nu există. Descărcare inițială...')
            download_file(url, file_name)

# Rulează funcția de actualizare
if __name__ == '__main__':
    update_files()

