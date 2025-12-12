<?php
session_start();
require_once '../Config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$answer = isset($_POST['answer']) ? strtolower(trim($_POST['answer'])) : '';

if ($quiz_id <= 0 || !in_array($answer, ['a', 'b', 'c'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// 1. Check if already answered
$check = $conn->prepare("SELECT id FROM user_quizzes WHERE user_id = ? AND quiz_id = ?");
$check->bind_param("ii", $user_id, $quiz_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already answered']);
    exit();
}
$check->close();

// 2. Get Quiz Data
$quiz_query = $conn->prepare("SELECT correct_option, xp_reward FROM quizzes WHERE id = ?");
$quiz_query->bind_param("i", $quiz_id);
$quiz_query->execute();
$quiz_res = $quiz_query->get_result();
if ($quiz_res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Quiz not found']);
    exit();
}
$quiz = $quiz_res->fetch_assoc();
$quiz_query->close();

// 3. Record the answer (even if wrong, to prevent re-answering)
$insert = $conn->prepare("INSERT INTO user_quizzes (user_id, quiz_id) VALUES (?, ?)");
$insert->bind_param("ii", $user_id, $quiz_id);
$insert->execute();
$insert->close();

// 4. Check if correct
$is_correct = ($answer === strtolower($quiz['correct_option']));
$xp_reward = $quiz['xp_reward'];

$leveled_up = false;
$new_level = $_SESSION['level'];

if ($is_correct) {
    // 5. Update User XP
    $update_user = $conn->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
    $update_user->bind_param("ii", $xp_reward, $user_id);
    $update_user->execute();
    $update_user->close();

    // 6. Check Level Up
    $u_query = $conn->prepare("SELECT xp, level FROM users WHERE id = ?");
    $u_query->bind_param("i", $user_id);
    $u_query->execute();
    $user_data = $u_query->get_result()->fetch_assoc();
    $u_query->close();
    
    $current_xp = $user_data['xp'];
    $current_level = $user_data['level'];
    $new_level = 1;

    // Level thresholds from technical_doc.md
    if ($current_xp > 600) {
        $new_level = 4;
    } elseif ($current_xp > 300) {
        $new_level = 3;
    } elseif ($current_xp > 100) {
        $new_level = 2;
    }

    if ($new_level > $current_level) {
        $upd_level = $conn->prepare("UPDATE users SET level = ? WHERE id = ?");
        $upd_level->bind_param("ii", $new_level, $user_id);
        $upd_level->execute();
        $upd_level->close();
        $_SESSION['level'] = $new_level;
        $leveled_up = true;
    }
    $_SESSION['xp'] = $current_xp; // Update Session XP
}

echo json_encode([
    'success' => true,
    'correct' => $is_correct,
    'xp_reward' => $is_correct ? $xp_reward : 0,
    'leveled_up' => $leveled_up,
    'new_level' => $new_level
]);

$conn->close();
?>
