<?php
session_start();
include 'database/db_connect.php'; // Include conexiunea la baza de date

// Verificăm dacă proxy-urile sunt deja salvate în sesiune
if (!isset($_SESSION['proxies'])) {
    try {
        // Selectăm 40 de proxy-uri aleatorii din baza de date
        $query = "SELECT type, country, city, ip, port, asn, organization, date_added FROM cached_proxies ORDER BY RAND() LIMIT 40";
        $stmt = $pdo->query($query);
        $proxies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculăm timpul scurs pentru fiecare proxy și salvăm în sesiune
        foreach ($proxies as &$proxy) {
            $dateAdded = strtotime($proxy['date_added']);
            $timeDiff = time() - $dateAdded;

            if ($timeDiff < 60) {
                $proxy['time_since_added'] = $timeDiff . " seconds ago";
            } elseif ($timeDiff < 3600) {
                $proxy['time_since_added'] = floor($timeDiff / 60) . " min";
            } elseif ($timeDiff < 86400) {
                $proxy['time_since_added'] = floor($timeDiff / 3600) . " hours";
            } else {
                $proxy['time_since_added'] = floor($timeDiff / 86400) . " days";
            }

            // Limităm numărul de caractere pentru Organization
            if (strlen($proxy['organization']) > 20) {
                $proxy['organization'] = substr($proxy['organization'], 0, 20) . '...';
            }
        }

        // Salvăm proxy-urile în sesiune
        $_SESSION['proxies'] = $proxies;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // Dacă sunt salvate în sesiune, le preluăm de acolo
    $proxies = $_SESSION['proxies'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
    .body{
        display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 30px 20px;
    background: linear-gradient(to right, #d4e0ed, #8e97a1);
    }
    </style>
</head>
<body>
    <div>
        <?php include 'includes/navbar.php'; ?>
    </div>

<div class="container">
    <!-- Formularul de login -->
    <div class="dynamic-panel login-panel">
        <form method="post" action="process_login.php" class="inline-form">
            <div class="form-row">
                <input type="text" name="username_or_email" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-row">
                <img src="includes/captcha.php" alt="Captcha" class="captcha-image">
                <input type="text" name="captcha" placeholder="Enter Captcha" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

        <!-- Afișarea proxy-urilor -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>IP:Port</th>
                    <th>ASN</th>
                    <th>Organization</th>
                    <th>Added Ago</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($proxies)): ?>
                    <?php foreach ($proxies as $index => $proxy): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($proxy['type']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['country']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['city']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['ip'] . ':' . $proxy['port']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['asn']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['organization']); ?></td>
                            <td><?php echo htmlspecialchars($proxy['time_since_added']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No proxies available at the moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
