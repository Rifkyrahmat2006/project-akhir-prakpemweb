<?php
session_start();
require_once __DIR__ . '/../Config/database.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['positions']) && is_array($data['positions'])) {
        $stmt = $conn->prepare("UPDATE artifacts SET position_top = ?, position_left = ? WHERE id = ?");
        
        $success_count = 0;
        foreach ($data['positions'] as $pos) {
            $top = $pos['top'];
            $left = $pos['left'];
            $id = $pos['id'];
            
            $stmt->bind_param("ssi", $top, $left, $id);
            if ($stmt->execute()) {
                $success_count++;
            }
        }
        
        echo json_encode(['status' => 'success', 'updated' => $success_count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
