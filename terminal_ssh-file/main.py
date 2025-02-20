import os

os.chdir(os.path.dirname(os.path.abspath(__file__)))

import subprocess
from datetime import datetime

def log_message(log_file, message):
    """
    Logs a message to a file with a timestamp.
    """
    with open(log_file, 'a') as file:
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        file.write(f"[{timestamp}] {message}\n")

def run_scripts(scripts, log_folder, timeouts):
    """
    Runs a list of scripts sequentially with individual timeouts and logs their progress.

    Args:
        scripts (list): List of script names to execute.
        log_folder (str): Folder to store log files.
        timeouts (dict): Dictionary of timeouts for each script.
    """
    central_log = os.path.join(log_folder, "central_log.txt")
    log_message(central_log, "Starting script execution")

    for script in scripts:
        print(f"Running {script}...")
        log_message(central_log, f"Running script: {script}")

        log_file = os.path.join(log_folder, f"{os.path.basename(script)}.log")
        try:
            with open(log_file, "a") as log_output:
                process = subprocess.run(
                    ["python3", script],
                    stdout=log_output,
                    stderr=subprocess.STDOUT,
                    check=True,
                    timeout=timeouts.get(script, 300)  # Default timeout: 300 seconds
                )
            log_message(central_log, f"Script {script} completed successfully.")
            print(f"Script {script} completed successfully.")
        except subprocess.TimeoutExpired:
            log_message(central_log, f"Script {script} timed out.")
            print(f"Script {script} timed out.")
            break
        except subprocess.CalledProcessError as e:
            log_message(central_log, f"Script {script} failed. Check {log_file} for details.")
            print(f"Script {script} failed. Check logs for details.")
            break

if __name__ == "__main__":
    # List of scripts to execute
    scripts = ["get_proxy.py", "check_proxy.py", "json_vorte.py", "transfer_proxy.py"]

    # Folder to store logs
    log_folder = "logs"
    os.makedirs(log_folder, exist_ok=True)

    # Timeouts for each script (in seconds)
    timeouts = {
        "get_proxy.py": 300,  # 5 minutes
        "check_proxy.py": 1800,  # 30 minutes
        "json_vorte.py": 300,  # 5 minutes
        "transfer_proxy.py": 300  # 5 minutes
    }

    # Run scripts in sequence
    run_scripts(scripts, log_folder, timeouts)
