<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$car_id  = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
$error   = '';
$success = '';

// Get car details
$car = null;
if ($car_id) {
    $res = mysqli_query($conn, "SELECT * FROM cars WHERE id = $car_id");
    if (mysqli_num_rows($res) > 0) {
        $car = mysqli_fetch_assoc($res);
    } else {
        $error = "Car not found.";
    }
}

if (isset($_POST['submit'])) {
    $quantity = intval($_POST['quantity']);
    if ($quantity <= 0) $error = "Quantity must be at least 1.";

    if (!$error) {
        $total_price = $car['price'] * $quantity;
        $sql = "INSERT INTO orders (user_id, car_id, quantity, total_price) VALUES ($user_id, $car_id, $quantity, $total_price)";
        if (mysqli_query($conn, $sql)) {
            $success = "Order placed successfully!";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Car</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="margin-top:40px;">
    <h2>Order Car</h2>

    <?php if ($error): ?>
        <div style="background:#ffe6e6;padding:10px;margin:10px 0;"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div style="background:#e6ffe6;padding:10px;margin:10px 0;"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($car): ?>
        <form method="POST">
            <p><strong><?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?></strong></p>
            <p>Price: Tsh<?php echo number_format($car['price'],2); ?></p>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" value="1" min="1" required>
            </div>
            <button type="submit" name="submit" class="btn">Place Order</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
