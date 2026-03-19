<?php
$pageTitle = 'Admin Panel - Razenderon';
include __DIR__ . '/_header.php';

// Redirect if not admin
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: /welcome');
    exit;
}

ob_start();
include __DIR__ . '/view/admin_panel.html';
$content = ob_get_clean();

$sql = "SELECT 
            l.ID AS leaseID, 
            l.car,
            l.startDay, 
            l.lastDay,
            l.totalCost,
            c.*,
            a.fullName,
            a.email
        FROM 
            lease l
        JOIN 
            car c ON l.car = c.ID
        JOIN
            account a ON l.account = a.ID
        ORDER BY 
            l.startDay ASC";

$stmt = $db->prepare($sql);
$stmt->execute();
$activeLeases = $stmt->fetchAll();

$rows = '';

foreach ($activeLeases as $lease) {

    $rows .= '<tr><td>' . $lease['leaseID'] . '</td><td>' . $lease['brand'] . ' ' . $lease['model'] .
    '</td><td>' . $lease['age'] . '</td><td>' . $lease['fullName'] . '</td><td>' . $lease['email'] . '</td><td>' . 
    $lease['startDay'] . '</td><td>' . $lease['lastDay'] . '</td><td>' . "---" . '</td></tr>';

}

$content = str_replace('{{unavailable_cars_table_rows}}', $rows, $content);

echo $content;

include __DIR__ . '/_footer.php';
