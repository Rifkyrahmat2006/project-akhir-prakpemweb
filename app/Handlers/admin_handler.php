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
        $img = $_POST['image_url']; // Default fallback

        // Handle File Upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = $_FILES['image_file']['name'];
            $fileSize = $_FILES['image_file']['size'];
            $fileType = $_FILES['image_file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Generate unique name
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                
                // Define paths
                // Handler is in app/Handlers/, need to go up to public/assets/...
                // Root is ../../
                $uploadFileDir = '../../public/assets/img/artifacts/';
                $dest_path = $uploadFileDir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    // DB Path (relative from web root or absolute logic used in project)
                    // Currently project uses /project-akhir/public/... or relative.
                    // room.php includes are deep. Let's use root-relative path for safety if app is in fixed folder
                    // Or relative to public logic.
                    // Let's use standard /project-akhir/public/assets/... for consistency with room images
                    $img = '/project-akhir/public/assets/img/artifacts/' . $newFileName;
                }
            }
        }

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

        // Handle File Upload (Edit)
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = $_FILES['image_file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = '../../public/assets/img/artifacts/';
                $dest_path = $uploadFileDir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $img = '/project-akhir/public/assets/img/artifacts/' . $newFileName;
                }
            }
        }

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
