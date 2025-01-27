<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $room = $_POST['room'] ?? '';
    $username = trim($_POST['username'] ?? '');

    // Name validation
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    // Email validation
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Password validation
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) !== 8 || !preg_match('/^[a-z0-9_]+$/', $password)) {
        $errors['password'] = "Password must be 8 characters, lowercase, numbers, or underscores only";
    }

    // Confirm password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Room validation
    $valid_rooms = ['Application1', 'Application2', 'Cloud'];
    if (empty($room) || !in_array($room, $valid_rooms)) {
        $errors['room'] = "Invalid room selection";
    }

    // Username validation
    if (empty($username)) {
        $errors['username'] = "Username is required";
    }

    // If no errors, proceed with saving data
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $stmt = $db->prepare("INSERT INTO users (name, email, password, room, username) VALUES (:name, :email, :password, :room, :username)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':room' => $room,
            ':username' => $username
        ]);

        // Redirect to login page
        header('Location: login.php');
        exit();
    }

    // If there are errors, store them in session
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: register.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Registration Form</h2>
        
        <form action="register.php" method="POST">
            <!-- Name Field -->
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['name'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['name']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Email Field -->
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['email'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['email']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password">
                <?php if (isset($_SESSION['errors']['password'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['password']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password">
                <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['confirm_password']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Room Field -->
            <div class="form-group">
                <label>Room No.:</label>
                <select name="room">
                    <option value="">-- Select Room --</option>
                    <option value="Application1" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Application1' ? 'selected' : '' ?>>Application1</option>
                    <option value="Application2" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Application2' ? 'selected' : '' ?>>Application2</option>
                    <option value="Cloud" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Cloud' ? 'selected' : '' ?>>Cloud</option>
                </select>
                <?php if (isset($_SESSION['errors']['room'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['room']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Username Field -->
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['username'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['username']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
<?php
// Clear errors and old input after displaying them
unset($_SESSION['errors'], $_SESSION['old']);
?>