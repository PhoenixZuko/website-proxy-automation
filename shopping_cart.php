<?php
session_start();
include 'database/db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    die('User not authenticated. Please log in.');
}
$userId = $_SESSION['user_id'];

// Inițializează coșul dacă nu există
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Logica pentru ștergerea unui articol din coș
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = $_POST['remove_id'];
    if (($key = array_search($removeId, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexează array-ul
    }
    header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
    exit;
}

// Logica pentru salvarea proxy-urilor selectate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_selected'])) {
    foreach ($_SESSION['cart'] as $proxyId) {
        // Log de debug în fișier, nu în output
        error_log("Inserting user_id: " . $_SESSION['user_id'] . " with proxy_id: " . $proxyId);

        $query = "INSERT INTO user_proxies (user_id, proxy_id, requested_at) 
                  SELECT :user_id, :proxy_id, NOW()
                  WHERE NOT EXISTS (
                      SELECT 1 FROM user_proxies WHERE user_id = :user_id AND proxy_id = :proxy_id
                  )";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':proxy_id' => $proxyId
        ]);
    }

    // Golește coșul
    $_SESSION['cart'] = [];
    
    // Redirecționează după inserare
    header('Location: my_products.php');
    exit;
}


// Obține ID-urile articolelor din sesiune
$cartItems = $_SESSION['cart'];

if (empty($cartItems)) {
    $items = [];
} else {
    // Creează interogarea pentru a obține detalii despre articole
    $placeholders = implode(',', array_fill(0, count($cartItems), '?'));
    $query = "SELECT id, type, country, city, ip, port, asn, organization, date_added FROM proxies WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($cartItems);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funcție pentru calcularea timpului scurs
function timeAgo($dateTime) {
    $time = strtotime($dateTime);
    $timeDiff = time() - $time;

    if ($timeDiff < 60) {
        return $timeDiff . " seconds ago";
    } elseif ($timeDiff < 3600) {
        return floor($timeDiff / 60) . " minutes ago";
    } elseif ($timeDiff < 86400) {
        return floor($timeDiff / 3600) . " hours ago";
    } else {
        return floor($timeDiff / 86400) . " days ago";
    }
}

// Funcție pentru cenzurarea IP-ului
function censorIp($ip) {
    return substr($ip, 0, 4) . str_repeat('*', strlen($ip) - 4);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coș de cumpărături</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .empty-cart {
            text-align: center;
            color: red;
            font-size: 18px;
        }
        .remove-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .remove-button:hover {
            background-color: #ff1a1a;
        }
        .take-button {
            display: block;
            margin: 20px auto;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .take-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <!-- Navbar inclus -->
    <?php include 'includes/nav_login.php'; ?>

    <?php if (!empty($items)): ?>
        <form method="POST" action="">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>IP:Port</th>
                        <th>ASN</th>
                        <th>Organization</th>
                        <th>Added Ago</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['type']); ?></td>
                            <td><?php echo htmlspecialchars($item['country']); ?></td>
                            <td><?php echo htmlspecialchars($item['city']); ?></td>
                            <td><?php echo htmlspecialchars(censorIp($item['ip']) . ':' . $item['port']); ?></td>
                            <td><?php echo htmlspecialchars($item['asn']); ?></td>
                            <td>
                                <?php 
                                // Trunchierea organizației dacă este prea lungă
                                echo strlen($item['organization']) > 25 
                                    ? htmlspecialchars(substr($item['organization'], 0, 25)) . '...' 
                                    : htmlspecialchars($item['organization']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars(timeAgo($item['date_added'])); ?></td>
                            <td>
                                <button type="submit" name="remove_id" value="<?php echo htmlspecialchars($item['id']); ?>" class="remove-button">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="take_selected" class="take-button">Take Selected Proxy</button>
        </form>
    <?php else: ?>
        <p class="empty-cart">Coșul tău de cumpărături este gol!</p>
    <?php endif; ?>

    <div>
        <!-- Footer inclus -->
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
