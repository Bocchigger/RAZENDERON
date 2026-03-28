<?php

$pageTitle = 'Browse Cars - Razenderon';
include __DIR__ . '/_header.php';


$q = $db->query("SELECT *, brand AS make, age AS year FROM car WHERE isAvailable = 0");
$unavailableCars = $q->fetchAll(PDO::FETCH_ASSOC);

$q = $db->query("SELECT *, brand AS make, age AS year FROM car WHERE isAvailable = 1");
$availableCars = $q->fetchAll(PDO::FETCH_ASSOC);



// Initialize favorites in session if not set
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

// Handle adding a car to favorites
if (!empty($_GET['add_fav'])) {
    $add_id = $_GET['add_fav'];
    if (!in_array($add_id, $_SESSION['favorites'])) {
        $_SESSION['favorites'][] = $add_id;
    }
    // Redirect to clear the GET parameter
    header('Location: /browse');
    exit;
}

// Handle remove favorite action
if (isset($_GET['remove_fav'])) {
    $remove_id = $_GET['remove_fav'];
    // array_diff is a clean way to remove an element by its value
    $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$remove_id]);
    // Redirect to the same page without the GET parameter to prevent accidental re-removal
    header('Location: /browse');
    exit;
}

$content = file_get_contents(__DIR__ . '/view/browse.html');

// Replace placeholders in the HTML
$carListHtml = '';

// Render Available Cars first
foreach ($availableCars as $car) {
    $button = '<button class="btn" onclick="location.href=\'/checkout?car_id=' . $car['ID'] . '\'">Rent Now</button>';
    $statusClass = 'available';
    $statusText = 'Available';

    $favoriteStar = '';
    if (in_array($car['ID'], $_SESSION['favorites'])) {
        // Favorited: Link to remove (handled by favorites.php), solid star
        $favoriteStar = '<a href="/browse?remove_fav=' . $car['ID'] . '" class="favorite-star favorited" title="Remove from Favorites">&#9733;</a>';
    } else {
        // Not favorited: Link to add, outline star
        $favoriteStar = '<a href="/browse?add_fav=' . $car['ID'] . '" class="favorite-star" title="Add to Favorites">&#9734;</a>';
    }

    if (empty($car['image'])) {
        $car['image'] = 'auto_placeholder.png';
    }

    // Prepare data attributes for the modal
    $dataAttributes = ' data-id="' . htmlspecialchars($car['ID']) . '"';
    // ... (rest of data attributes)

    foreach ($car as $key => $value) {
        $dataAttributes .= ' data-' . $key . '="' . $value . '" ';
    }

    $dataAttributes .= ' data-status="available"';

    $carListHtml .= '
        <div class="car-item clickable" ' . $dataAttributes . '>
            ' . $favoriteStar . '
            <img src="/images/' . htmlspecialchars($car['image']) . '" alt="' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . '">
            <div class="car-item-content">
                <h3>' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . ' (' . htmlspecialchars($car['year']) . ')</h3>
                <p class="price">&euro;' . htmlspecialchars($car['price_per_day']) . ' / day</p>
                <p class="status ' . $statusClass . '">' . $statusText . '</p>
                ' . $button . '
            </div>
        </div>
    ';
}

// Render Unavailable Cars at the bottom
if (!empty($unavailableCars)) {
    $carListHtml .= '<h2 style="grid-column: 1 / -1; text-align: center; margin-top: 40px; color: var(--color-primary);">Unavailable Cars</h2>'; // Header for unavailable cars
    foreach ($unavailableCars as $car) {
        $button = '<button class="btn" disabled>Unavailable</button>';
        $statusClass = 'unavailable';
        $statusText = 'Unavailable';

        $favoriteStar = '';
        if (in_array($car['ID'], $_SESSION['favorites'])) {
            $favoriteStar = '<a href="/favorites?remove_fav=' . $car['ID'] . '" class="favorite-star favorited" title="Remove from Favorites">&#9733;</a>';
        }
        // No option to add unavailable cars to favorites for simplicity

        // Prepare data attributes for the modal (even if disabled)
        $dataAttributes = ' data-id="' . htmlspecialchars($car['ID']) . '"';
        // ... (rest of data attributes)

        if (empty($car['image'])) {
            $car['image'] = 'auto_placeholder.png';
        }

        $carListHtml .= '
            <div class="car-item" ' . $dataAttributes . '>
                 ' . $favoriteStar . '
                <img src="/images/' . htmlspecialchars($car['image']) . '" alt="' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . '">
                <div class="car-item-content">
                    <h3>' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) . ' (' . htmlspecialchars($car['year']) . ')</h3>
                    <p class="price">$' . htmlspecialchars($car['price_per_day']) . ' / day</p>
                    <p class="status ' . $statusClass . '">' . $statusText . '</p>
                    ' . $button . '
                </div>
            </div>
        ';
    }
}

$content = str_replace('{{car_list}}', $carListHtml, $content);

echo $content;
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carItems = document.querySelectorAll('.clickable');
    const modal = document.getElementById('carModal');
    const closeButton = document.querySelector('.close-button');
    const modalCarImage = document.getElementById('modalCarImage');
    const modalCarTitle = document.getElementById('modalCarTitle');
    const modalCarBrand = document.getElementById('modalCarBrand');
    const modalCarModel = document.getElementById('modalCarModel');
    const modalCarYear = document.getElementById('modalCarYear');
    const modalCarPrice = document.getElementById('modalCarPrice');
    const modalCarType = document.getElementById('modalCarType');
    const modalCarSeats = document.getElementById('modalCarSeats');
    const modalCarColor = document.getElementById('modalCarColor');
    const modalCarStatus = document.getElementById('modalCarStatus');
    const modalCarFeatures = document.getElementById('modalCarFeatures');
    const modalRentButton = document.getElementById('modalRentButton');

    carItems.forEach(item => {
        item.addEventListener('click', function(event) {
            // Prevent opening modal if a button inside car-item is clicked
            if (event.target.tagName === 'BUTTON' || event.target.closest('button')) {
                return;
            }

            modalCarImage.src = '/images/' + item.dataset.image;
            modalCarTitle.textContent = item.dataset.make + ' ' + item.dataset.model + ' (' + item.dataset.year + ')';
            modalCarBrand.textContent = item.dataset.make;
            modalCarModel.textContent = item.dataset.model;
            modalCarYear.textContent = item.dataset.year;
            modalCarPrice.textContent = item.dataset.price_per_day;
            modalCarType.textContent = item.dataset.type; 
            modalCarSeats.textContent = item.dataset.seats;
            modalCarColor.textContent = item.dataset.color;
            modalCarStatus.textContent = item.dataset.status;
            
            // Populate features
            modalCarFeatures.innerHTML = ''; // Clear previous features
            const features = [];
            if (item.dataset.towbar === 'Yes') features.push('Towbar');
            if (item.dataset.winterTires === 'Yes') features.push('Winter Tires');
            if (item.dataset.roofboxOption === 'Yes') features.push('Roofbox Option');
            if (item.dataset.class && item.dataset.class !== 'N/A') features.push('Class: ' + item.dataset.class);
            
            if (features.length > 0) {
                features.forEach(feature => {
                    const li = document.createElement('li');
                    li.textContent = feature;
                    modalCarFeatures.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'No special features/addons.';
                modalCarFeatures.appendChild(li);
            }


            // Update Rent Now button
            if (item.dataset.status === 'available') {
                modalRentButton.style.display = 'block';
                modalRentButton.onclick = function() {
                    location.href = '/checkout?car_id=' + item.dataset.id;
                };
            } else {
                modalRentButton.style.display = 'none';
            }

            modal.classList.add('is-active');
        });
    });

    closeButton.addEventListener('click', function() {
        modal.classList.remove('is-active');
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.remove('is-active');
        }
    });
});
</script>
<?php
include __DIR__ . '/_footer.php';
