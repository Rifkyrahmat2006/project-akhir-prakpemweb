<?php
/**
 * Migration script for Hidden Artifact feature
 * Run this once to update the database
 */

require_once 'app/Config/database.php';

$errors = [];
$success = [];

// 1. Add hidden artifact columns to rooms table
$sql1 = "ALTER TABLE `rooms` 
    ADD COLUMN `hidden_artifact_name` VARCHAR(100) DEFAULT NULL,
    ADD COLUMN `hidden_artifact_desc` TEXT DEFAULT NULL,
    ADD COLUMN `hidden_artifact_image` VARCHAR(255) DEFAULT NULL,
    ADD COLUMN `hidden_artifact_xp` INT DEFAULT 100";

if ($conn->query($sql1) === TRUE) {
    $success[] = "Added hidden artifact columns to rooms table";
} else {
    if (strpos($conn->error, "Duplicate column") !== false) {
        $success[] = "Hidden artifact columns already exist (skipped)";
    } else {
        $errors[] = "Error adding columns: " . $conn->error;
    }
}

// 2. Create user_hidden_artifacts tracking table
$sql2 = "CREATE TABLE IF NOT EXISTS `user_hidden_artifacts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `room_id` INT NOT NULL,
    `unlocked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `user_room` (`user_id`, `room_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql2) === TRUE) {
    $success[] = "Created user_hidden_artifacts table";
} else {
    $errors[] = "Error creating table: " . $conn->error;
}

// 3. Add is_correct column to user_quizzes if not exists (to track correct answers)
$sql3 = "ALTER TABLE `user_quizzes` ADD COLUMN `is_correct` TINYINT(1) DEFAULT 0";
if ($conn->query($sql3) === TRUE) {
    $success[] = "Added is_correct column to user_quizzes";
} else {
    if (strpos($conn->error, "Duplicate column") !== false) {
        $success[] = "is_correct column already exists (skipped)";
    } else {
        $errors[] = "Error adding is_correct: " . $conn->error;
    }
}

// 4. Set default hidden artifacts for existing rooms
$rooms = [
    1 => ['name' => 'Ancient Battle Map', 'desc' => 'A secret map showing ancient battle formations.', 'xp' => 100],
    2 => ['name' => 'Master\'s Palette', 'desc' => 'The original color palette of a Renaissance master.', 'xp' => 120],
    3 => ['name' => 'Royal Seal', 'desc' => 'A hidden royal seal of authentication.', 'xp' => 150],
    4 => ['name' => 'Forbidden Manuscript', 'desc' => 'Pages from a forbidden historical text.', 'xp' => 200]
];

foreach ($rooms as $room_id => $data) {
    $stmt = $conn->prepare("UPDATE rooms SET hidden_artifact_name = ?, hidden_artifact_desc = ?, hidden_artifact_xp = ? WHERE id = ? AND hidden_artifact_name IS NULL");
    $stmt->bind_param("ssii", $data['name'], $data['desc'], $data['xp'], $room_id);
    $stmt->execute();
}
$success[] = "Set default hidden artifacts for rooms";

$conn->close();

// Output results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hidden Artifact Migration</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #1a1a1a; color: #fff; }
        .success { color: #4ade80; margin: 10px 0; }
        .error { color: #f87171; margin: 10px 0; }
        h1 { color: #c5a059; }
    </style>
</head>
<body>
    <h1>🏛️ Hidden Artifact Migration</h1>
    
    <?php foreach ($success as $msg): ?>
        <p class="success">✓ <?php echo $msg; ?></p>
    <?php endforeach; ?>
    
    <?php foreach ($errors as $msg): ?>
        <p class="error">✗ <?php echo $msg; ?></p>
    <?php endforeach; ?>
    
    <?php if (empty($errors)): ?>
        <h2 style="color: #4ade80; margin-top: 30px;">Migration Complete!</h2>
        <p>You can now configure hidden artifacts in the Room Editor.</p>
    <?php else: ?>
        <h2 style="color: #f87171; margin-top: 30px;">Migration had errors</h2>
    <?php endif; ?>
</body>
</html>
