<?php
$pageTitle = 'Your Favorites - Razenderon';
include __DIR__ . '/_header.php';


// Handle remove favorite action FIRST, before any data is fetched
if (isset($_GET['remove_fav'])) {
    $remove_id = $_GET['remove_fav'];
    // array_diff is a clean way to remove an element by its value
    $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$remove_id]);
    // Redirect to the same page without the GET parameter to prevent accidental re-removal
    header('Location: /favorites');
    exit;
}

$favoritedCars = [];
$favoritedCarsHtml = '';

if (!empty($_SESSION['favorites'])) {
    // Create the correct number of placeholders for the IN () clause
    $placeholders = implode(',', $_SESSION['favorites']);
    
    $placeholders = str_repeat('?,',count($_SESSION['favorites'])) . '0';
    // Fetch all favorited cars from the database in one query
    $sql = "SELECT * FROM car WHERE id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($_SESSION['favorites']);
    $favoritedCars = $stmt->fetchAll();
    
    foreach ($favoritedCars as $car) {
        $button = '';
        if ($car['isAvailable'] === 1) {
            $button = '<button class="btn" onclick="location.href=\'/checkout?car_id=' . $car['ID'] . '\'">Rent Now</button>';
        } else {
            $button = '<button class="btn" disabled>' . htmlspecialchars(ucfirst($car['status'])) . '</button>';
        }

        if (empty($car['image'])) {
            $car['image'] = 'auto_placeholder.png';
        }  

        $favoritedCarsHtml .= '
            <div class="car-item">
                <img src="/images/' . htmlspecialchars($car['image']) . '" style="max-width:300px; margin:auto;" alt="' . htmlspecialchars($car['brand']) . ' ' . htmlspecialchars($car['model']) . '">
                <h3>' . htmlspecialchars($car['brand']) . ' ' . htmlspecialchars($car['model']) . ' (' . htmlspecialchars($car['age']) . ')</h3>
                <p>Price: &euro;' . htmlspecialchars($car['price_per_day']) . ' / day</p>
                ' . $button . '
                <br><br><a href="/favorites?remove_fav=' . $car['ID'] . '" class="delete-link">Remove from favorites</a><br><br>
            </div>
        ';
    }
}

if (empty($favoritedCarsHtml)) {
    $favoritedCarsHtml = '<p>You have no favorited cars yet. <a href="/browse">Explore our fleet</a> to add some!</p>';
}

// Load the view template

$content = file_get_contents(__DIR__ . '/view/favorites.html');

// Replace the placeholder with the generated list
$content = str_replace('{{favorited_cars_list}}', $favoritedCarsHtml, $content);

echo $content;

include '_footer.php';
