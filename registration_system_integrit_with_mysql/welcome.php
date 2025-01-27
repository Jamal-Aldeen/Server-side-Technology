<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
        <div class="user-info">
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Room: <?= htmlspecialchars($user['room']) ?></p>
            <p>Username: <?= htmlspecialchars($user['username']) ?></p>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>