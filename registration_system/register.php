<?php
session_start();

define('SECRET_KEY', 'd4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4'); 

// Define allowed file types and max file size
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $room = $_POST['room'] ?? '';
    $username = trim($_POST['username'] ?? '');

    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) !== 8 || !preg_match('/^[a-z0-9_]+$/', $password)) {
        $errors['password'] = "Password must be 8 characters, lowercase, numbers, or underscores only";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    $valid_rooms = ['Application1', 'Application2', 'Cloud'];
    if (empty($room) || !in_array($room, $valid_rooms)) {
        $errors['room'] = "Invalid room selection";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required";
    }

    if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['profile_pic'] = "Profile picture is required";
    } else {
        $file_type = $_FILES['profile_pic']['type'];
        $file_size = $_FILES['profile_pic']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $errors['profile_pic'] = "Only JPG, PNG, and GIF images allowed";
        }

        if ($file_size > $max_size) {
            $errors['profile_pic'] = "File size exceeds 2MB limit";
        }
    }

    if (empty($errors)) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_ext;
        $destination = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
            $hashed_password = hash_hmac('sha256', $password, SECRET_KEY);

            $user_data = [
                'name' => htmlspecialchars($name),
                'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
                'password' => $hashed_password, 
                'room' => $room,
                'username' => htmlspecialchars($username),
                'profile_pic' => $destination
            ];

            $user_json = json_encode($user_data) . PHP_EOL;
            file_put_contents('users.txt', $user_json, FILE_APPEND | LOCK_EX);

            unset($_SESSION['errors'], $_SESSION['old']);
            header('Location: login.php');
            exit();
        } else {
            $errors['profile_pic'] = "Failed to upload profile picture";
        }
    }

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
        
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['name'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['email'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password">
                <?php if (isset($_SESSION['errors']['password'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password">
                <?php if (isset($_SESSION['errors']['confirm_password'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['confirm_password']) ?></div>
                <?php endif; ?>
            </div>

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

            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>">
                <?php if (isset($_SESSION['errors']['username'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['username']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" name="profile_pic" accept="image/*">
                <?php if (isset($_SESSION['errors']['profile_pic'])): ?>
                    <div class="error"><?= htmlspecialchars($_SESSION['errors']['profile_pic']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
<?php
unset($_SESSION['errors'], $_SESSION['old']);
?>