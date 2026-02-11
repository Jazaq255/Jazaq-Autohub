<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get session data
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role     = $_SESSION['role'];

// Get cars if admin or for display
$cars = [];
$query = "SELECT * FROM cars ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $cars[] = $row;
}

// Get all orders for admin view
$orders = [];
if ($role === 'admin') {
    $orders_query = "SELECT orders.*, users.username, cars.make, cars.model, cars.year 
                     FROM orders 
                     JOIN users ON orders.user_id = users.id 
                     JOIN cars ON orders.car_id = cars.id
                     ORDER BY orders.created_at DESC";
    $orders_result = mysqli_query($conn, $orders_query);
    while ($order = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $order;
    }

    // Get all users for admin
    $users = [];
    $users_query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
    $users_result = mysqli_query($conn, $users_query);
    while ($user_row = mysqli_fetch_assoc($users_result)) {
        $users[] = $user_row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard -Jazaq AutoHub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="logo">Jazaq AutoHub</a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="cars.php">Cars</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <!-- Friendly Welcome -->
        <h1>
            <?php if ($role === 'admin'): ?>
                Welcome Admin, <?php echo htmlspecialchars($username); ?> 
            <?php else: ?>
                Welcome Customer, <?php echo htmlspecialchars($username); ?> 
            <?php endif; ?>
        </h1>

        <?php if ($role === 'admin'): ?>
            <div style="margin: 30px 0;">
                <a href="add_car.php" class="btn">Add New Car</a>
            </div>
            
            <h2>Manage Cars</h2>
            <?php if (empty($cars)): ?>
                <p>No cars in inventory. <a href="add_car.php">Add your first car</a></p>
            <?php else: ?>
                <div class="cars-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:20px;">
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card" style="background:white; padding:15px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
                            <div class="car-image" style="height:200px; overflow:hidden; border-radius:5px; margin-bottom:10px;">
                                <?php if (!empty($car['image_url']) && file_exists($car['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                         alt="Car Image" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <i class="fas fa-car" style="font-size: 60px; color: #666; display:flex; justify-content:center; align-items:center; height:100%;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="car-details">
                                <h3><?php echo htmlspecialchars($car['year']) . ' ' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']); ?></h3>
                                <div class="car-price">Tsh<?php echo number_format($car['price'], 2); ?></div>
                                <p><?php echo htmlspecialchars($car['color']); ?> | <?php echo htmlspecialchars($car['mileage']); ?> miles</p>
                                
                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <a href="edit_car.php?id=<?php echo $car['id']; ?>" 
                                       class="btn" style="background: #3498db; flex: 1; text-align: center;">
                                        Edit
                                    </a>
                                    <a href="delete_car.php?id=<?php echo $car['id']; ?>" 
                                       class="btn" style="background: #e74c3c; flex: 1; text-align: center;"
                                       onclick="return confirm('Are you sure you want to delete this car?')">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Admin View Orders -->
            <h2 style="margin-top:50px;">All Orders</h2>
            <?php if (empty($orders)): ?>
                <p>No orders yet.</p>
            <?php else: ?>
                <table class="orders-table" border="1" cellpadding="10" cellspacing="0" style="width:100%; margin-top:20px;">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Car</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Ordered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo htmlspecialchars($order['year'] . ' ' . $order['make'] . ' ' . $order['model']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td><?php echo $order['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- ADMIN: Manage Users -->
            <h2 style="margin-top:50px;">Manage Users</h2>
            <?php if (empty($users)): ?>
                <p>No users found.</p>
            <?php else: ?>
                <table border="1" width="100%" cellpadding="10" cellspacing="0" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo $u['role']; ?></td>
                                <td><?php echo $u['created_at']; ?></td>
                                <td>
                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                           onclick="return confirm('Delete this user?');" style="color:red;">Delete</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php else: ?>
            <!-- User Panel -->
            <div style="margin: 30px 0;">
                <h2>Available Cars</h2>
                <div class="cars-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:20px;">
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card" style="background:white; padding:15px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
                            <div class="car-image" style="height:200px; overflow:hidden; border-radius:5px; margin-bottom:10px;">
                                <?php if (!empty($car['image_url']) && file_exists($car['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                         alt="Car Image" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <i class="fas fa-car" style="font-size: 60px; color: #666; display:flex; justify-content:center; align-items:center; height:100%;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="car-details">
                                <h3><?php echo htmlspecialchars($car['year']) . ' ' . htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']); ?></h3>
                                <div class="car-price">Tsh<?php echo number_format($car['price'], 2); ?></div>
                                <p><?php echo htmlspecialchars($car['color']); ?> | <?php echo htmlspecialchars($car['mileage']); ?> miles</p>
                                <a href="order.php?car_id=<?php echo $car['id']; ?>" class="btn" style="margin-top:10px; background:#27ae60;">Order Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
