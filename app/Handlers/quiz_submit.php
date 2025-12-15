<?php
/**
 * Quiz Submit Controller
 * Pure controller - delegates business logic to Models
 */

// Load bootstrap
require_once __DIR__ . '/../bootstrap.php';

// Start output buffering to catch any unwanted output/warnings
ob_start();

try {
    header('Content-Type: application/json');

    // Check authentication using middleware
    if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Unauthorized');
    }

    $user_id = userId();
    $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
    $answer = isset($_POST['answer']) ? strtolower(trim($_POST['answer'])) : '';

    // Validate input
    if ($quiz_id <= 0 || !in_array($answer, ['a', 'b', 'c'])) {
        throw new Exception('Invalid input');
    }

    // Submit quiz answer using Model (handles XP, validation, etc.)
    $result = Quiz::submit($conn, $user_id, $quiz_id, $answer);

    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    // Update session
    if ($result['new_xp']) {
        $_SESSION['xp'] = $result['new_xp'];
    }
    $_SESSION['level'] = $result['new_level']; // Always update level

    // Check for hidden artifact unlock
    $hidden_artifact_unlocked = false;
    $hidden_artifact_data = null;

    // Get room_id from quiz using Model
    $quiz = Quiz::findById($conn, $quiz_id);
    if ($quiz) {
        $room_id = $quiz['room_id'];
        
        // Check if already unlocked using Model
        if (!Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id)) {
            // Check unlock conditions (50% correct) using Model
            if (Quiz::checkHiddenArtifactUnlock($conn, $room_id, $user_id)) {
                // Unlock hidden artifact using Model
                Room::unlockHiddenArtifact($conn, $room_id, $user_id);
                
                // Get hidden artifact data using Model
                $hidden = Room::getHiddenArtifact($conn, $room_id);
                if ($hidden) {
                    $hidden_artifact_unlocked = true;
                    $hidden_artifact_data = $hidden;
                    
                    // Add bonus XP using Model
                    $bonusXp = User::addXp($conn, $user_id, $hidden['xp']);
                    $_SESSION['xp'] = $bonusXp['new_xp'];
                    $_SESSION['level'] = $bonusXp['new_level'];
                }
            }
        }
    }

    // Get XP progress data using Model (if needed for response)
    $progressData = User::getXpProgressData($_SESSION['xp'], $_SESSION['level']);

    $response = [
        'success' => true,
        'correct' => $result['is_correct'],
        'correct_answer' => $quiz['correct_option'],
        'xp_reward' => $result['xp_earned'],
        'leveled_up' => $result['leveled_up'],
        'new_level' => $result['new_level'],
        'new_xp' => $_SESSION['xp'],
        'xp_progress' => $progressData['xp_progress'],
        'rank_name' => $progressData['rank_name'],
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
