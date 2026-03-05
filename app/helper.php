<?php

// This file contains general helper functions

function loggedIn()
{
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        return true;
    }

    return false;
}


function isAdmin()
{
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
        return true;
    }

    return false;
}

function connect_db()
{
    require_once '../config/config.php';
    
    try {
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }

    return $db;
}