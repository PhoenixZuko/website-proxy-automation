<?php
// Database configuration
$host = "localhost";
$dbname = "wfdjveoc_vorte";
$username = "wfdjveoc_proxy";
$password = "D3pVmmB0bx06";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Șterge datele vechi din tabelul cached_proxies
    $pdo->exec("DELETE FROM cached_proxies");

    // Inserează 40 de proxy-uri random în tabelul cached_proxies
    $query = "INSERT INTO cached_proxies (type, country, city, ip, port, asn, organization, date_added)
              SELECT type, country, city, ip, port, asn, organization, date_added
              FROM proxies
              WHERE city != 'Unknown City'
              ORDER BY RAND()
              LIMIT 40";
    $pdo->exec($query);

    echo "Cached proxies updated successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
