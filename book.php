<?php
require 'db-conn.php';  // Verbind met de database

// Verkrijg het boek-ID uit de URL
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Controleer of het boek-ID geldig is
if ($bookId <= 0) {
    echo 'Ongeldig boek-ID.';
    exit;
}

// Verkrijg de boekinformatie
$sql = 'SELECT * FROM book WHERE id = ?';
$stmt = $conn->prepare($sql);
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Controleer of het boek bestaat
if (!$book) {
    echo 'Boek niet gevonden.';
    exit;
}

// Haal de comments voor dit boek op
$sqlComments = 'SELECT comment_title, comment_text, rating, created_at FROM comments WHERE book_id = ? ORDER BY created_at DESC';
$stmtComments = $conn->prepare($sqlComments);
$stmtComments->execute([$bookId]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

// Bereken het gemiddelde van de beoordelingen
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

// Bereken en update de gemiddelde beoordeling
$averageRate = calculateAverageRating($conn, $bookId);

// Update het gemiddelde in de database
$sqlUpdateBook = 'UPDATE book SET average_rate = ? WHERE id = ?';
$stmtUpdateBook = $conn->prepare($sqlUpdateBook);
$stmtUpdateBook->execute([$averageRate, $bookId]);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/3aef4a74c1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($book['title']); ?></title>
    <link rel="icon" href="stuff/icn.png">

    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        position: relative;
    }
    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('stuff/w6.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        opacity: 0.8;
        z-index: -1;
        filter: blur(1px);
    }

    .logo-search-container {
        display: flex;
        align-items: center;
        position: sticky;
        top: 0;
        background-color: rgba(255, 255, 255, 0.3);
        z-index: 1000;
        padding: 10px;
    }

    .logo {
        margin-right: 15px;
        margin-left: 15px;
    }

    .search-bar-container {
        position: relative;
        flex-grow: 1;
    }

    .search-bar {
        width: 100%;
        padding: 10px 40px 10px 20px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .search-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        font-size: 20px;
        color: #aaa;
    }

    .profile-image-container {
        padding: 20px;
    }

    .profile-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-link {
        display: inline-block;
    }

    .container {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        flex-wrap: nowrap;
    }

    .left-column {
        width: 65%;
    }

    .right-column {
        width: 30%;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
    }

    .card-container {
        display: flex;
        align-items: flex-start;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-container .image {
        flex: 0 0 auto;
    }

    .card-container .image img {
        height: 300px;
        border-radius: 10px;
        max-width: 100%;
    }

    .card-container .info {
        padding-top: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-left: 20px;
    }

    .card-container .sterren {
        margin-top: auto;
        padding-top: 9rem;
        color: #777;
    }

    .card-container .title {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .card-container .desc {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .card-container .sterren i,
    .leave-com .input-stars i {
        font-size: 20px;
        margin: 0 2px;
        color: #DAA520; /* Gouden kleur voor de sterren */
    }

    .comments .author i {
        font-size: 16px;  /* Maak de sterren kleiner */
        margin: 0 1px;   /* Verminder de ruimte tussen de sterren */
        color: #DAA520; /* Gouden kleur voor de sterren */
    }

    .comments .comment .title {
        font-weight: bold; /* Maak de titel van de comment vetgedrukt */
        font-size: 16px;   /* Pas eventueel de grootte van de titel aan, indien gewenst */
    }

    .leave-com {
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
    }

    .leave-com .top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .leave-com .title-input {
        width: 45%;
    }

    .leave-com .input-stars {
        display: flex;
        align-items: center;
        margin-left: 10px;
    }

    .leave-com .bottom-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .leave-com .desc-input {
        width: 70%;
    }

    .leave-com .send-btn {
        margin-left: 10px;
    }

    .leave-com input,
    .leave-com textarea {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .leave-com .input-stars i {
        font-size: 24px;
        margin: 0 2px;
        cursor: pointer;
    }

    .leave-com .send-btn button {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        background-color: #777;
        color: white;
        cursor: pointer;
    }

    .comments {
        max-height: 400px;
        overflow-y: auto;
    }

    .comments .comment {
        margin-bottom: 10px;
    }

    .comments .comment .text {
        font-size: 16px;
    }

    .comments .comment .author {
        font-size: 14px;
        color: #777;
    }

    .comments .comment .date {
        font-size: 12px;
        color: #aaa;
    }

    .comments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        border-bottom: 2px solid #ccc; /* Voegt een lijn toe onder de Reacties-header */
        margin-bottom: 15px; /* Voegt wat ruimte toe onder de lijn */
    }

    .comments-header h2 {
        margin: 0;
    }

    .comments-header .sort-options {
        font-size: 14px;
        color: #777;
        cursor: pointer;
    }
</style>


    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const starRating = document.querySelectorAll('.input-stars i');
            let ratingValue = 0;

            starRating.forEach((star, index) => {
                star.addEventListener('mouseover', () => {
                    updateStars(index + 1);
                });

                star.addEventListener('mouseout', () => {
                    updateStars(ratingValue);
                });

                star.addEventListener('click', () => {
                    ratingValue = index + 1;
                    document.getElementById('rating').value = ratingValue;
                });
            });

            function updateStars(rating) {
                starRating.forEach((star, index) => {
                    star.className = index < rating ? 'fas fa-star' : 'far fa-star';
                });
            }
        });
    </script>
</head>
<body>
    <div class="logo-search-container">
        <a href="home.php">
            <img src="stuff/file9.png" width="90px" height="90px" class="logo">
        </a>
        <div class="search-bar-container">
            <form action="book-search.php" method="GET">
                <input type="text" name="query" placeholder="Zoek hier..." class="search-bar">
                <i class="fas fa-search search-icon"></i>
            </form>
        </div>
        <div class="profile-image-container">
            <a href="profile-page.html" class="profile-link">
                <img src="stuff/pfp3.jpeg" alt="Profile Image" class="profile-image">
            </a>
        </div>
    </div>
    <div class="container">
        <div class="left-column">
            <div class="card-container">
                <div class="image">
                    <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image">
                </div>
                <div class="info">
                    <div class="title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <div class="desc"><?php echo htmlspecialchars($book['description']); ?></div>
                    
                    <div class="sterren">
        <p style="font-size: 12px; color: black; margin: 0;">
            Average Rating: 
            <?php
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= round($averageRate)) {
                    echo '<i class="fas fa-star"></i>';
                } else {
                    echo '<i class="far fa-star"></i>';
                }
            }
            ?>
        </p>
    </div>


                </div>
            </div>
            <form method="post" action="save-com.php" class="leave-com">
                <div class="top-row">
                    <div class="title-input">
                        <label for="title">Name:</label>
                        <input type="text" id="title" name="comment_title" required>
                    </div>
                    <div class="input-stars">
                        <label for="rating">Rating:</label>
                        <input type="hidden" id="rating" name="rating" value="0">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="far fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="bottom-row">
                    <div class="desc-input">
                        <label for="content">Comment:</label>
                        <textarea id="content" name="comment_text" rows="4" required></textarea>
                    </div>
                    <div class="send-btn">
                        <button type="submit">Verzenden</button>
                    </div>
                </div>
                <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
            </form>
        </div>
        <div class="right-column">
            <div class="comments">
                <div class="comments-header">
                    <h2>Reacties</h2>
                    <div class="sort-options">Sorteren op datum</div>
                </div>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="title"><?php echo htmlspecialchars($comment['comment_title']); ?></div>
                        <div class="text"><?php echo htmlspecialchars($comment['comment_text']); ?></div>
                        <div class="author">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $comment['rating']): ?>
                                    <i class="fas fa-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="date"><?php echo htmlspecialchars($comment['created_at']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
