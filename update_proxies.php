<?php
// Database configuration
$host = "localhost";
$dbname = "wfdjveoc_vorte";
$username = "wfdjveoc_proxy";
$password = "D3pVmmB0bx06";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Truncate the `proxies` table
    $pdo->exec("TRUNCATE TABLE proxies");
    echo "The `proxies` table has been successfully cleared.<br>";

    // Path to the JSON file
    $json_file = "/home/wfdjveoc/public_html/vorte.eu/proxies/data_json_proxy/flat_proxies.json";

    // Read the JSON file
    if (!file_exists($json_file)) {
        throw new Exception("JSON file not found at the path: $json_file");
    }
    $json_data = file_get_contents($json_file);
    $proxies = json_decode($json_data, true);

    if (!$proxies) {
        throw new Exception("Failed to decode JSON file. Please check the file's structure.");
    }

    // Prepare SQL query for insertion
    $stmt = $pdo->prepare("
        INSERT INTO proxies (type, country, city, ip, port, asn, organization, date_added)
        VALUES (:type, :country, :city, :ip, :port, :asn, :organization, :date_added)
    ");

    // Iterate through the proxies and insert them into the database
    foreach ($proxies as $proxy) {
        // Validate and truncate fields as necessary
        $port = substr($proxy['port'], 0, 5); // Ensure port is not longer than 5 characters
        $organization = substr($proxy['organization'], 0, 100); // Truncate organization to 100 characters

        // Skip invalid ports (non-numeric or too long)
        if (!is_numeric($port)) {
            echo "Error: Port value '{$proxy['port']}' is invalid.<br>";
            continue; // Skip this entry
        }

        $stmt->execute([
            ':type' => $proxy['type'],
            ':country' => $proxy['country'],
            ':city' => $proxy['city'],
            ':ip' => $proxy['ip'],
            ':port' => $port,
            ':asn' => $proxy['asn'],
            ':organization' => $organization,
            ':date_added' => date('Y-m-d H:i:s') // Current timestamp
        ]);
    }

    echo "All proxies have been successfully imported!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
