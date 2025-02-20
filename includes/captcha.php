<?php
session_start();

// Generează un cod CAPTCHA de 5 caractere
$code = substr(str_shuffle("23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 5);
$_SESSION['captcha'] = $code;

header('Content-type: image/png');

// Creează imaginea CAPTCHA
$image = imagecreatetruecolor(200, 80);
$background = imagecolorallocate($image, 248, 248, 248); // Fundal alb
$foreground = imagecolorallocate($image, 0, 0, 0); // Text negru
imagefilledrectangle($image, 0, 0, 200, 80, $background);

// Specifică calea către fișierul de font TTF
$font = __DIR__ . '/arial.ttf'; // Fontul trebuie să fie în același folder ca și acest script
$font_size = 27; // Dimensiunea fontului

// Calculează poziția textului pentru a-l centra
$bbox = imagettfbbox($font_size, 0, $font, $code);
$x = (200 - ($bbox[4] - $bbox[0])) / 2; // Centrare pe axa X
$y = (80 - ($bbox[5] - $bbox[1])) / 2 + $font_size / 2; // Centrare pe axa Y

// Adaugă textul CAPTCHA în imagine
imagettftext($image, $font_size, 0, $x, $y, $foreground, $font, $code);

// Generează imaginea CAPTCHA și distruge resursa după utilizare
imagepng($image);
imagedestroy($image);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container label {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        .login-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .login-container input:focus {
            border-color: #007bff;
            outline: none;
        }
        .captcha-image {
            display: block;
            margin: 10px 0;
        }
        .login-container button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="captcha">CAPTCHA:</label>
            <img src="includes/captcha.php" alt="CAPTCHA" class="captcha-image">
            <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
