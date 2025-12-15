<?php
/**
 * Admin - Edit Quiz
 * Uses Middleware for admin authentication
 */

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Require admin access
requireAdmin();

// Get quiz ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: quizzes.php");
    exit();
}

// Fetch quiz
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    header("Location: quizzes.php?msg=Quiz not found");
    exit();
}

// Fetch Rooms for dropdown
$rooms = $conn->query("SELECT id, name FROM rooms ORDER BY name")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../public/header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar Component -->
    <?php adminSidebar('quizzes'); ?>

    <!-- Main Content -->
    <main class="flex-grow p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl text-white font-serif">Edit Quiz</h2>
            <a href="quizzes.php" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Quizzes
            </a>
        </div>

        <form action="../app/Handlers/admin_handler.php" method="POST" class="max-w-2xl bg-darker-bg rounded-lg border border-gray-800 p-8">
            <input type="hidden" name="action" value="edit_quiz">
            <input type="hidden" name="id" value="<?php echo $quiz['id']; ?>">
            
            <!-- Room Selection -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Room *</label>
                <select name="room_id" required class="w-full bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none">
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room['id']; ?>" <?php echo $room['id'] == $quiz['room_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($room['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Question -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Question *</label>
                <textarea name="question" required rows="3" class="w-full bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none"><?php echo htmlspecialchars($quiz['question']); ?></textarea>
            </div>

            <!-- Options -->
            <div class="mb-6 space-y-4">
                <label class="block text-gray-400 text-sm mb-2">Answer Options *</label>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">A</span>
                    <input type="text" name="option_a" required value="<?php echo htmlspecialchars($quiz['option_a']); ?>" class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">B</span>
                    <input type="text" name="option_b" required value="<?php echo htmlspecialchars($quiz['option_b']); ?>" class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">C</span>
                    <input type="text" name="option_c" required value="<?php echo htmlspecialchars($quiz['option_c']); ?>" class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                </div>
            </div>

            <!-- Correct Answer -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Correct Answer *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="a" <?php echo $quiz['correct_option'] == 'a' ? 'checked' : ''; ?> required class="text-gold">
                        <span class="text-white">A</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="b" <?php echo $quiz['correct_option'] == 'b' ? 'checked' : ''; ?> class="text-gold">
                        <span class="text-white">B</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="c" <?php echo $quiz['correct_option'] == 'c' ? 'checked' : ''; ?> class="text-gold">
                        <span class="text-white">C</span>
                    </label>
                </div>
            </div>

            <!-- XP Reward -->
            <div class="mb-8">
                <label class="block text-gray-400 text-sm mb-2">XP Reward *</label>
                <input type="number" name="xp_reward" value="<?php echo $quiz['xp_reward']; ?>" required min="1" class="w-32 bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none">
            </div>

            <button type="submit" class="bg-gold hover:bg-gold-hover text-black font-bold py-3 px-6 rounded transition">
                <i class="fas fa-save mr-2"></i> Update Quiz
            </button>
        </form>
    </main>
</div>

<?php $conn->close(); ?>
