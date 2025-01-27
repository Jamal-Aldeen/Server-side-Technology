<?php
session_start();

// Redirect if not admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $email = $_POST['email'];

    // Read all users from the file
    $users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Find the user to delete
    foreach ($users as $key => $user_json) {
        $user = json_decode($user_json, true);
        if ($user['email'] === $email) {
            // Delete the user's photo file
            if (file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']); // Delete the file
            }

            // Remove the user from the array
            unset($users[$key]);
            break;
        }
    }

    // Save the updated users list back to the file
    file_put_contents('users.txt', implode(PHP_EOL, $users));

    // Set a success message
    $_SESSION['message'] = "User and their photo deleted successfully";
    header('Location: admin.php');
    exit();
}

// Get all users
$users = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Admin Panel</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message-box">
                <p><?= htmlspecialchars($_SESSION['message']) ?></p>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Room</th>
                    <th>Profile Picture</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user_json): ?>
                    <?php $user = json_decode($user_json, true); ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['room']) ?></td>
                        <td><img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="profile-img"></td>
                        <td>
                            <form action="admin.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user and their photo?');">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>