<?php
/**
 * Admin - Manage Quizzes
 * Uses Middleware for admin authentication
 */

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Require admin access
requireAdmin();

// Fetch all rooms with quiz count
$rooms_result = $conn->query("
    SELECT rooms.*, COUNT(quizzes.id) as quiz_count 
    FROM rooms 
    LEFT JOIN quizzes ON rooms.id = quizzes.room_id 
    GROUP BY rooms.id 
    ORDER BY rooms.id
");
$rooms = [];
while ($row = $rooms_result->fetch_assoc()) {
    $rooms[] = $row;
}

// Check if specific room is selected
$selected_room = null;
$quizzes = [];
if (isset($_GET['room_id'])) {
    $room_id = intval($_GET['room_id']);
    
    // Get room info
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $selected_room = $stmt->get_result()->fetch_assoc();
    
    // Get quizzes for this room
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE room_id = ? ORDER BY id");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}

include __DIR__ . '/../public/header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar Component -->
    <?php adminSidebar('quizzes'); ?>

    <!-- Main Content -->
    <main class="flex-grow p-8 overflow-y-auto">
        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-900/20 border border-green-500/50 text-green-300 px-4 py-2 mb-6 rounded">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if (!$selected_room): ?>
            <!-- Room Selection View -->
            <div class="mb-8">
                <h2 class="text-3xl text-white font-serif mb-2">Manage Quizzes</h2>
                <p class="text-gray-400">Select a room to manage its quizzes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($rooms as $room): ?>
                    <?php roomCard($room, 'quizzes.php', 'Quizzes', $room['quiz_count']); ?>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Quiz List for Selected Room -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <a href="quizzes.php" class="text-gray-400 hover:text-white text-sm mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Rooms
                    </a>
                    <h2 class="text-3xl text-white font-serif"><?php echo htmlspecialchars($selected_room['name']); ?> Quizzes</h2>
                </div>
                <a href="add_quiz.php?room_id=<?php echo $selected_room['id']; ?>" class="bg-gold hover:bg-gold-hover text-black font-bold py-2 px-4 rounded transition">
                    <i class="fas fa-plus mr-2"></i> Add Quiz
                </a>
            </div>

            <div class="bg-darker-bg rounded-lg border border-gray-800 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">#</th>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Question</th>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Options</th>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Answer</th>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">XP</th>
                            <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                    No quizzes in this room yet. 
                                    <a href="add_quiz.php?room_id=<?php echo $selected_room['id']; ?>" class="text-gold hover:underline">Add one now</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $num = 1; foreach ($quizzes as $item): ?>
                                <tr class="hover:bg-gray-800/50 transition">
                                    <td class="px-6 py-4 text-gray-500"><?php echo $num++; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium"><?php echo htmlspecialchars($item['question']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-xs space-y-1">
                                            <div><span class="text-gold">A:</span> <?php echo htmlspecialchars(substr($item['option_a'], 0, 25)); ?><?php echo strlen($item['option_a']) > 25 ? '...' : ''; ?></div>
                                            <div><span class="text-gold">B:</span> <?php echo htmlspecialchars(substr($item['option_b'], 0, 25)); ?><?php echo strlen($item['option_b']) > 25 ? '...' : ''; ?></div>
                                            <div><span class="text-gold">C:</span> <?php echo htmlspecialchars(substr($item['option_c'], 0, 25)); ?><?php echo strlen($item['option_c']) > 25 ? '...' : ''; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-green-900/30 border border-green-500/30 rounded text-green-400 font-bold">
                                            <?php echo strtoupper($item['correct_option']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gold font-bold"><?php echo $item['xp_reward']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="edit_quiz.php?id=<?php echo $item['id']; ?>" class="text-blue-400 hover:text-blue-300 mr-4">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="../app/Handlers/admin_handler.php?action=delete_quiz&id=<?php echo $item['id']; ?>&room_id=<?php echo $selected_room['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this quiz?')" 
                                           class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php $conn->close(); ?>
