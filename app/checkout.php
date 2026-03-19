<?php
$pageTitle = 'Checkout - Razenderon';
include __DIR__ . '/_header.php';

if (empty($_SESSION['id'])) {
    header ('Location: /login');
    exit();
}

$user_id = $_SESSION['id'];
$car_details_html = '';
$checkout_form_html = '';
$error_message = '';

// Handle POST request: Finalize the lease
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_lease'])) {
    // Sanitize and validate inputs
    $car_id = filter_input(INPUT_POST, 'car_id');
    $price_per_day = filter_input(INPUT_POST, 'price_per_day', FILTER_VALIDATE_FLOAT);
    $lease_duration = filter_input(INPUT_POST, 'lease_duration', FILTER_VALIDATE_INT);

    // Basic validation check
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        if ($_POST['lease_duration'] < 1) {
            $error = 'Lease duration must be at least 1 day.';
        }   

        if ($_POST['lease_duration'] > 30) {
            $error = 'Lease duration must not exceed 30 days.';
        }  

        if (strlen($_POST['cc_number']) < 1) {
            $error = 'Please provide a valid creditcard number.';
            // usually through payment provider.
        }

        $total_cost = $lease_duration * $price_per_day;
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+$lease_duration days"));

        $db->beginTransaction();
        try {
            // 1. Insert into leases table
            $sql_insert = "INSERT INTO lease SET
                car = :car, 
                account = :account, 
                startDay = :startDay, 
                lastDay = :lastDay, 
                totalCost = :totalCost 
                ";
            $stmt_insert = $db->prepare($sql_insert);
            $stmt_insert->execute([
                'car' => $car_id, 
                'account' => (int)$user_id, 
                'startDay' => $start_date, 
                'lastDay' => $end_date, 
                'totalCost' => $total_cost
                ]);

            // 2. Update cars table
            $sql_update = "UPDATE car SET isAvailable = '0' WHERE id = :id AND isAvailable = '1'";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->execute(['id' => $car_id]);
            
            $db->commit();
            
            $email = $_POST['email'];
            $_SESSION['success_message'] = "Lease successful for car #$car_id! A confirmation has been sent to $email.";
            header('Location: /active_leases');
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            // $error_message = 'An error occurred while processing your lease. Please try again.';
            $error_message = $e->getMessage();
        }
    } else {
        $error_message = 'Please fill out all fields correctly.';
    }

// Handle GET request: Display confirmation form
}

// Always fetch car and user data for display on GET or on POST error
$car_id_get = $_GET['car_id'] ?? ($_POST['car_id'] ?? null);

if ($car_id_get) {
    $stmt_car = $db->prepare("SELECT * FROM car WHERE id = ?");
    $stmt_car->execute([$car_id_get]);
    $selectedCar = $stmt_car->fetch();

    $stmt_user = $db->prepare("SELECT * FROM account WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();

    if ($selectedCar && $user) {
        if ($selectedCar['isAvailable'] == '0' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
             $car_details_html = '<p class="error-message">This car is no longer available for lease.</p>';
        } else {
            if (empty($selectedCar['image'])) {
                $selectedCar['image'] = 'auto_placeholder.png';
            }
            if (empty($selectedCar['deposit'])) {
                $selectedCar['deposit'] = ceil($selectedCar['price_per_day'] / 2);
            }
            // Car Details HTML
            $car_details_html = '
                <div style="text-align: center;">
                    <img src="/images/' . htmlspecialchars($selectedCar['image']) . '" alt="Car Image" style="max-width: 100%; border-radius: 4px;">
                    <h3>' . htmlspecialchars($selectedCar['brand'] . ' ' . $selectedCar['model']) . ' (' . $selectedCar['age'] . ')</h3>
                    <p><strong>Price per day:</strong> &euro;' . htmlspecialchars($selectedCar['price_per_day']) . '</p>
                    <p>A deposit of &euro;' . htmlspecialchars($selectedCar['deposit']) . ' may be required.</p>
                </div>
            ';

            // Checkout Form HTML
            $checkout_form_html = '
                <form method="POST" action="/checkout">
                    ' . ($error_message ? '<p class="error-message">'.$error_message.'</p>' : '') . '
                    <input type="hidden" name="car_id" value="' . $selectedCar['ID'] . '">
                    <input type="hidden" name="price_per_day" value="' . $selectedCar['price_per_day'] . '">
                    
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="' . htmlspecialchars($user['fullName']) . '" required>
                    
                    <label for="email">Email for Confirmation</label>
                    <input type="email" id="email" name="email" value="' . htmlspecialchars($user['email']) . '" required readonly>

                    <label for="address">Shipping Address</label>
                    <select name="address" required>
                        <option value="" selected disabled>Pickup Location</option>
                        <option value="Emmen">Emmen</option>
                    </select>

                    <label for="cc_number">Credit Card Number (@payment provider)</label>
                    <input type="text" id="cc_number" name="cc_number" placeholder="XXXX XXXX XXXX XXXX">

                    <label for="lease_duration">Lease Duration (in days)</label>
                    <input type="number" id="lease_duration" name="lease_duration" value="7" min="1" max="30" required>
                    
                    <button type="submit" name="confirm_lease" class="btn" style="width: 100%; margin-top: 20px;">Confirm & Finalize Lease</button>
                </form>
            ';
        }
    } else {
         $car_details_html = '<p class="error-message">Could not find the selected car or user.</p>';
    }
} else {
    $car_details_html = '<p class="error-message">No car was selected. Please <a href="/browse">go back to the browse page</a> and select a car.</p>';
}

// Load the view template and replace placeholders
ob_start();
include __DIR__ . '/view/checkout.html';
$content = ob_get_clean();
$content = str_replace('{{car_details}}', $car_details_html, $content);
$content = str_replace('{{checkout_form}}', $checkout_form_html, $content);
echo $content;

include __DIR__ . '/_footer.php';
