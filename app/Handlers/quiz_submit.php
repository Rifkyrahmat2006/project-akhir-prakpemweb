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

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$answer = isset($_POST['answer']) ? strtolower(trim($_POST['answer'])) : '';

// Validate input
if ($quiz_id <= 0 || !in_array($answer, ['a', 'b', 'c'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Submit quiz answer using Model
$result = Quiz::submit($conn, $user_id, $quiz_id, $answer);

if (!$result['success']) {
    echo json_encode($result);
    exit();
}

// Update session
if ($result['new_xp']) {
    $_SESSION['xp'] = $result['new_xp'];
}
if ($result['leveled_up']) {
    $_SESSION['level'] = $result['new_level'];
}

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

echo json_encode([
    'success' => true,
    'correct' => $result['is_correct'],
    'xp_reward' => $result['xp_earned'],
    'leveled_up' => $result['leveled_up'],
    'new_level' => $result['new_level'],
    'hidden_artifact_unlocked' => $hidden_artifact_unlocked,
    'hidden_artifact' => $hidden_artifact_data
]);

$conn->close();
?>
