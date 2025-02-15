<?php
session_start();
require 'db.php';

// Redirect to welcome page if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: welcome.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Admin login
    if ($email === 'admin@gmail.com' && $password === 'admin') {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit();
    }

    // Regular user login
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header('Location: welcome.php');
        exit();
    }

    // Login failed
    $_SESSION['error'] = "Invalid email or password";
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-box">
                <p><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>