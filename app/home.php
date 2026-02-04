<?php

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




