<?php
session_start();
require_once 'config.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php?error=Invalid car ID');
    exit();
}

$car_id = intval($_GET['id']);

// Delete related orders first
$delete_orders = $conn->prepare("DELETE FROM orders WHERE car_id = ?");
$delete_orders->bind_param("i", $car_id);
$delete_orders->execute();

// Then delete car
$delete_car = $conn->prepare("DELETE FROM cars WHERE id = ?");
$delete_car->bind_param("i", $car_id);

if ($delete_car->execute()) {
    header('Location: dashboard.php?success=Car and its orders deleted successfully');
} else {
    header('Location: dashboard.php?error=Failed to delete car: ' . urlencode($conn->error));
}

$delete_orders->close();
$delete_car->close();
$conn->close();
exit();
?>
