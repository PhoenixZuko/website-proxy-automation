<?php
session_start();
header('Content-Type: application/json');

// Citim datele trimise prin AJAX
$data = json_decode(file_get_contents('php://input'), true);
$proxyId = $data['id'] ?? null;
$action = $data['action'] ?? null;

if (!$proxyId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Inițializare coș dacă nu există
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Gestionarea acțiunilor
if ($action === 'add') {
    // Adaugă ID-ul în coș dacă nu există deja
    if (!in_array($proxyId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $proxyId;
    }
} elseif ($action === 'remove') {
    // Elimină ID-ul din coș
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$proxyId]);
}

// Returnează răspunsul JSON
echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
exit;
?>
