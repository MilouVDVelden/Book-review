<?php
require 'db-conn.php';  // Verbind met de database

// Verkrijg de zoekopdracht uit de URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Sanitize de zoekopdracht
$query = htmlspecialchars($query);

if ($query === '') {
    // Als er geen zoekopdracht is ingevoerd, selecteer dan alle boeken
    $sql = 'SELECT * FROM book';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
} else {
    // Als er wel een zoekopdracht is ingevoerd, zoek dan op titel of auteur
    $sql = 'SELECT * FROM book WHERE title LIKE ? OR author LIKE ?';
    $stmt = $conn->prepare($sql);
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm]);
}

// Verkrijg de zoekresultaten
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/3aef4a74c1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Search Results</title>
    <link rel="icon" href="stuff/icn.png">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body::before {
    content: "";
    position: fixed; /* Fixed zorgt ervoor dat de achtergrond vast blijft tijdens scrollen */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('stuff/w6.jpg');
    background-size: cover; /* Houd de originele grootte van de afbeelding */
    background-repeat: repeat; /* Herhaal de achtergrondafbeelding */
    opacity: 0.8; /* Pas de opaciteit van de achtergrondafbeelding aan */
    z-index: -1; /* Zorg ervoor dat de afbeelding onder de inhoud staat */
    filter: blur(1px);
}


        .logo-search-container {
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            background-color: rgba(255, 255, 255, 0.3); /* Almost transparent */
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

        .title1, .title2, .title3 {
            text-align: center;
            margin: 20px 0;
        }

        .cards-container {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 0;

        }

        .card {
            flex: 1 1 calc(25% - 2rem);
            margin: 1rem;

        }

        .card-title {
            text-align: center;
            font-size: 19px;
        }

        .card-desc {
            text-align: center;
            font-size: 14px;
            margin-bottom: 0;
        }

        .stars {
            text-align: center;
            font-size: 24px;
        color : #DAA520;
        }

        .stars a {
            font-size: 11px;
        }

        .search-container {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="logo-search-container">
    <a href="home.php">
        <img src="stuff/file9.png" width="90px" height="90px" class="logo"></a>
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

    <div class="title1">
        <span><?php echo htmlspecialchars($query); ?></span>
    </div>

    <div class="cards-container">
        <?php
        // Geef de zoekresultaten weer
        if ($books) {
            foreach ($books as $book) {
                echo '<div class="card" style="max-width: 250px;">';
                echo '<a href="book.php?id=' . htmlspecialchars($book['id']) . '">';
                echo '<img src="' . htmlspecialchars($book['image']) . '" class="card-img-top" style="max-height: 350px;" alt="' . htmlspecialchars($book['title']) . '">';
                echo '<div class="card-body">'; echo '</a>';
                echo '<h5 class="card-title">' . htmlspecialchars($book['title']) . '</h5>';
               
                echo '<p class="card-desc">' . htmlspecialchars($book['author']) . '</p>';
                echo '<div class="stars">';
                
                $averageRate = $book['average_rate'];
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $averageRate) {
                        echo '<i class="fa-solid fa-star"></i>';
                    } else {
                        echo '<i class="fa-regular fa-star"></i>';
                    }
                }
                
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No results found.</p>';
        }
        ?>
    </div>

    <script src="script.js"></script>
</body>
</html>
