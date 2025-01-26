<?php
session_start();

// Debugging: Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted"); // Log to server error log

    // Validate inputs
    $errors = [];
    $fields = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'room' => $_POST['room'] ?? '',
        'ext' => trim($_POST['ext'] ?? '')
    ];

    // Name validation
    if (empty($fields['name'])) {
        $errors['name'] = "Name is required";
    }

    // Email validation
    if (empty($fields['email'])) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Password validation
    if (empty($fields['password'])) {
        $errors['password'] = "Password is required";
    } elseif (strlen($fields['password']) !== 8 || !preg_match('/^[a-z0-9_]+$/', $fields['password'])) {
        $errors['password'] = "Password must be 8 characters, lowercase, numbers, or underscores only";
    }

    // Confirm password
    if ($fields['password'] !== $fields['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Room validation
    $valid_rooms = ['Application1', 'Application2', 'Cloud'];
    if (empty($fields['room']) || !in_array($fields['room'], $valid_rooms)) {
        $errors['room'] = "Invalid room selection";
    }

    // Extension validation
    if (empty($fields['ext']) || !is_numeric($fields['ext'])) {
        $errors['ext'] = "Valid extension number required";
    }

    // Profile picture validation
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

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

    // If no errors, proceed with saving data
    if (empty($errors)) {
        // Handle file upload
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $errors['profile_pic'] = "Failed to create upload directory";
            }
        }

        if (empty($errors)) {
            $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                // Prepare user data
                $user_data = [
                    'name' => htmlspecialchars($fields['name']),
                    'email' => filter_var($fields['email'], FILTER_SANITIZE_EMAIL),
                    'password' => password_hash($fields['password'], PASSWORD_DEFAULT),
                    'room' => $fields['room'],
                    'ext' => (int)$fields['ext'],
                    'profile_pic' => $destination
                ];

                // Save user data to file
                $user_json = json_encode($user_data) . PHP_EOL;
                if (file_put_contents('users.txt', $user_json, FILE_APPEND | LOCK_EX) === false) {
                    $errors['save'] = "Failed to save user data";
                } else {
                    // Clear session data and redirect
                    unset($_SESSION['errors'], $_SESSION['old']);
                    header('Location: login.php');
                    exit();
                }
            } else {
                $errors['profile_pic'] = "Failed to upload profile picture";
            }
        }
    }

    // If there are errors, store them in session
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $fields;
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
        <h2>Add User</h2>
        
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password">
            </div>

            <div class="form-group">
                <label>Room No.:</label>
                <select name="room">
                    <option value="">-- Select Room --</option>
                    <option value="Application1" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Application1' ? 'selected' : '' ?>>Application1</option>
                    <option value="Application2" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Application2' ? 'selected' : '' ?>>Application2</option>
                    <option value="Cloud" <?= isset($_SESSION['old']['room']) && $_SESSION['old']['room'] == 'Cloud' ? 'selected' : '' ?>>Cloud</option>
                </select>
            </div>

            <div class="form-group">
                <label>Ext.:</label>
                <input type="number" name="ext" value="<?= htmlspecialchars($_SESSION['old']['ext'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>