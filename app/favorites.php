<?php
$pageTitle = 'Your Favorites - Razenderon';
include '_header.php';
// include 'config/database.php';

// Session management must be at the very top
// The header.php already starts the session, so this is just for clarity
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Handle remove favorite action FIRST, before any data is fetched
if (isset($_GET['remove_fav'])) {
    $remove_id = (int)$_GET['remove_fav'];
    // array_diff is a clean way to remove an element by its value
    $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$remove_id]);
    // Redirect to the same page without the GET parameter to prevent accidental re-removal
    header('Location: favorites.php');
    exit;
}

$favoritedCars = [];
$favoritedCarsHtml = '';

if (!empty($_SESSION['favorites'])) {
    // Create the correct number of placeholders for the IN () clause
    $placeholders = implode(',', array_fill(0, count($_SESSION['favorites']), '?'));
    
    // Fetch all favorited cars from the database in one query
    $sql = "SELECT * FROM car WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['favorites']);
    $favoritedCars = $stmt->fetchAll();
    
    foreach ($favoritedCars as $car) {
        $button = '';
        if ($car['status'] === 'available') {
            $button = '<button class="btn" onclick="location.href=\'checkout.php?car_id=' . $car['id'] . '\'">Rent Now</button>';
        } else {
            $button = '<button class="btn" disabled>' . htmlspecialchars(ucfirst($car['status'])) . '</button>';
        }

        $favoritedCarsHtml .= '
            <div class="car-item">
                <img src="' . htmlspecialchars($car['image']) . '" alt="' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . '">
                <h3>' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . ' (' . htmlspecialchars($car['year']) . ')</h3>
                <p>Price: $' . htmlspecialchars($car['price_per_day']) . ' / day</p>
                ' . $button . '
                <a href="favorites.php?remove_fav=' . $car['id'] . '" class="btn" style="background-color: #c9302c; margin-top: 10px;">Remove Favorite</a>
            </div>
        ';
    }
}

if (empty($favoritedCarsHtml)) {
    $favoritedCarsHtml = '<p>You have no favorited cars yet. <a href="/browse">Explore our fleet</a> to add some!</p>';
}

// Load the view template

include 'view/favorites.html';
$content = file_get_contents(__DIR__ . '/view/favorites.html');

// Replace the placeholder with the generated list
$content = str_replace('{{favorited_cars_list}}', $favoritedCarsHtml, $content);

echo $content;

include '_footer.php';
