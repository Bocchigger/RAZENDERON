<?php


if (!isset($_SESSION['username'])) {
    header('Location: /login');
    exit;
}

$cars = file_get_contents(__DIR__.'/../var/cars.json');

$cars = json_decode($cars, true);

if (empty($cars)){
    exit("geen data");
}

require_once '../config/config.php';
try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

$q = $db->prepare('SELECT COUNT(*) FROM car');
$q->execute();
$count = $q->fetchColumn();
if ($count > 1) {
    exit("er staan al $count auto's in de database");
}

foreach ($cars as $car){
    echo $car["brand"] . " " . $car["model"] . "<br>";
    
    $q = $db->prepare('INSERT INTO car SET 
        id = :id,
        brand = :brand, 
        model = :model,
        type = :type,
        age = :age,
        seats = :seats,
        towbar = :towbar,
        color = :color,
        winter_tires = :winter_tires,
        roofbox_option = :roofbox_option,
        class = :class,
        isAvailable = 1,
        hasDamage = 0,
        hasInsurance = 1,
        transmission = "manual"

        ');

    if (!in_array($car["type"], ["electric", "petrol", "diesel", "hybrid"] )){
        echo $car["_id"] . " error in type" . "<br>";
        continue;
    }

    if (!in_array($car["class"], ["A", "B", "C", "D"] )){
        echo $car["_id"] . " error in class" . "<br>";
        continue;
    }



    $q->execute([
        'id' => $car["_id"],
        'brand' => $car["brand"],
        'model' => $car["model"],
        'type' => $car["type"],
        'age' => $car["age"],
        'seats' => $car["seats"],
        'towbar' => $car["towbar"],
        'color' => substr($car["color"], 0, 30),
        'winter_tires' => $car["winter_tires"],
        'roofbox_option' => $car["roofbox_option"],
        'class' => $car["class"]
    ]);
}

echo "done";







