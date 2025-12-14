<?php
/**
 * Password Hash Generator
 * This creates a password hash that will work on YOUR system
 */

$username = "fahmiadmin";
$new_password = "123";

echo "=== Password Hash Generator ===\n\n";

// Generate the hash
$hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "Username: $username\n";
echo "Password: $new_password\n";
echo "Generated Hash: $hash\n\n";

// Verify it works
if (password_verify($new_password, $hash)) {
    echo "✓ Hash verification SUCCESSFUL!\n\n";
} else {
    echo "✗ Hash verification FAILED!\n\n";
}

echo "=== SQL Query to Run ===\n";
echo "Copy and paste this into phpMyAdmin SQL tab:\n\n";
echo "UPDATE users SET password = '$hash' WHERE username = '$username';\n\n";

// Also update the database directly
require_once 'app/Config/database.php';

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $hash, $username);

if ($stmt->execute()) {
    echo "✓ DATABASE UPDATED SUCCESSFULLY!\n";
    echo "You can now login with:\n";
    echo "  Username: $username\n";
    echo "  Password: $new_password\n";
} else {
    echo "✗ Database update failed: " . $conn->error . "\n";
}

$stmt->close();
$conn->close();
?>
