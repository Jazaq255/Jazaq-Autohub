<?php
// edit_car.php - Allows admin to edit existing car details
session_start();

// ------------------- DATABASE CONNECTION -------------------
$host = "localhost";       // usually localhost
$user = "root";            // your DB username
$password = "";            // your DB password
$dbname = "car-dealership";  // replace with your DB name

$conn = mysqli_connect($host, $user, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// ------------------- END DATABASE CONNECTION -------------------

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if car ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$car_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch car details
$query = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    $error = 'Car not found!';
    $car = null;
} else {
    $car = mysqli_fetch_assoc($result);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = mysqli_real_escape_string($conn, $_POST['make']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    $mileage = intval($_POST['mileage']);
    $fuel_type = mysqli_real_escape_string($conn, $_POST['fuel_type']);
    $transmission = mysqli_real_escape_string($conn, $_POST['transmission']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Validation
    if (empty($make) || empty($model) || empty($year) || empty($price)) {
        $error = 'Please fill in all required fields';
    } elseif ($year < 1900 || $year > date('Y') + 1) {
        $error = 'Please enter a valid year';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } else {
        $update_query = "UPDATE cars SET 
                         make='$make',
                         model='$model',
                         year=$year,
                         price=$price,
                         mileage=$mileage,
                         fuel_type='$fuel_type',
                         transmission='$transmission',
                         color='$color',
                         description='$description'
                         WHERE id=$car_id";
        if (mysqli_query($conn, $update_query)) {
            $success = 'Car updated successfully!';
            $result = mysqli_query($conn, "SELECT * FROM cars WHERE id=$car_id");
            $car = mysqli_fetch_assoc($result);
        } else {
            $error = 'Failed to update car: ' . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Car - Jazaq AutoHub</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="container">
        <a href="index.html" class="logo">AutoHub</a>
        <ul class="nav-links">
            <li><a href="index.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="cars.php"><i class="fas fa-car"></i> Cars</a></li>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container" style="margin-top: 40px;">
    <?php if ($error && !$car): ?>
        <div class="error" style="background: #ffe6e6; padding: 20px; border-radius: 5px; text-align: center;">
            <h2><i class="fas fa-exclamation-triangle"></i> Error</h2>
            <p><?php echo $error; ?></p>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    <?php else: ?>
        <h1><i class="fas fa-edit"></i> Edit Car</h1>
        <p>Editing: <?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?></p>
        
        <?php if ($error): ?>
            <div class="error" style="background: #ffe6e6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #e6ffe6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Make *</label>
                    <input type="text" name="make" value="<?php echo htmlspecialchars($car['make']); ?>" required>
                    <label>Model *</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                    <label>Year *</label>
                    <input type="number" name="year" min="1900" max="<?php echo date('Y')+1; ?>" value="<?php echo $car['year']; ?>" required>
                    <label>Price ($) *</label>
                    <input type="number" name="price" min="0" step="0.01" value="<?php echo $car['price']; ?>" required>
                    <label>Mileage</label>
                    <input type="number" name="mileage" min="0" value="<?php echo $car['mileage']; ?>">
                </div>
                <div>
                    <label>Fuel Type</label>
                    <select name="fuel_type">
                        <option value="Petrol" <?php echo ($car['fuel_type']=='Petrol')?'selected':''; ?>>Petrol</option>
                        <option value="Diesel" <?php echo ($car['fuel_type']=='Diesel')?'selected':''; ?>>Diesel</option>
                        <option value="Electric" <?php echo ($car['fuel_type']=='Electric')?'selected':''; ?>>Electric</option>
                        <option value="Hybrid" <?php echo ($car['fuel_type']=='Hybrid')?'selected':''; ?>>Hybrid</option>
                    </select>
                    <label>Transmission</label>
                    <select name="transmission">
                        <option value="Automatic" <?php echo ($car['transmission']=='Automatic')?'selected':''; ?>>Automatic</option>
                        <option value="Manual" <?php echo ($car['transmission']=='Manual')?'selected':''; ?>>Manual</option>
                    </select>
                    <label>Color</label>
                    <input type="text" name="color" value="<?php echo htmlspecialchars($car['color']); ?>">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($car['description']); ?></textarea>
                </div>
            </div>
            <div style="margin-top:20px;">
                <button type="submit" class="btn"><i class="fas fa-save"></i> Update Car</button>
                <a href="dashboard.php" class="btn" style="background:#95a5a6;"><i class="fas fa-times"></i> Cancel</a>
                <a href="delete_car.php?id=<?php echo $car_id; ?>" class="btn" style="background:#e74c3c;" onclick="return confirm('Are you sure? This cannot be undone.')"><i class="fas fa-trash"></i> Delete</a>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>