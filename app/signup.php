<?php

$error = '';
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (strlen ($_POST['fullname']) < 3) {
        $error = 'Full name must be at least 3 characters.';
    }   

    if (strlen ($_POST['fullname']) > 100) {
        $error = 'Full name must not exceed 100 characters.';
    }    

    if (strlen ($_POST['username']) < 3) {
        $error = 'Username must be at least 3 characters.';
    }

    if (strlen ($_POST['username']) > 100) {
        $error = 'Username must not exceed 100 characters.';
    }

    if (strlen ($_POST['password']) < 7) {
        $error = 'Password must be at least 7 characters.';
    }

    if ($_POST['password'] !== $_POST['passwordconfirm']) {
        $error = 'Passwords do not match.';
    }

    $q = $db->prepare('SELECT ID FROM account WHERE username = :username');
    $q->execute([
        'username' => $_POST['username'],
    ]);
    $result = $q->fetch(PDO::FETCH_ASSOC);
    if (!empty($result['ID'])) {
        $error = 'Username already taken.';
    }

    $q = $db->prepare('SELECT ID FROM account WHERE email = :email');
    $q->execute([
        'email' => $_POST['email'],
    ]);
    $result = $q->fetch(PDO::FETCH_ASSOC);
    if (!empty($result['ID'])) {
        $error = 'An account on this email already exists.';
    }

    if (empty($error)) {

        $q = $db->prepare('INSERT INTO account SET 
            license = :license,
            fullname = :fullname,
            username = :username,
            password = :password,
            email = :email
            ');
        $q->execute([
            'license' => $_POST['license'],
            'fullname' => $_POST['fullname'],
            'username' => $_POST['username'],
            'password' => $_POST['password'], // @todo password hash
            'email' => $_POST['email'],
        ]);
        $id = $db->lastInsertId();

        $body = file_get_contents(__DIR__.'/view/email_welcome.html');
        $body = str_replace('{{fullname}}', $_POST['fullname'], $body);
        mail( $_POST['email'], "Welcome to Razenderon's Autoverhuur!", $body);

        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['fullName'] = $_POST['fullname'];
        $_SESSION['logintime'] = new DateTime();
        $_SESSION['isAdmin'] = 0;
        
        header('Location: /browse');
        exit();
    }
}

if (!empty($error)) {
    $error = '<div class="error-message">' . $error . '</div>';
}

$html = file_get_contents(__DIR__.'/view/signup.html');
$html = str_replace('{{error}}', $error, $html);
echo $html;