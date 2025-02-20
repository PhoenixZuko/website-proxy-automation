<?php
session_start();
include 'database/db_connect.php';

// Obține proxy-urile din cart
$cartItems = isset($_SESSION['shopping_cart']) ? $_SESSION['shopping_cart'] : [];

if (!empty($cartItems)) {
    $placeholders = implode(',', array_fill(0, count($cartItems), '?'));
    $query = "SELECT id, type, country, city, ip, port, asn, organization FROM proxies WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($cartItems);
    $proxies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $proxies = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
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
    </style>
</head>
<body>
    <h1>Shopping Cart</h1>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proxies as $proxy): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proxy['id']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['type']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['country']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['city']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['ip'] . ':' . $proxy['port']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['asn']); ?></td>
                    <td><?php echo htmlspecialchars($proxy['organization']); ?></td>
                    <td>
                        <form method="POST" action="remove_from_cart.php" style="display:inline;">
                            <input type="hidden" name="proxy_id" value="<?php echo htmlspecialchars($proxy['id']); ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST" action="take_proxies.php">
        <button type="submit">Take It!</button>
    </form>
</body>
</html>
