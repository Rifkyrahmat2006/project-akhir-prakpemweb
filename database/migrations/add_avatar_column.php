<?php
require_once __DIR__ . '/../../app/Config/database.php';

// Add avatar column if it doesn't exist
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'avatar' added successfully to 'users' table.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'avatar' already exists.";
}

// Create uploads directory if not exists
$target_dir = __DIR__ . "/../../public/uploads/avatars/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
    echo "\nCreated avatars directory: " . $target_dir;
}
?>
