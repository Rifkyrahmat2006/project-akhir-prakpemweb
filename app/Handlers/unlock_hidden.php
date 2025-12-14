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
    $current_level = User::calculateLevel($user['xp']);
    $_SESSION['level'] = $current_level;
    
    // Calculate XP progress for client-side update
    $xp_thresholds = [
        1 => ['min' => 0, 'max' => 100],
        2 => ['min' => 101, 'max' => 300],
        3 => ['min' => 301, 'max' => 600],
        4 => ['min' => 601, 'max' => 1000]
    ];
    $rank_names = [
        1 => 'Visitor',
        2 => 'Explorer',
        3 => 'Historian',
        4 => 'Royal Curator'
    ];
    $new_level = $current_level;
    $new_xp = $user['xp'];
    $current_threshold = $xp_thresholds[$new_level] ?? $xp_thresholds[1];
    $xp_progress = 0;
    if ($new_level < 4) {
        $range = $current_threshold['max'] - $current_threshold['min'];
        $progress = $new_xp - $current_threshold['min'];
        $xp_progress = min(100, max(0, ($progress / $range) * 100));
    } else {
        $xp_progress = 100;
    }
    $rank_name = $rank_names[$new_level] ?? 'Visitor';
    
    echo json_encode([
        'success' => true,
        'message' => 'Hidden artifact unlocked!',
        'xp_reward' => $xp_reward,
        'leveled_up' => $level_up,
        'new_level' => $current_level,
        'new_xp' => $user['xp'],
        'xp_progress' => $xp_progress,
        'rank_name' => $rank_name,
        'artifact' => $hidden_artifact
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unlock artifact']);
}

$conn->close();
?>
