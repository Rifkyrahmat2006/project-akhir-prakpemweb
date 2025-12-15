<?php
/**
 * Get Quiz Questions for a Room
 * Returns quiz questions for the professor dialogue quiz system
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_GET['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Room ID required']);
    exit();
}

require_once __DIR__ . '/../Config/database.php';

$room_id = intval($_GET['room_id']);

// Fetch all quiz questions for this room
$stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, correct_option, xp_reward FROM quizzes WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode([
    'success' => true,
    'questions' => $questions
]);

$stmt->close();
$conn->close();
?>
