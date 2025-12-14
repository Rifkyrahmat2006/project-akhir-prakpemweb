<?php
/**
 * Artifact Collection Controller
 * Pure controller - delegates business logic to Models
 */

// Load bootstrap
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = userId();
$artifact_id = isset($_POST['artifact_id']) ? intval($_POST['artifact_id']) : 0;

// Validate input
if ($artifact_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid artifact']);
    exit();
}

// Collect artifact using Model (handles XP, level up, etc.)
$result = Artifact::collect($conn, $user_id, $artifact_id);

if (!$result['success']) {
    echo json_encode($result);
    exit();
}

// Update session with new XP/Level
$_SESSION['xp'] = $result['new_xp'];
if ($result['leveled_up']) {
    $_SESSION['level'] = $result['new_level'];
}

// Get artifact and room info using Models
$artifact = Artifact::findById($conn, $artifact_id);
$room_id = $artifact['room_id'];

// Check room completion status using Models
$all_collected = Room::allArtifactsCollected($conn, $room_id, $user_id);
$collected_count = Room::getCollectedArtifactCount($conn, $room_id, $user_id);
$total_artifacts = Room::getTotalArtifactCount($conn, $room_id);

// Get hidden artifact info if all collected
$hidden_artifact = null;
if ($all_collected) {
    $hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
    if ($hidden_artifact) {
        $hidden_artifact['unlocked'] = Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id);
    }
}

// Response - XP progress data now comes from Model via $result
echo json_encode([
    'success' => true,
    'new_xp' => $result['new_xp'],
    'leveled_up' => $result['leveled_up'],
    'new_level' => $result['new_level'],
    'xp_progress' => $result['xp_progress'],
    'rank_name' => $result['rank_name'],
    'all_collected' => $all_collected,
    'room_id' => $room_id,
    'hidden_artifact' => $hidden_artifact,
    'collected_count' => $collected_count,
    'total_artifacts' => $total_artifacts
]);

$conn->close();
?>
