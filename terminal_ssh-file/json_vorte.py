import os
import geoip2.database
import json


def load_asn_mapping(asn_file_path):
    """
    Load ASN mappings from a file.
    Each line in the file should be in the format: ASN:Description.
    """
    asn_mapping = {}
    with open(asn_file_path, 'r') as asn_file:
        for line in asn_file:
            parts = line.strip().split(':', 1)
            if len(parts) == 2:
                asn_number = parts[0].strip()
                asn_name = parts[1].strip()
                asn_mapping[asn_number] = asn_name
    return asn_mapping


def process_proxies_flat(input_folder, output_file, country_reader, asn_reader, city_reader, asn_mapping):
    """
    Processes proxy files and saves data into a flat JSON file.
    Each entry will contain all the necessary fields for direct SQL import.

    Args:
        input_folder (str): The folder containing checked proxy files.
        output_file (str): The path to the output JSON file.
        country_reader (geoip2.database.Reader): GeoIP2 Country database reader.
        asn_reader (geoip2.database.Reader): GeoIP2 ASN database reader.
        city_reader (geoip2.database.Reader): GeoIP2 City database reader.
        asn_mapping (dict): Mapping of ASN numbers to descriptions.
    """
    proxies = []  # Flat list to hold all proxies

    for filename in os.listdir(input_folder):
        if filename.startswith("checked_") and filename.endswith(".txt"):
            # Extract SOCKS type from the filename (e.g., SOCKS5, SOCKS4, HTTPS)
            socks_type = filename.split("_")[1].upper()

            with open(os.path.join(input_folder, filename), 'r') as proxy_file:
                for line in proxy_file:
                    line = line.strip()
                    if not line or ':' not in line:
                        continue

                    ip, port = line.split(':', 1)
                    try:
                        # Retrieve country, city, and ASN information for the IP
                        country_response = country_reader.country(ip)
                        country_name = country_response.country.name or "Unknown Country"

                        city_response = city_reader.city(ip)
                        city_name = city_response.city.name or "Unknown City"

                        asn_response = asn_reader.asn(ip)
                        asn_number = str(asn_response.autonomous_system_number)
                        asn_name = asn_mapping.get(asn_number, asn_response.autonomous_system_organization or f"ASN{asn_number}")

                        # Append the proxy to the flat list
                        proxies.append({
                            "type": socks_type,
                            "country": country_name,
                            "city": city_name,
                            "ip": ip,
                            "port": port,
                            "asn": asn_number,
                            "organization": asn_name
                        })

                        print(f"Processed {ip}:{port} -> {socks_type}, {city_name}, {country_name} ({asn_name})")

                    except geoip2.errors.AddressNotFoundError:
                        print(f"IP {ip} not found in the database.")

    # Save all proxies to a single JSON file
    with open(output_file, 'w', encoding='utf-8') as json_file:
        json.dump(proxies, json_file, indent=4, ensure_ascii=False)

    print(f"All proxies saved to {output_file}")


if __name__ == "__main__":
    # Define paths
    input_folder = "PROXY_CHECKED"
    output_file = "flat_proxies.json"  # Output JSON file

    # Paths to GeoIP2 databases
    info_db_folder = "INFO_DATA_BASE"
    country_db_path = os.path.join(info_db_folder, "GeoLite2-Country.mmdb")
    asn_db_path = os.path.join(info_db_folder, "GeoLite2-ASN.mmdb")
    city_db_path = os.path.join(info_db_folder, "GeoLite2-City.mmdb")
    asn_info_path = os.path.join(info_db_folder, "ASN_INFO.txt")  # Path to ASN info file

    # Load ASN mappings
    asn_mapping = load_asn_mapping(asn_info_path)

    # Initialize GeoIP readers
    with geoip2.database.Reader(country_db_path) as country_reader, \
         geoip2.database.Reader(asn_db_path) as asn_reader, \
         geoip2.database.Reader(city_db_path) as city_reader:

        # Process proxies into a flat JSON file
        process_proxies_flat(input_folder, output_file, country_reader, asn_reader, city_reader, asn_mapping)
