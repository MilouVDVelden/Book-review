<?php
require 'db-conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Gebruik hier $conn in plaats van $pdo
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: home.php"); // Vervang door je inlogpagina
        // Je kan hier een redirect naar de homepagina of dashboard toevoegen
    } else {
        echo "Incorrect email or password!";
    }
}
?>

