<?php
session_start();

// Use __DIR__ for reliable paths regardless of where this is called from
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/User.php';

// Load env for BASE_URL
require_once __DIR__ . '/../Config/env.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'register') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if ($password !== $confirm_password) {
            header("Location: " . BASE_URL . "/register.php?error=Passwords do not match");
            exit();
        }

        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            header("Location: " . BASE_URL . "/register.php?error=Username already taken");
            exit();
        }
        $stmt->close();

        // Create User
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, xp) VALUES (?, ?, 'visitor', 0)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "/login.php?success=1");
        } else {
            header("Location: " . BASE_URL . "/register.php?error=Registration failed");
        }
        $stmt->close();

    } elseif ($action == 'login') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, username, password, role, xp, avatar FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Calculate level from XP
                $calculated_level = User::calculateLevel($user['xp']);
                
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['level'] = $calculated_level;
                $_SESSION['xp'] = $user['xp'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['avatar'] = $user['avatar'] ?? null;

                if ($user['role'] === 'admin') {
                    header("Location: " . BASE_URL . "/admin/");
                } else {
                    header("Location: " . BASE_URL . "/lobby/");
                }
                exit();
            } else {
                header("Location: " . BASE_URL . "/login.php?error=Invalid password");
            }
        } else {
            header("Location: " . BASE_URL . "/login.php?error=User not found");
        }
        $stmt->close();
    }
}
$conn->close();
?>
