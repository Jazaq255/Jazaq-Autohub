<?php
session_start();
require_once 'config.php'; // Make sure this connects $conn to your DB

// Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = "";
$success = "";

// Handle form submission
if (isset($_POST['submit'])) {

    // Sanitize inputs
    $make          = mysqli_real_escape_string($conn, $_POST['make']);
    $model         = mysqli_real_escape_string($conn, $_POST['model']);
    $year          = intval($_POST['year']);
    $price         = floatval($_POST['price']);
    $mileage       = isset($_POST['mileage']) ? intval($_POST['mileage']) : 0;
    $fuel_type     = mysqli_real_escape_string($conn, $_POST['fuel_type']);
    $transmission  = mysqli_real_escape_string($conn, $_POST['transmission']);
    $color         = mysqli_real_escape_string($conn, $_POST['color']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);

    $image_url = "";

    // Basic validation
    if ($make === "" || $model === "" || $year <= 0 || $price <= 0) {
        $error = "Please fill all required fields correctly";
    }

    // IMAGE UPLOAD
    if (!$error && isset($_FILES['car_image']) && $_FILES['car_image']['name'] != "") {

        $target_dir = "uploads/cars/";

        // Create folder if not exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["car_image"]["name"]);
        $target_file = $target_dir . $image_name;

        $image_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_ext, $allowed)) {
            $error = "Only JPG, JPEG, PNG, GIF images allowed";
        } else {
            if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
                $image_url = $target_file; // Store relative path in DB
            } else {
                $error = "Image upload failed";
            }
        }
    }

    // INSERT INTO DATABASE
    if (!$error) {
        $sql = "INSERT INTO cars 
        (make, model, year, price, mileage, fuel_type, transmission, color, description, image_url)
        VALUES
        ('$make', '$model', $year, $price, $mileage, '$fuel_type', '$transmission', '$color', '$description', '$image_url')";

        if (mysqli_query($conn, $sql)) {
            $success = "Car added successfully!";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Car - Jazaq AutoHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.html" class="logo">AutoHub</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="cars.php">Cars</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container" style="margin-top:40px;">
    <h2>Add New Car</h2>

    <?php if ($error): ?>
        <div style="background:#ffe6e6;padding:10px;margin:10px 0;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background:#e6ffe6;padding:10px;margin:10px 0;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Make *</label>
            <input type="text" name="make" required>
        </div>

        <div class="form-group">
            <label>Model *</label>
            <input type="text" name="model" required>
        </div>

        <div class="form-group">
            <label>Year *</label>
            <input type="number" name="year" required min="1900" max="<?php echo date('Y')+1; ?>">
        </div>

        <div class="form-group">
            <label>Price *</label>
            <input type="number" name="price" step="0.01" required min="0">
        </div>

        <div class="form-group">
            <label>Mileage</label>
            <input type="number" name="mileage" min="0">
        </div>

        <div class="form-group">
            <label>Fuel Type</label>
            <select name="fuel_type">
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>

        <div class="form-group">
            <label>Transmission</label>
            <select name="transmission">
                <option value="Automatic">Automatic</option>
                <option value="Manual">Manual</option>
            </select>
        </div>

        <div class="form-group">
            <label>Color</label>
            <input type="text" name="color">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <div class="form-group">
            <label>Car Image *</label>
            <input type="file" name="car_image" accept="image/*" required>
        </div>

        <button type="submit" name="submit" class="btn">Add Car</button>
    </form>
</div>

</body>
</html>
