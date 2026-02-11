<?php
// Start session for potential user authentication
session_start();

// Sample featured cars array (you can replace this with database queries later)
$featuredCars = [
    [
        'id' => 1,
        'name' => 'Toyota Camry 2024',
        'price' => '25,999',
        'image' => 'camry.jpg',
        'year' => '2024'
    ],
    [
        'id' => 2,
        'name' => 'Honda Civic 2024',
        'price' => '22,499',
        'image' => 'civic.jpg',
        'year' => '2024'
    ],
    [
        'id' => 3,
        'name' => 'Tesla Model 3 2024',
        'price' => '39,990',
        'image' => 'tesla.jpg',
        'year' => '2024'
    ],
    [
        'id' => 4,
        'name' => 'Ford Mustang 2023',
        'price' => '35,500',
        'image' => 'mustang.jpg',
        'year' => '2023'
    ]
];

// Get current year for copyright
$currentYear = date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jazaq AutoHub - Online Car Dealership</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* You can move this to style.css file */
        .car-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .car-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .car-card .price {
            color: #e44d26;
            font-size: 1.2em;
            font-weight: bold;
        }
        .car-card .year {
            color: #666;
            font-size: 0.9em;
        }
        .btn-small {
            display: inline-block;
            padding: 8px 16px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .btn-small:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Jazaq AutoHub</a>
            <ul class="nav-links">
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="cars.php"><i class="fas fa-car"></i> Cars</a></li>
                <li><a href="#about"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="#contact"><i class="fas fa-phone"></i> Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1>Find Your Dream Car Today</h1>
            <p>Browse our wide selection of quality vehicles at affordable prices</p>
            <a href="cars.php" class="btn">Browse Cars</a>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Choose Jazaq AutoHub</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Quality Checked</h3>
                    <p>All vehicles undergo thorough inspection</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-tag"></i>
                    <h3>Best Prices</h3>
                    <p>Competitive pricing guaranteed</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Our team is always ready to help</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Cars Preview -->
    <section class="featured-cars">
        <div class="container">
            <h2>Featured Cars</h2>
            <div class="cars-grid" id="featuredCars">
                <?php if (!empty($featuredCars)): ?>
                    <?php foreach ($featuredCars as $car): ?>
                        <div class="car-card">
                            <img src="images/<?php echo htmlspecialchars($car['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($car['name']); ?>"
                                 onerror="this.src='images/default-car.jpg'">
                            <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                            <p class="year">Year: <?php echo htmlspecialchars($car['year']); ?></p>
                            <p class="price">$<?php echo htmlspecialchars($car['price']); ?></p>
                            <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn-small">View Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No featured cars available at the moment.</p>
                <?php endif; ?>
            </div>
            <a href="cars.php" class="btn">View All Cars</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo $currentYear; ?> Jazaq Car Dealership. Educational Project.</p>
        </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="script.js"></script>
    
    <?php
    // Optional: Add PHP debug information (remove in production)
    if (isset($_GET['debug'])) {
        echo '<!-- PHP Version: ' . phpversion() . ' -->';
    }
    ?>
</body>
</html>
