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

echo json_encode([
    'success' => true,
    'new_xp' => $result['new_xp'],
    'leveled_up' => $result['leveled_up'],
    'new_level' => $result['new_level'],
    'all_collected' => $all_collected,
    'room_id' => $room_id,
    'hidden_artifact' => $hidden_artifact
]);

$conn->close();
?>

