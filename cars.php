<?php
session_start();
require_once 'config.php';

// Check DB connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get filter values
$search       = $_GET['search'] ?? '';
/* ❗ FIX: Convert price filters to float */
$min_price    = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price    = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : 100000000;

$make_filter  = $_GET['make'] ?? '';

// Base SQL
$sql = "SELECT * FROM cars WHERE price BETWEEN ? AND ?";
$params = [$min_price, $max_price];
$types  = "dd";

// Search filter
if (!empty($search)) {
    $sql .= " AND (make LIKE ? OR model LIKE ? OR description LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like, $like);
    $types .= "sss";
}

// Make filter
if (!empty($make_filter)) {
    $sql .= " AND make = ?";
    $params[] = $make_filter;
    $types .= "s";
}

$sql .= " ORDER BY year DESC";

// Prepare & execute
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch makes for dropdown
$makes_result = mysqli_query($conn, "SELECT DISTINCT make FROM cars ORDER BY make");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory - Jazaq AutoHub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.html" class="logo">AutoHub</a>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="cars.php">Cars</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container" style="margin-top:40px;">
    <h1>Car Inventory</h1>

    <!-- FILTER FORM -->
    <div style="background:white;padding:20px;border-radius:8px;margin:30px 0;">
        <form method="GET">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">

                <div class="form-group">
                    <label>Search</label>
                    <input type="text" name="search"
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search make, model, description">
                </div>

                <div class="form-group">
                    <label>Min Price (TZS)</label>
                    <input type="number" name="min_price" min="0"
                           value="<?php echo $min_price; ?>">
                </div>

                <div class="form-group">
                    <label>Max Price (TZS)</label>
                    <input type="number" name="max_price" min="0"
                           value="<?php echo $max_price != 100000000 ? $max_price : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Car Make</label>
                    <select name="make">
                        <option value="">All Makes</option>
                        <?php while ($m = mysqli_fetch_assoc($makes_result)): ?>
                            <option value="<?php echo htmlspecialchars($m['make']); ?>"
                                <?php echo ($m['make'] === $make_filter) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['make']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn" style="margin-top:20px;">Apply Filters</button>
            <a href="cars.php" class="btn" style="margin-top:20px;background:#95a5a6;">Clear</a>
        </form>
    </div>

    <!-- RESULTS -->
    <h2>Available Cars (<?php echo mysqli_num_rows($result); ?>)</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="cars-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
            <?php while ($car = mysqli_fetch_assoc($result)): ?>
                <div style="background:white;padding:15px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,.1);">

                    <div style="height:200px;margin-bottom:10px;overflow:hidden;">
                        <?php if (!empty($car['image_url'])): /* ❗ FIX: remove file_exists */ ?>
                            <img src="<?php echo htmlspecialchars($car['image_url']); ?>"
                                 style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <i class="fas fa-car"
                               style="font-size:60px;color:#777;display:flex;justify-content:center;align-items:center;height:100%;"></i>
                        <?php endif; ?>
                    </div>

                    <h3>
                        <?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?>
                    </h3>

                    <div class="car-price">
                        TZS <?php echo number_format($car['price']); ?>
                    </div>

                    <p>
                        <i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage']); ?> km<br>
                        <i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?><br>
                        <i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['transmission']); ?><br>
                        <i class="fas fa-palette"></i> <?php echo htmlspecialchars($car['color']); ?>
                    </p>

                    <p><?php echo htmlspecialchars(substr($car['description'], 0, 100)); ?>...</p>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn" style="background:#3498db;">
                            Edit
                        </a>
                    <?php endif; ?>

                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="background:white;padding:40px;border-radius:8px;text-align:center;">
            <h3>No cars found</h3>
            <p>Try changing the filters</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
