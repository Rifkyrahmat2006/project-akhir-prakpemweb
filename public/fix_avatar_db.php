<?php
// Fix Database Migration
// Run this file to add the missing avatar column

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_museum');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully.<br>";

// Check if avatar column exists
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");
if ($check->num_rows == 0) {
    echo "Column 'avatar' NOT found. Adding it now...<br>";
    
    $sql = "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "<b style='color:green'>SUCCESS:</b> Column 'avatar' added to 'users' table.<br>";
    } else {
        echo "<b style='color:red'>ERROR:</b> " . $conn->error . "<br>";
    }
} else {
    echo "Column 'avatar' ALREADY EXISTS.<br>";
}

// Ensure upload directory exists
$target_dir = __DIR__ . "/uploads/avatars/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
    echo "Created directory: $target_dir<br>";
} else {
    echo "Directory exists: $target_dir<br>";
}

echo "Done.";
?>
