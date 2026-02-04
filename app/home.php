<?php
session_start();

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
echo " - this is the homepage.";


$id = 4;

$q = $db->prepare('SELECT * FROM lease where id = :id');
$q->execute(['id' => $id]);

$result = $q->fetchAll(PDO::FETCH_ASSOC);


echo "<pre>";
var_dump($result);
echo "</pre>";
*/



$time = time();

$html = file_get_contents(__DIR__.'/view/home.html');

$html = str_replace('{{time}}', $time, $html);
$html = str_replace('{{username}}', $_SESSION['username'], $html);

echo $html;



