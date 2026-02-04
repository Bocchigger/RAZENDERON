<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$isMobile = false;
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
foreach ($mobileKeywords as $keyword) {
    if (strpos($userAgent, $keyword) !== false) {
        $isMobile = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razenderon Car Rentals</title>
    <style>
        /* General Body Styling */
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }

        .background-image {
            background: linear-gradient(to right, #005c97, #363795);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .page-container {
            background-color: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Header and Navigation */
        .main-header {
            background: #ffffff;
            border-bottom: 2px solid #005c97;
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .main-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo h1 {
            margin: 0;
            font-size: 2em;
            color: #005c97;
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #005c97;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(to right, #005c97, #363795);
            color: white;
        }

        .hero-content h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }

        .hero-content p {
            font-size: 1.3em;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .cta-button {
            background-color: #ffffff;
            color: #005c97;
            border: 2px solid #ffffff;
            padding: 15px 30px;
            font-family: 'Roboto', sans-serif;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 5px;
        }

        .cta-button:hover {
            background-color: transparent;
            color: #ffffff;
        }

        /* Content Sections */
        .content-section {
            padding: 60px 30px;
            border-bottom: 1px solid #eee;
        }

        .content-section:last-of-type {
            border-bottom: none;
        }

        .content-section h2 {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 40px;
            color: #005c97;
        }

        /* Fleet Section */
        .fleet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .car-card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .car-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card-content h3 {
            font-size: 1.5em;
            margin-top: 0;
            color: #005c97;
        }

        .card-content p {
            font-size: 1em;
            line-height: 1.6;
            color: #666;
        }

        /* Footer */
        .main-footer {
            text-align: center;
            padding: 30px;
            background: #333;
            color: #f4f7f6;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .main-nav {
                flex-direction: column;
                align-items: center;
            }

            .nav-links {
                margin-top: 15px;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            .nav-links li {
                margin: 10px 0;
            }

            .hero-content h1 {
                font-size: 2.5em;
            }

            .hero-content p {
                font-size: 1.1em;
            }

            .content-section h2 {
                font-size: 2em;
            }

            .fleet-grid {
                grid-template-columns: 1fr;
            }

            .menu-toggle {
                display: flex;
                flex-direction: column;
                justify-content: space-around;
                width: 30px;
                height: 25px;
                cursor: pointer;
                position: absolute;
                top: 25px;
                right: 30px;
            }

            .menu-toggle div {
                width: 100%;
                height: 3px;
                background-color: #333;
                transition: all 0.3s;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }

        body.mobile .main-nav {
            flex-direction: row;
            justify-content: space-between;
        }

        body.mobile .nav-links {
            display: none; /* Initially hide nav links */
            flex-direction: column;
            text-align: center;
            width: 100%;
        }

        body.mobile .nav-links.active {
            display: flex; /* Show when active */
        }

        body.mobile .menu-toggle {
            display: block; /* Show hamburger menu */
            position: absolute;
            top: 25px;
            right: 20px;
            cursor: pointer;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php if ($isMobile) echo 'mobile'; ?>">
    <div class="background-image"></div>
    <div class="page-container">
        <header class="main-header">
            <nav class="main-nav">
                <a href="#" class="nav-logo">
                    <h1>Razenderon</h1>
                </a>
                <div class="menu-toggle">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <ul class="nav-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#fleet">Our Fleet</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main class="content-wrapper">
            <section id="hero" class="hero-section">
                <div class="hero-content">
                    <h1>Your Journey Starts Here</h1>
                    <p>Premium car rentals for any occasion. Quality, comfort, and safety guaranteed.</p>
                    <button class="cta-button">Browse Cars</button>
                </div>
            </section>

            <section id="about" class="content-section">
                <h2>About Razenderon</h2>
                <p>At Razenderon, we are committed to providing our customers with the best rental experience. 
                    Our diverse fleet, competitive prices, and exceptional customer service make us the top choice for car rentals. 
                    Whether for a business trip or a family vacation, we have the perfect car for you.</p>
            </section>

            <section id="fleet" class="content-section">
                <h2>Our Fleet</h2>
                <div class="fleet-grid">
                    <div class="car-card">
                        <img src="https://via.placeholder.com/300x200.png?text=Economy+Car" alt="Economy Car">
                        <div class="card-content">
                            <h3>Economy</h3>
                            <p>Perfect for city trips and fuel efficiency.</p>
                        </div>
                    </div>
                    <div class="car-card">
                        <img src="https://via.placeholder.com/300x200.png?text=SUV" alt="SUV">
                        <div class="card-content">
                            <h3>SUV</h3>
                            <p>Spacious and versatile for family adventures.</p>
                        </div>
                    </div>
                    <div class="car-card">
                        <img src="https://via.placeholder.com/300x200.png?text=Luxury+Sedan" alt="Luxury Sedan">
                        <div class="card-content">
                            <h3>Luxury Sedan</h3>
                            <p>Travel in style and comfort.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="content-section">
                <h2>Contact Us</h2>
                <p>Have questions? Reach out to us anytime.</p>
                <p>Email: contact@razenderon.com | Phone: 123-456-7890</p>
            </section>
        </main>

        <footer class="main-footer">
            <p>&copy; 2026 Razenderon, Inc. All rights reserved.</p>
        </footer>
    </div>
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    </script>
</body>
</html>