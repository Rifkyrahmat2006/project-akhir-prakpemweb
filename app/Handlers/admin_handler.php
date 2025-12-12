<?php
session_start();
require_once '../Config/database.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action == 'add_artifact') {
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $room_id = $_POST['room_id'];
        $xp = $_POST['xp_reward'];
        $img = $_POST['image_url'];

        $stmt = $conn->prepare("INSERT INTO artifacts (room_id, name, description, image_url, xp_reward) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $room_id, $name, $desc, $img, $xp);
        
        if ($stmt->execute()) {
            header("Location: ../../admin/artifacts.php?msg=Artifact Added Successfully");
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();

    } elseif ($action == 'delete_artifact') {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM artifacts WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: ../../admin/artifacts.php?msg=Artifact Deleted");
        } else {
             header("Location: ../../admin/artifacts.php?err=Delete Failed");
        }
        $stmt->close();
    
    } elseif ($action == 'edit_artifact') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $room_id = $_POST['room_id'];
        $xp = $_POST['xp_reward'];
        $img = $_POST['image_url'];

        $stmt = $conn->prepare("UPDATE artifacts SET room_id=?, name=?, description=?, image_url=?, xp_reward=? WHERE id=?");
        $stmt->bind_param("isssii", $room_id, $name, $desc, $img, $xp, $id);

        if ($stmt->execute()) {
            header("Location: ../../admin/artifacts.php?msg=Artifact Updated");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
