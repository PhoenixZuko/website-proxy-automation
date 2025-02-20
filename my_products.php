<?php
session_start();
include 'database/db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    die('Error: User not authenticated.');
}
$userId = $_SESSION['user_id'];

// Obține produsele utilizatorului din baza de date
$query = "SELECT p.id, p.type, p.country, p.city, p.ip, p.port, p.asn, p.organization, p.date_added, up.requested_at
          FROM proxies p
          INNER JOIN user_proxies up ON p.id = up.proxy_id
          WHERE up.user_id = :user_id
          ORDER BY up.requested_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging
error_log("Session user_id: " . $_SESSION['user_id']);
error_log("Query results: " . print_r($items, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products</title>
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
        .empty-list {
            text-align: center;
            color: red;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <!-- Navbar inclus -->
    <?php include 'includes/nav_login.php'; ?>

    <h1>My Products</h1>
    <?php if (!empty($items)): ?>
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['type']); ?></td>
                        <td><?php echo htmlspecialchars($item['country']); ?></td>
                        <td><?php echo htmlspecialchars($item['city']); ?></td>
                        <td><?php echo htmlspecialchars($item['ip'] . ':' . $item['port']); ?></td>
                        <td><?php echo htmlspecialchars($item['asn']); ?></td>
                        <td>
                            <?php 
                            // Trunchierea organizației dacă este prea lungă
                            echo strlen($item['organization']) > 25 
                                ? htmlspecialchars(substr($item['organization'], 0, 25)) . '...' 
                                : htmlspecialchars($item['organization']);
                            ?>
                        </td>
                        <td><?php 
                            // Calcularea timpului scurs
                            $time = strtotime($item['date_added']);
                            $timeDiff = time() - $time;

                            if ($timeDiff < 60) {
                                echo $timeDiff . " seconds ago";
                            } elseif ($timeDiff < 3600) {
                                echo floor($timeDiff / 60) . " minutes ago";
                            } elseif ($timeDiff < 86400) {
                                echo floor($timeDiff / 3600) . " hours ago";
                            } else {
                                echo floor($timeDiff / 86400) . " days ago";
                            }
                        ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty-list">You have not selected any proxies yet!</p>
    <?php endif; ?>

    <div>
        <!-- Footer inclus -->
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
