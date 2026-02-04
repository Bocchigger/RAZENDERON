<?php

$URI = $_SERVER['REQUEST_URI'];

switch ($URI) {


    case '/':
        require_once 'home.php';
        break;

    case '/login':
        require_once 'login.php';
        break;

    case '/logout':
        require_once 'logout.php';
        break;

    default:
        require_once '404.php';
        break;


}