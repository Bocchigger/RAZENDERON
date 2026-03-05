<?php


$currentPage = $URI;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Razenderon Car Rentals'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="background-image"></div>
    <div class="navbar">
        <a href="/browse" class="<?php echo ($currentPage == '/browse') ? 'active' : ''; ?>">Browse Cars</a>
        <a href="/active_leases" class="<?php echo ($currentPage == '/active_leases') ? 'active' : ''; ?>">Active Leases</a>
        <a href="/profile" class="<?php echo ($currentPage == '/profile') ? 'active' : ''; ?>">Profile</a>
        <a href="/compare" class="<?php echo ($currentPage == '/compare') ? 'active' : ''; ?>">Compare Cars</a>
        <a href="/favorites" class="<?php echo ($currentPage == '/favorites') ? 'active' : ''; ?>">Favorites</a>
        <?php if (isAdmin()): ?>
            <a href="/admin_panel" class="<?php echo ($currentPage == '/admin_panel') ? 'active' : ''; ?>">Admin Panel</a>
        <?php endif; ?>
        <div class="right">
            <?php if (loggedIn()): ?>
                <a href="/profile" class="<?php echo ($currentPage == '/profile') ? 'active' : ''; ?>"><?php echo $_SESSION['username']; ?></a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="content-wrapper">
<!-- end of header -->
