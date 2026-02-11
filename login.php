<?php
session_start();
require "config.php"; // Make sure $conn is created here

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simple validation
    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {

        // Read from users table
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Check password using your column: passsword (triple s)
            if (password_verify($password, $user['passsword'])) {

                // Create session
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();

            } else {
                $error = "Wrong password!";
            }

        } else {
            $error = "User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AutoHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="logo">Jazaq AutoHub</a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2>Login into account</h2>
        
        <?php if ($error): ?>
            <div class="error" style="background: #ffe6e6; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #e6ffe6; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Choose a username">
            </div>
            
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required 
                       placeholder="At least 6 characters">
            </div>
            
            
           <button type="submit" name="submit" class="btn">login</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center;">
            don't you have an account? <a href="register.php">register here</a>
        </p>
    </div>

    <script src="script.js"></script>
</body>
</html>