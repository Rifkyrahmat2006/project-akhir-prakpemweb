<?php
/**
 * Unlock Hidden Artifact Controller
 * Pure controller - delegates business logic to Models
 */

// Load bootstrap
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_POST['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Room ID required']);
    exit();
}

$user_id = userId();
$room_id = intval($_POST['room_id']);

// Check if already unlocked using Model
if (Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id)) {
    echo json_encode(['success' => true, 'message' => 'Already unlocked', 'already_unlocked' => true]);
    exit();
}

// Get hidden artifact info using Model
$hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
if (!$hidden_artifact) {
    echo json_encode(['success' => false, 'message' => 'No hidden artifact for this room']);
    exit();
}

// Unlock the hidden artifact using Model
$unlocked = Room::unlockHiddenArtifact($conn, $room_id, $user_id);

if ($unlocked) {
    // Award XP using Model (returns complete progress data)
    $xp_reward = $hidden_artifact['xp'] ?? 100;
    $xpResult = User::addXp($conn, $user_id, $xp_reward);
    
    // Update session
    $_SESSION['xp'] = $xpResult['new_xp'];
    $_SESSION['level'] = $xpResult['new_level'];
    
    // Response - all progress data comes from Model
    echo json_encode([
        'success' => true,
        'message' => 'Hidden artifact unlocked!',
        'xp_reward' => $xp_reward,
        'leveled_up' => $xpResult['leveled_up'],
        'new_level' => $xpResult['new_level'],
        'new_xp' => $xpResult['new_xp'],
        'xp_progress' => $xpResult['xp_progress'],
        'rank_name' => $xpResult['rank_name'],
        'artifact' => $hidden_artifact
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unlock artifact']);
}

$conn->close();
?>
