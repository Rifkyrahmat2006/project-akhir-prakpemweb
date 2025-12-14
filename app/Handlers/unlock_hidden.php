<?php
/**
 * Unlock Hidden Artifact
 * Called when user passes the quiz with 50%+ correct answers
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
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

require_once '../Config/database.php';
require_once '../Models/Room.php';
require_once '../Models/User.php';

$user_id = $_SESSION['user_id'];
$room_id = intval($_POST['room_id']);

// Check if already unlocked
if (Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id)) {
    echo json_encode(['success' => true, 'message' => 'Already unlocked', 'already_unlocked' => true]);
    exit();
}

// Get hidden artifact info
$hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
if (!$hidden_artifact) {
    echo json_encode(['success' => false, 'message' => 'No hidden artifact for this room']);
    exit();
}

// Unlock the hidden artifact
$unlocked = Room::unlockHiddenArtifact($conn, $room_id, $user_id);

if ($unlocked) {
    // Award XP for unlocking the hidden artifact
    $xp_reward = $hidden_artifact['xp'] ?? 100;
    $level_up = User::addXp($conn, $user_id, $xp_reward);
    
    // Update session
    $user = User::findById($conn, $user_id);
    $_SESSION['xp'] = $user['xp'];
    $_SESSION['level'] = $user['level'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Hidden artifact unlocked!',
        'xp_reward' => $xp_reward,
        'leveled_up' => $level_up,
        'new_level' => $user['level'],
        'new_xp' => $user['xp'],
        'artifact' => $hidden_artifact
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unlock artifact']);
}

$conn->close();
?>
