<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'database/db_connect.php'; // Conexiunea la baza de date


// Setăm numărul maxim de proxy-uri gratuite
$freeProxyLimit = 60;

// Obținem ID-ul utilizatorului curent
$userId = $_SESSION['user_id'] ?? null;

// Obținem numărul de proxy-uri gratuite utilizate
$usedProxies = 0;
if ($userId) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_proxies WHERE user_id = :user_id AND DATE(requested_at) = CURDATE()");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $usedProxies = $stmt->fetchColumn();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Calculăm proxy-urile gratuite rămase
$remainingProxies = max(0, $freeProxyLimit - $usedProxies);

// Obținem numărul de elemente din coșul de cumpărături
$cartCount = isset($_SESSION['shopping_cart']) ? count($_SESSION['shopping_cart']) : 0;
?>

<script>
    function updateCartCount() {
        fetch('cart_count.php')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.right-section a').textContent = `Shopping Cart = ${data.count}`;
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }

    // Actualizăm coșul la încărcarea paginii
    document.addEventListener('DOMContentLoaded', updateCartCount);
</script>

<nav class="navbar">
    <div class="logo">
        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!
    </div>
<div class="right-section">
    <span>Free Proxy = <?php echo $remainingProxies; ?></span> |
    <a href="shopping_cart.php">Shopping Cart = <span id="cart-count"><?php echo $cartCount; ?></span></a>
</div>

    <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="my_products.php">My Products</a></li>
        <li><a href="shopping_cart.php">Shopping Cart</a></li>
        <li><a href="includes/logout.php">Logout</a></li>
    </ul>
</nav>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .navbar .logo {
        font-size: 20px;
        font-weight: bold;
    }
    .nav-links {
        list-style: none;
        display: flex;
        gap: 15px;
    }
    .nav-links li {
        display: inline;
    }
    .nav-links a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        transition: color 0.3s;
    }
    .nav-links a:hover {
        color: #f8f9fa;
    }
    .right-section {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
    }
    .right-section a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        background-color: #28a745;
        padding: 5px 10px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .right-section a:hover {
        background-color: #218838;
    }
</style>
