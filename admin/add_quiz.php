<?php
/**
 * Admin - Add Quiz
 * Uses Middleware for admin authentication
 */

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Require admin access
requireAdmin();

// Pre-selected room from URL
$preselected_room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

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
            <h2 class="text-3xl text-white font-serif">Add New Quiz</h2>
            <a href="quizzes.php<?php echo $preselected_room_id ? '?room_id='.$preselected_room_id : ''; ?>" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Quizzes
            </a>
        </div>

        <form action="../app/Handlers/admin_handler.php" method="POST" class="max-w-2xl bg-darker-bg rounded-lg border border-gray-800 p-8">
            <input type="hidden" name="action" value="add_quiz">
            
            <!-- Room Selection -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Room *</label>
                <select name="room_id" required class="w-full bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none">
                    <option value="">Select a room...</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room['id']; ?>" <?php echo ($room['id'] == $preselected_room_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($room['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Question -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Question *</label>
                <textarea name="question" required rows="3" class="w-full bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none" placeholder="Enter the quiz question..."></textarea>
            </div>

            <!-- Options -->
            <div class="mb-6 space-y-4">
                <label class="block text-gray-400 text-sm mb-2">Answer Options *</label>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">A</span>
                    <input type="text" name="option_a" required class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none" placeholder="Option A">
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">B</span>
                    <input type="text" name="option_b" required class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none" placeholder="Option B">
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="bg-gold text-black w-8 h-8 flex items-center justify-center rounded font-bold">C</span>
                    <input type="text" name="option_c" required class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none" placeholder="Option C">
                </div>
            </div>

            <!-- Correct Answer -->
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Correct Answer *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="a" required class="text-gold">
                        <span class="text-white">A</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="b" class="text-gold">
                        <span class="text-white">B</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="correct_option" value="c" class="text-gold">
                        <span class="text-white">C</span>
                    </label>
                </div>
            </div>

            <!-- XP Reward -->
            <div class="mb-8">
                <label class="block text-gray-400 text-sm mb-2">XP Reward *</label>
                <input type="number" name="xp_reward" value="25" required min="1" class="w-32 bg-black border border-gray-700 text-white px-4 py-3 rounded focus:border-gold outline-none">
            </div>

            <button type="submit" class="bg-gold hover:bg-gold-hover text-black font-bold py-3 px-6 rounded transition">
                <i class="fas fa-save mr-2"></i> Save Quiz
            </button>
        </form>
    </main>
</div>

<?php $conn->close(); ?>
