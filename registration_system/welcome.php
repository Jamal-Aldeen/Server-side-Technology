<?php
session_start();

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
        <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="profile-img">
        <div class="user-info">
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Room: <?= htmlspecialchars($user['room']) ?></p>
            <p>Username: <?= htmlspecialchars($user['username']) ?></p>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>