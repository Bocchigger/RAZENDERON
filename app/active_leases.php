<?php
$pageTitle = 'Your Active Leases - Razenderon';
include '_header.php';

$user_id = $_SESSION['id'];

// Check for a success message from the checkout page
$successMessageHtml = '';
if (isset($_SESSION['success_message'])) {
    $successMessageHtml = '<div class="card" style="background-color: #28a745; color: white; margin-bottom: 20px;">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    // Unset the message so it doesn't show again on refresh
    unset($_SESSION['success_message']);
}

// Fetch active leases for the user from the database
$sql = "SELECT 
            l.ID, 
            l.car,
            l.startDay, 
            l.lastDay,
            l.totalCost,
            c.*
        FROM 
            lease l
        JOIN 
            car c ON l.car = c.ID
        WHERE 
            l.account = :account
        ORDER BY 
            l.startDay ASC";

$stmt = $db->prepare($sql);
$stmt->execute(['account' => $user_id]);
$activeLeases = $stmt->fetchAll();

$content = file_get_contents(__DIR__ . '/view/active_leases.html');

$activeLeasesHtml = '';
if (!empty($activeLeases)) {
    
    foreach ($activeLeases as $lease) {
        
        if (empty($lease['image'])) {
            $lease['image'] = 'auto_placeholder.png';
        }   

        $activeLeasesHtml .= '
            <div class="card" style="text-align: left; margin-bottom: 20px; display: flex; gap: 20px; align-items: center;">
                <img src="/images/' . htmlspecialchars($lease['image']) . '" alt="Car Image" style="width: 150px; height: auto; border-radius: 4px;">
                <div>
                    <h4>' . htmlspecialchars($lease['brand']) . ' ' . htmlspecialchars($lease['model']) . ' (' . $lease['age'] . ')</h4>
                    <p><strong>Lease ID:</strong> ' . $lease['ID'] . '</p>
                    <p><strong>Period:</strong> ' . htmlspecialchars($lease['startDay']) . ' to ' . htmlspecialchars($lease['lastDay']) . '</p>
                    <p><strong>Total Cost:</strong> &euro;' . htmlspecialchars(number_format($lease['totalCost'], 2)) . '</p>
                </div>
            </div>
        ';
    }
} else {
    $activeLeasesHtml = '<p>You have no active leases. <a href="/browse">Why not rent one?</a></p>';
}

$content = str_replace('{{active_leases}}', $successMessageHtml . $activeLeasesHtml, $content);

echo $content;

include '_footer.php';
