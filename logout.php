<?php
// logout.php - Destroy session and redirect to home

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header('Location: index.html');
exit();
?>