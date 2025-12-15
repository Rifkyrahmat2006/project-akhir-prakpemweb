<?php
session_start();
require_once __DIR__ . '/../Config/env.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/User.php';

$baseUrl = defined('BASE_URL') ? BASE_URL : '';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $baseUrl . "/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["avatar"])) {
    $target_dir = __DIR__ . "/../../public/uploads/avatars/";
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $new_filename = "avatar_" . $_SESSION['user_id'] . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error'] = "File is not an image.";
        header("Location: " . $baseUrl . "/settings.php");
        exit();
    }

    // Check file size (max 2MB)
    if ($_FILES["avatar"]["size"] > 2000000) {
        $_SESSION['error'] = "Sorry, your file is too large (Max 2MB).";
        header("Location: " . $baseUrl . "/settings.php");
        exit();
    }

    // Allow certain file formats
    if ($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif") {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        header("Location: " . $baseUrl . "/settings.php");
        exit();
    }

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        $avatar_path = $baseUrl . "/uploads/avatars/" . $new_filename;
        
        // Update User Model
        if (User::updateAvatar($conn, $_SESSION['user_id'], $avatar_path)) {
            $_SESSION['avatar'] = $avatar_path; // Update session
            $_SESSION['success'] = "Avatar updated successfully.";
        } else {
            $_SESSION['error'] = "Database update failed.";
        }
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
    }
}

header("Location: " . $baseUrl . "/settings.php");
exit();
?>
