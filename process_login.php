<?php
session_start();
include 'database/db_connect.php'; // Conectarea la baza de date

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preluăm datele din formular
    $username_or_email = trim($_POST['username_or_email']);
    $password = trim($_POST['password']);
    $captcha = trim($_POST['captcha']);

    // Validăm CAPTCHA
    if (!isset($_SESSION['captcha']) || $captcha !== $_SESSION['captcha']) {
        die("Invalid CAPTCHA code.");
    }

    try {
        // Verificăm dacă utilizatorul există
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([
            ':username' => $username_or_email,
            ':email' => $username_or_email,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Logare reușită, salvăm datele utilizatorului în sesiune
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirecționăm către o pagină protejată
            header("Location: dashboard.php");
            exit;
        } else {
            // Mesaj de eroare în cazul unei autentificări nereușite
            die("Invalid username/email or password.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
