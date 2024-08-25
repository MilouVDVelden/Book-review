<?php
require 'db-conn.php';  // Zorg ervoor dat je de databaseverbinding goed hebt

if (isset($_POST['comment_title'], $_POST['comment_text'], $_POST['rating'], $_POST['book_id'])) {
    $commentTitle = trim($_POST['comment_title']);
    $commentText = trim($_POST['comment_text']);
    $rating = intval($_POST['rating']);
    $bookId = intval($_POST['book_id']);
    
    // Zorg ervoor dat rating een geldige waarde heeft tussen 1 en 5
    $rating = max(1, min(5, $rating));

    $sql = 'INSERT INTO comments (book_id, comment_title, comment_text, rating) VALUES (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$bookId, $commentTitle, $commentText, $rating]);
        
        // Update de gemiddelde beoordeling na toevoegen van een recensie
        $averageRate = calculateAverageRating($conn, $bookId);
        $sqlUpdateBook = 'UPDATE book SET average_rate = ? WHERE id = ?';
        $stmtUpdateBook = $conn->prepare($sqlUpdateBook);
        $stmtUpdateBook->execute([$averageRate, $bookId]);

        header("Location: book.php?id=$bookId"); 
        exit;
    } catch (PDOException $e) {
        echo 'Er is een fout opgetreden: ' . $e->getMessage();
    }
} else {
    echo 'Niet alle vereiste gegevens zijn verzonden.';
}

// Functie om het gemiddelde van de beoordelingen te berekenen
function calculateAverageRating($conn, $bookId) {
    $sqlRatings = 'SELECT rating FROM comments WHERE book_id = ?';
    $stmtRatings = $conn->prepare($sqlRatings);
    $stmtRatings->execute([$bookId]);

    $ratings = $stmtRatings->fetchAll(PDO::FETCH_COLUMN, 0);

    if (count($ratings) > 0) {
        $totalRating = array_sum($ratings);
        return $totalRating / count($ratings);
    }
    return 0;
}
?>
