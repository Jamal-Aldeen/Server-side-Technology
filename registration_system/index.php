<?php
session_start();

// Redirect to welcome page if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: welcome.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Application</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome to Our Application</h1>
        <p>Please log in or sign up to continue.</p>
        
        <div class="action-buttons">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Sign Up</a>
        </div>
    </div>
</body>
</html> 
