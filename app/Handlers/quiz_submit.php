<?php
/**
 * Quiz Submit Handler
 * Refactored to use Models
 */
session_start();
require_once '../Config/database.php';
require_once '../Models/User.php';
require_once '../Models/Quiz.php';
require_once '../Models/Room.php';

// Start output buffering to catch any unwanted output/warnings
ob_start();

try {
    header('Content-Type: application/json');

    // Check authentication
    if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Unauthorized');
    }

    $user_id = $_SESSION['user_id'];
    $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
    $answer = isset($_POST['answer']) ? strtolower(trim($_POST['answer'])) : '';

    // Validate input
    if ($quiz_id <= 0 || !in_array($answer, ['a', 'b', 'c'])) {
        throw new Exception('Invalid input');
    }

    // Submit quiz answer using Model
    $result = Quiz::submit($conn, $user_id, $quiz_id, $answer);

    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    // Update session - ALWAYS sync level to prevent desync
    if ($result['new_xp']) {
        $_SESSION['xp'] = $result['new_xp'];
    }
    $_SESSION['level'] = $result['new_level']; // Always update level

    // Check for hidden artifact unlock
    $hidden_artifact_unlocked = false;
    $hidden_artifact_data = null;

    // Get room_id from quiz
    $quiz = Quiz::findById($conn, $quiz_id);
    if ($quiz) {
        $room_id = $quiz['room_id'];
        
        // Check if already unlocked
        if (!Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id)) {
            // Check unlock conditions (50% correct)
            if (Quiz::checkHiddenArtifactUnlock($conn, $room_id, $user_id)) {
                // Unlock hidden artifact
                Room::unlockHiddenArtifact($conn, $room_id, $user_id);
                
                // Get hidden artifact data
                $hidden = Room::getHiddenArtifact($conn, $room_id);
                if ($hidden) {
                    $hidden_artifact_unlocked = true;
                    $hidden_artifact_data = $hidden;
                    
                    // Add bonus XP
                    User::addXp($conn, $user_id, $hidden['xp']);
                }
            }
        }
    }

    $response = [
        'success' => true,
        'correct' => $result['is_correct'],
        'correct_answer' => $quiz['correct_option'], // Add this for incorrect feedback
        'xp_reward' => $result['xp_earned'],
        'leveled_up' => $result['leveled_up'],
        'new_level' => $result['new_level'],
        'hidden_artifact_unlocked' => $hidden_artifact_unlocked,
        'hidden_artifact' => $hidden_artifact_data
    ];

    // Clear buffer and output JSON
    ob_clean();
    echo json_encode($response);

} catch (Exception $e) {
    // Clear buffer and output JSON error
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
exit();
?>
