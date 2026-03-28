<?php

require_once 'helper.php';

session_start();

$db = connect_db();

$URI = $_SERVER['PATH_INFO'] ?? '/';
// var_dump($_SERVER);

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

    case '/signup':
        require_once 'signup.php';
        break;

    case '/browse':
        require_once 'browse.php';
        break;

    case '/import':
        require_once 'import.php';
        break;

    case '/active_leases':
        require_once 'active_leases.php';
        break;

    case '/profile':
        require_once 'profile.php';
        break;

    case '/compare':
        require_once 'compare.php';
        break;

    case '/favorites':
        require_once 'favorites.php';
        break;

    case '/admin_panel':
        require_once 'admin_panel.php';
        break;

    case '/checkout':
        require_once 'checkout.php';
        break; 

    default:
        require_once '404.php';
        break;


}