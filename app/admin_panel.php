<?php
$pageTitle = 'Admin Panel - Razenderon';
include __DIR__ . '/_header.php';

// Redirect if not admin
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: /welcome');
    exit;
}

if(!empty($_GET['return'])) {
    $sql = "SELECT car FROM lease WHERE ID = :leaseID";
    $stmt = $db->prepare($sql);
    $stmt->execute([':leaseID' => (int)$_GET['return']]);
    $carID = $stmt->fetch();
    
    $sql = "DELETE FROM lease WHERE ID = :leaseID";
    $stmt = $db->prepare($sql);
    $stmt->execute([':leaseID' => (int)$_GET['return']]);
    
    $sql = "UPDATE car SET isAvailable = 1 WHERE ID = :carID";
    $stmt = $db->prepare($sql);
    $stmt->execute([':carID' => $carID['car']]);
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
    $lease['startDay'] . '</td><td>' . $lease['lastDay'] . '</td><td><a class="btn btn-danger" onClick="if (!confirm(\'Are you sure to return '. $lease['brand'] . ' ' . $lease['model'] . ' (due ' . $lease['lastDay'] . ')?\')) return false;" href="/admin_panel?return=' . $lease['leaseID'] . '">Return car</a></td></tr>';

}

$content = str_replace('{{unavailable_cars_table_rows}}', $rows, $content);

echo $content;

include __DIR__ . '/_footer.php';
