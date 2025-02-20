<?php
session_start();
include 'database/db_connect.php'; // Conectarea la baza de date

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preluăm datele din formular
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $captcha = trim($_POST['captcha']);

    // Validare CAPTCHA
    if (!isset($_SESSION['captcha']) || $captcha !== $_SESSION['captcha']) {
        die("Invalid CAPTCHA code.");
    }

    // Validare parole
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash parola
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Pregătim interogarea SQL
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        // Redirecționare la pagina de succes
        header("Location: register_success.php");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Cod pentru eroare de duplicare
            die("Username or email already exists.");
        }
        die("Database error: " . $e->getMessage());
    }
}
?>
