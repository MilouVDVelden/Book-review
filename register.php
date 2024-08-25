<?php
require 'db-conn.php';  // Verbindt met de database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Email already registered!";
    } else {
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        if ($stmt->execute([$name, $email, $password])) {
            header("Location: index.html"); // Vervang door je inlogpagina
        }
    }
}
?>
