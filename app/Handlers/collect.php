<?php
/**
 * Artifact Collection Handler
 * Refactored to use Models
 */
session_start();
require_once '../Config/database.php';
require_once '../Models/Artifact.php';
require_once '../Models/Room.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$artifact_id = isset($_POST['artifact_id']) ? intval($_POST['artifact_id']) : 0;

// Validate input
if ($artifact_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid artifact']);
    exit();
}

// Collect artifact using Model
$result = Artifact::collect($conn, $user_id, $artifact_id);

if (!$result['success']) {
    echo json_encode($result);
    exit();
}

// Update session
$_SESSION['xp'] = $result['new_xp'];
if ($result['leveled_up']) {
    $_SESSION['level'] = $result['new_level'];
}

// Check if all artifacts in this room are now collected
$artifact = Artifact::findById($conn, $artifact_id);
$room_id = $artifact['room_id'];
$all_collected = Room::allArtifactsCollected($conn, $room_id, $user_id);

// Get hidden artifact info if all collected
$hidden_artifact = null;
if ($all_collected) {
    $hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
    $hidden_unlocked = Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id);
    if ($hidden_artifact) {
        $hidden_artifact['unlocked'] = $hidden_unlocked;
    }
}
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
$new_level = $result['new_level'];
$new_xp = $result['new_xp'];
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

// Get current collection count for room
$collected_count = Room::getCollectedArtifactCount($conn, $room_id, $user_id);
$total_artifacts = Room::getTotalArtifactCount($conn, $room_id);

echo json_encode([
    'success' => true,
    'new_xp' => $new_xp,
    'leveled_up' => $result['leveled_up'],
    'new_level' => $new_level,
    'xp_progress' => $xp_progress,
    'rank_name' => $rank_name,
    'all_collected' => $all_collected,
    'room_id' => $room_id,
    'hidden_artifact' => $hidden_artifact,
    'collected_count' => $collected_count,
    'total_artifacts' => $total_artifacts
]);

$conn->close();
?>

