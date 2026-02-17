<?php

// If user is already logged in, redirect to the main page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: /browse');
    exit;
}

$error = '';
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //db connection
    require_once '../config/config.php';
    try {
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }


    // prepare and execute query
    $q = $db->prepare('SELECT ID, fullName, username, isAdmin FROM account WHERE username = :username AND password = :password');
    $q->execute([
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ]);
    $user = $q->fetch(PDO::FETCH_ASSOC);

    // @todo wachtwoord hash toevoegen!

    if (!empty ($user)) {
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullName'] = $user['fullName'];
        $_SESSION['logintime'] = new DateTime();
        $_SESSION['isAdmin'] = $user['isAdmin'];


        // Redirect to browse page
        header('Location: /browse');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}


if ($error) {
    $error = '<p class="error-message">' . $error . '</p>';
}

$html = file_get_contents(__DIR__.'/view/login.html');
$html = str_replace('{{error}}', $error, $html);
echo $html;