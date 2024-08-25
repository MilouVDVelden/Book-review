<?php
require 'db-conn.php';

// Haal de top 10 boeken op met hun gemiddelde rating
try {
    $stmt = $conn->query('
        SELECT id, title, author, average_rate, previous_rank
        FROM book
        ORDER BY average_rate DESC
        LIMIT 10
    ');
    $topBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    exit();
}

// Bereken de huidige ranking en sla deze op
foreach ($topBooks as $index => $book) {
    $currentRank = $index + 1;
    $previousRank = $book['previous_rank'];
    $rankChange = is_null($previousRank) ? 0 : $previousRank - $currentRank;

    // Update de huidige rank naar de `previous_rank` kolom in de database
    try {
        $stmt = $conn->prepare('UPDATE book SET previous_rank = :currentRank WHERE id = :id');
        $stmt->execute([':currentRank' => $currentRank, ':id' => $book['id']]);
    } catch (PDOException $e) {
        echo 'Update failed: ' . $e->getMessage();
        exit();
    }

    $topBooks[$index]['currentRank'] = $currentRank;
    $topBooks[$index]['rankChange'] = $rankChange;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Caveat:wght@500&family=Montserrat:ital,wght@0,500;1,500&family=Oswald&family=Teko:wght@300..700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/3aef4a74c1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Home</title>
    <link rel="icon" href="stuff/icn.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        .background-container {
            position: fixed;
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
        .content-container {
            position: relative;
            z-index: 1;
        }
        .logo-search-container {
            display: flex;
            align-items: center;
        }
        .logo {
            margin-right: 15px;
            margin-left: 15px;
        }
        .search-bar-container {
            position: relative;
            flex-grow: 1;
        }
        .fa-star {
            color: #DAA520; /* Gouden kleur voor de sterren */
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
        .title1 {
            text-align: center;
            font-family: "Teko", sans-serif;
            margin-top: 2rem;
            margin-right: 50%;
            font-size: 70px;
            line-height: 1.1;
        }
        .title2 {
            font-family: "Teko", sans-serif;
            text-align: center;
            margin-right: 50%;
            opacity: 0.9;
            font-size: 58px;
            line-height: 0.4;
        }
        .title3 {
            font-family: "Teko", sans-serif;
            text-align: center;
            margin-right: 49.80%;
            font-size: 83px;
            opacity: 0.6;
            margin-top: -16px; /* Verklein of pas deze waarde aan voor de gewenste positie */
        }

        table {
            width: 100%;
            margin-top: 1rem;
            margin-bottom: 3rem;
        }
        th, td {
            text-align: right;
            padding-left: 20px;
        }
        .rating-cell {
            text-align: center;
            font-size: 17px;
        }
        .fa-arrow-up {
            color: green;
        }
        .container-table {
    position: relative;
    max-width: 900px;
    margin: 2rem auto 0 auto;
    margin-top: 3rem;
    margin-right: 5rem;
    border: transparent solid 1px;
    border-radius: 20px;
    background-color: white;
    margin-bottom: 5rem;
    z-index: 2; /* Zorg ervoor dat de tabel boven de afbeelding staat */
}

.image-container {
    position: absolute;
    left: 60px; /* Kan worden aangepast indien nodig */
    bottom: -200px; /* Verhoog de waarde om de afbeelding meer naar boven te verplaatsen */
    width: 300px; /* Breedte van de afbeelding */
    height: 300px; /* Hoogte van de afbeelding */
    overflow: hidden;
    z-index: 1; /* Zorg ervoor dat de afbeelding onder de tabel komt */
}
.content-container {
    flex: 1;
    /* Zorg ervoor dat de content-container de resterende ruimte opvult */
}



    </style>
</head>
<body>
    <header>
        <div class="background-container"></div>
        <div class="header" id="myHeader">
            <div class="content-container">
                <div class="logo-search-container">
                    <img src="stuff/file9.png" width="90px" height="90px" class="logo">
                    <div class="search-bar-container">
                        <form action="book-search.php" method="GET">
                            <input type="text" name="query" placeholder="Zoek hier..." class="search-bar">
                            <i class="fas fa-search search-icon"></i>
                        </form>
                    </div>
                    <div class="profile-image-container">
                        <a href="Profile.html" class="profile-link">
                            <img src="stuff/pfp3.jpeg" alt="Profile Image" class="profile-image">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="title1">
        <span><b>Review books.</b></span>
    </div>
    <div class="title2">
        <span>Best recommend books</span>
    </div>
    <div class="title3"><span>All times ranking</span></div>
    <div class="container-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <!-- Table headers here -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topBooks as $book): ?>
                <tr>
                    <td>
                        <?php
                        if ($book['rankChange'] > 0) {
                            echo '<i class="fa-solid fa-arrow-up" style="color:green;"></i>'; // Gestegen
                        } elseif ($book['rankChange'] < 0) {
                            echo '<i class="fa-solid fa-arrow-down" style="color:red;"></i>'; // Gedaald
                        } else {
                            echo '<i class="fa-solid fa-minus"></i>'; // Geen verandering
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($book['currentRank']); ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td class="rating-cell">
                        <?php
                        $rating = round($book['average_rate']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<i class="fas fa-star"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="image-container">
            <img src="stuff/book-we.png" alt="Beschrijving van afbeelding" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>


    <script src="script.js"></script>

</body>
</html>

