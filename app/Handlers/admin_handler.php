<?php
/**
 * Admin Handler - Handles all admin CRUD operations
 * Uses Middleware for admin authentication
 */

// Load bootstrap
require_once __DIR__ . '/../bootstrap.php';

// Require admin access
requireAdmin();

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
                    // DB Path - use BASE_URL for portability
                    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                    $img = $baseUrl . '/assets/img/artifacts/' . $newFileName;
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
                    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                    $img = $baseUrl . '/assets/img/artifacts/' . $newFileName;
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
    
    // Save Room Dialogs (AJAX)
    if ($action == 'save_room_dialogs') {
        header('Content-Type: application/json');
        
        $room_id = intval($_POST['room_id']);
        $dialogs = $_POST['dialogs']; // Already JSON string from frontend
        
        // Validate JSON
        $decoded = json_decode($dialogs, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE rooms SET professor_dialogs = ? WHERE id = ?");
        $stmt->bind_param("si", $dialogs, $room_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        exit();
    }
    
    // Save Hidden Artifact (AJAX)
    if ($action == 'save_hidden_artifact') {
        header('Content-Type: application/json');
        
        $room_id = intval($_POST['room_id']);
        $name = $_POST['name'];
        $desc = $_POST['desc'];
        $image = $_POST['image'];
        $xp = intval($_POST['xp']);
        
        $stmt = $conn->prepare("UPDATE rooms SET hidden_artifact_name = ?, hidden_artifact_desc = ?, hidden_artifact_image = ?, hidden_artifact_xp = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $desc, $image, $xp, $room_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
        exit();
    }
    
    // Add Quiz
    if ($action == 'add_quiz') {
        $room_id = intval($_POST['room_id']);
        $question = $_POST['question'];
        $option_a = $_POST['option_a'];
        $option_b = $_POST['option_b'];
        $option_c = $_POST['option_c'];
        $correct = strtolower($_POST['correct_option']);
        $xp = intval($_POST['xp_reward']);
        
        $stmt = $conn->prepare("INSERT INTO quizzes (room_id, question, option_a, option_b, option_c, correct_option, xp_reward) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $room_id, $question, $option_a, $option_b, $option_c, $correct, $xp);
        
        if ($stmt->execute()) {
            header("Location: ../../admin/quizzes.php?msg=Quiz Added Successfully");
        } else {
            echo "Error: " . $conn->error;
        }
    }
    
    // Edit Quiz
    if ($action == 'edit_quiz') {
        $id = intval($_POST['id']);
        $room_id = intval($_POST['room_id']);
        $question = $_POST['question'];
        $option_a = $_POST['option_a'];
        $option_b = $_POST['option_b'];
        $option_c = $_POST['option_c'];
        $correct = strtolower($_POST['correct_option']);
        $xp = intval($_POST['xp_reward']);
        
        $stmt = $conn->prepare("UPDATE quizzes SET room_id=?, question=?, option_a=?, option_b=?, option_c=?, correct_option=?, xp_reward=? WHERE id=?");
        $stmt->bind_param("isssssii", $room_id, $question, $option_a, $option_b, $option_c, $correct, $xp, $id);
        
        if ($stmt->execute()) {
            header("Location: ../../admin/quizzes.php?msg=Quiz Updated Successfully");
        } else {
            echo "Error: " . $conn->error;
        }
    }
    
    // Delete Quiz
    if ($action == 'delete_quiz') {
        $id = intval($_GET['id']);
        $room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
        
        // First delete user quiz answers for this quiz
        $conn->query("DELETE FROM user_quizzes WHERE quiz_id = $id");
        
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $redirect = "../../admin/quizzes.php?msg=Quiz Deleted";
            if ($room_id > 0) {
                $redirect .= "&room_id=" . $room_id;
            }
            header("Location: " . $redirect);
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
