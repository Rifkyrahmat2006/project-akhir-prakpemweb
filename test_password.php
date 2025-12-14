<?php
// Test Password Hash Verification
// This script tests if the password "123" matches the hash in database

$password = "123";
$hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhCa';

echo "Testing password verification:\n";
echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n\n";

if (password_verify($password, $hash)) {
    echo "✓ PASSWORD VERIFIED! The hash is correct for password '123'\n";
} else {
    echo "✗ PASSWORD VERIFICATION FAILED! The hash does NOT match password '123'\n";
}

echo "\n--- Current User Check ---\n";
require_once 'app/Config/database.php';

$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = 'rifky123'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "User found: " . $user['username'] . "\n";
    echo "Current password hash in DB: " . $user['password'] . "\n\n";
    
    // Test if "123" works with current hash
    if (password_verify("123", $user['password'])) {
        echo "✓ Password '123' WORKS with current database hash!\n";
    } else {
        echo "✗ Password '123' DOES NOT WORK with current database hash\n";
        echo "→ You need to update the database with the new hash\n";
    }
} else {
    echo "✗ User 'rifky123' not found in database!\n";
}
?>
