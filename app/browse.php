<?php

/*
require_once '../config/config.php';
try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

echo time();
echo " - this is the browsepage.";


$id = 4;

$q = $db->prepare('SELECT * FROM lease where id = :id');
$q->execute(['id' => $id]);

$result = $q->fetchAll(PDO::FETCH_ASSOC);


echo "<pre>";
var_dump($result);
echo "</pre>";
*/

if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}


$time = time();

$html = file_get_contents(__DIR__.'/view/browse.html');

// $html = str_replace('{{time}}', $time, $html);
$html = str_replace('{{time}}', (new DateTime())->format('Y-m-d H:i:s'), $html);

$html = str_replace('{{fullName}}', $_SESSION['fullName'], $html);


echo $html;

echo "<br><br>";
echo("Quick links: <a href='/login'>Login</a> | <a href='/logout'>Logout</a> | <a href='/welcome'>Welcome</a> | <a href='/404'>404 Page</a>");

