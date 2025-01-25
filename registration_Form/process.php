<?php
$filename = "users.txt";

// Create file if it doesn't exist
if (!file_exists($filename)) {
    file_put_contents($filename, '');
}

// Load existing records
$records = [];
if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $record = json_decode($line, true);
        if ($record !== null) {
            $records[] = $record;
        }
    }
}

// Handle DELETE request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (isset($records[$id])) {
        unset($records[$id]);
        // Save remaining records (one per line)
        $lines = [];
        foreach ($records as $record) {
            $lines[] = json_encode($record);
        }
        file_put_contents($filename, implode(PHP_EOL, $lines));
    }
    header("Location: display.php");
    exit();
}

// Handle POST request (new registration)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRecord = [
        'firstname' => $_POST['firstname'],
        'lastname' => $_POST['lastname'],
        'address' => $_POST['address'],
        'country' => $_POST['country'],
        'gender' => $_POST['gender'],
        'skills' => implode(', ', $_POST['skills']),
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'department' => $_POST['department']
    ];

    // Append new record as a new line
    file_put_contents($filename, json_encode($newRecord, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    header("Location: display.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="table-container">
        <h2>Customer Records</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>Country</th>
                    <th>Gender</th>
                    <th>Skills</th>
                    <th>Username</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $index => $record): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($record['firstname']) ?></td>
                            <td><?= htmlspecialchars($record['lastname']) ?></td>
                            <td><?= htmlspecialchars($record['address']) ?></td>
                            <td><?= htmlspecialchars($record['country']) ?></td>
                            <td><?= htmlspecialchars($record['gender']) ?></td>
                            <td><?= htmlspecialchars($record['skills']) ?></td>
                            <td><?= htmlspecialchars($record['username']) ?></td>
                            <td><?= htmlspecialchars($record['department']) ?></td>
                            <td>
                                <a href="?delete=<?= $index ?>"><button>Delete</button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="no-records">
                        <td colspan="10">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>