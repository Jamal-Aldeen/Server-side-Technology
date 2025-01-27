<?php
session_start();

// Define the secret key directly in the file
define('SECRET_KEY', 'd4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4'); // Replace with your generated key

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
    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user_json) {
        $user = json_decode($user_json, true);
        if ($user['email'] === $email) {
            // Hash the input password with the secret key
            $hashed_password = hash_hmac('sha256', $password, SECRET_KEY);

            // Compare the hashed passwords
            if ($hashed_password === $user['password']) {
                $_SESSION['user'] = $user;
                header('Location: welcome.php');
                exit();
            }
        }
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