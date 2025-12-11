<?php
session_start();
require_once '../Config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$artifact_id = isset($_POST['artifact_id']) ? intval($_POST['artifact_id']) : 0;

if ($artifact_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid artifact']);
    exit();
}

// 1. Check if already collected
$check = $conn->prepare("SELECT id FROM user_collections WHERE user_id = ? AND artifact_id = ?");
$check->bind_param("ii", $user_id, $artifact_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already collected']);
    exit();
}
$check->close();

// 2. Get Artifact XP
$art_query = $conn->prepare("SELECT xp_reward FROM artifacts WHERE id = ?");
$art_query->bind_param("i", $artifact_id);
$art_query->execute();
$art_res = $art_query->get_result();
if ($art_res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Artifact not found']);
    exit();
}
$xp_reward = $art_res->fetch_assoc()['xp_reward'];
$art_query->close();

// 3. Insert Collection
$insert = $conn->prepare("INSERT INTO user_collections (user_id, artifact_id) VALUES (?, ?)");
$insert->bind_param("ii", $user_id, $artifact_id);

if ($insert->execute()) {
    // 4. Update User XP
    $update_user = $conn->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
    $update_user->bind_param("ii", $xp_reward, $user_id);
    $update_user->execute();

    // 5. Check Level Up
    // Fetch new XP
    $u_query = $conn->prepare("SELECT xp, level FROM users WHERE id = ?");
    $u_query->bind_param("i", $user_id);
    $u_query->execute();
    $user_data = $u_query->get_result()->fetch_assoc();
    
    $current_xp = $user_data['xp'];
    $current_avg_level = $user_data['level'];
    $new_level = 1;

    // Logic from Doc:
    // Level 1: 0-100
    // Level 2: 101-300
    // Level 3: 301-600
    // Level 4: > 600
    if ($current_xp > 600) {
        $new_level = 4;
    } elseif ($current_xp > 300) {
        $new_level = 3;
    } elseif ($current_xp > 100) {
        $new_level = 2;
    }

    $leveled_up = false;
    if ($new_level > $current_avg_level) {
        $upd_level = $conn->prepare("UPDATE users SET level = ? WHERE id = ?");
        $upd_level->bind_param("ii", $new_level, $user_id);
        $upd_level->execute();
        $_SESSION['level'] = $new_level; // Update Session
        $leveled_up = true;
    }

    echo json_encode([
        'success' => true,
        'new_xp' => $current_xp,
        'leveled_up' => $leveled_up,
        'new_level' => $new_level
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?>
