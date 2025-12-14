<?php
// Fix Database Migration (CLI Version)

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_museum');

mysqli_report(MYSQLI_REPORT_OFF); // Disable exception throwing for cleaner handling here

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Connected successfully.\n";

// Check if avatar column exists
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");
if ($check->num_rows == 0) {
    echo "Column 'avatar' NOT found. Adding it now...\n";
    
    $sql = "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "SUCCESS: Column 'avatar' added to 'users' table.\n";
    } else {
        echo "ERROR: " . $conn->error . "\n";
    }
} else {
    echo "Column 'avatar' ALREADY EXISTS.\n";
}

// Ensure upload directory exists
$target_dir = __DIR__ . "/uploads/avatars/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
    echo "Created directory: $target_dir\n";
} else {
    echo "Directory exists: $target_dir\n";
}

echo "Done.\n";
?>
