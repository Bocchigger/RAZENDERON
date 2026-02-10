<?php

$URI = $_SERVER['REQUEST_URI'];

switch ($URI) {


    case '/':
        require_once 'welcome.php';
        break;

    case '/home':
        require_once 'welcome.php';
        break;

    case '/welcome':
        require_once 'welcome.php';
        break;

    case '/login':
        require_once 'login.php';
        break;

    case '/logout':
        require_once 'logout.php';
        break;

    case '/browse':
        require_once 'browse.php';
        break;

    default:
        require_once '404.php';
        break;


}