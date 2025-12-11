<?php
require_once 'app/Config/database.php';

$username = 'admin';
$password = 'password123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if exists
$check = $conn->query("SELECT id FROM users WHERE username = '$username'");
if ($check->num_rows > 0) {
    die("User '$username' already exists.");
}

$stmt = $conn->prepare("INSERT INTO users (username, password, role, level, xp) VALUES (?, ?, 'admin', 5, 1000)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "User '$username' created successfully.<br>";
    echo "Password: $password<br>";
    echo "Try logging in now.";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
