<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proxy_id'])) {
    $proxyId = $_POST['proxy_id'];
    if (isset($_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'] = array_filter($_SESSION['shopping_cart'], function($id) use ($proxyId) {
            return $id != $proxyId;
        });
    }
}

header('Location: shopping_cart.php');
exit;
?>
