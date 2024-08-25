<?php
$servername = 'localhost';  // Of de naam van je server
$dbname = 'book-review';  // Vervang door de naam van je database
$username = 'root';  // Vervang door je databasegebruikersnaam
$password = '';  // Vervang door je databasewachtwoord

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Stop verdere uitvoering als de verbinding mislukt
}
?>
