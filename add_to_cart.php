<?php
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && is_numeric($input['id'])) {
    $id = intval($input['id']);

    if (!isset($_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'] = [];
    }

    if (!in_array($id, $_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'][] = $id;
    }

    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
