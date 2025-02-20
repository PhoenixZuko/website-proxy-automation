"""
File Transfer Script
Author: [Andrei Sorin] (https://github.com/PhoenixZuko)
Assisted by: OpenAI's ChatGPT for code implementation and project structuring

Description:
This script transfers the `flat_proxies.json` file to a specified cPanel directory
via SCP. It first tests the SSH connection to ensure the server is accessible, then proceeds
with the file transfer.

Acknowledgments:
1. SSH configuration and key-based authentication guidance sourced from OpenSSH documentation.
2. Assisted by OpenAI's ChatGPT for code implementation and optimization.
"""

import subprocess

# SSH Configuration
cpanel_user = "wfdjveoc"  # Replace with your cPanel username
cpanel_host = "88.119.163.4"  # Replace with your cPanel IP or domain
ssh_key_path = "~/.ssh/id_rsa_cpanel"  # Path to the private SSH key
ssh_port = "10424"  # SSH port specific to cPanel
local_file = "flat_proxies.json"  # Local file to be transferred
destination_path = f"/home/{cpanel_user}/public_html/vorte.eu/proxies/data_json_proxy"  # Destination path on cPanel

# Test SSH Connection
def test_ssh_connection():
    try:
        print("Testing SSH connection...")
        subprocess.run(
            ["ssh", "-i", ssh_key_path, "-p", ssh_port, f"{cpanel_user}@{cpanel_host}", "ls"],
            check=True
        )
        print("SSH connection successful!")
    except subprocess.CalledProcessError:
        print("SSH connection failed.")
        exit(1)

# Transfer File to cPanel
def transfer_file():
    try:
        print(f"Transferring file {local_file} to cPanel...")
        subprocess.run(
            [
                "scp", "-i", ssh_key_path, "-P", ssh_port, local_file,
                f"{cpanel_user}@{cpanel_host}:{destination_path}"
            ],
            check=True
        )
        print("File transferred successfully!")
    except subprocess.CalledProcessError:
        print("File transfer failed.")
        exit(1)

if __name__ == "__main__":
    # Test SSH connection
    test_ssh_connection()

    # Transfer the file to cPanel
    transfer_file()
