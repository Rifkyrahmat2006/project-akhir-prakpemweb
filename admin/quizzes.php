<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

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

include '../public/header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar -->
    <aside class="w-64 bg-darker-bg border-r border-gold/20 flex flex-col">
        <div class="p-6 border-b border-gold/20">
            <h1 class="text-gold font-serif text-2xl font-bold">Curator Panel</h1>
        </div>
        
        <nav class="flex-grow p-4 space-y-2">
            <a href="index.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-chart-line w-6"></i> Dashboard
            </a>
            <a href="artifacts.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-boxes w-6"></i> Manage Artifacts
            </a>
            <a href="quizzes.php" class="block px-4 py-3 rounded bg-gold/10 text-gold border-l-4 border-gold">
                <i class="fas fa-question-circle w-6"></i> Manage Quizzes
            </a>
            <a href="users.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-users w-6"></i> Visitors
            </a>
            <a href="room_editor.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-map w-6"></i> Room Editor
            </a>
        </nav>

        <div class="p-4 border-t border-gold/20">
            <a href="../public/index.php" class="block w-full text-center py-2 border border-gray-700 text-gray-400 hover:text-white hover:border-white rounded transition mb-2">
                <i class="fas fa-eye mr-2"></i> View Site
            </a>
        </div>
    </aside>

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
                <h2 class="text-3xl text-white font-serif mb-2">Room Quizzes</h2>
                <p class="text-gray-400">Select a room to manage its quizzes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($rooms as $room): ?>
                    <a href="?room_id=<?php echo $room['id']; ?>" class="bg-darker-bg border border-gray-800 rounded-lg p-6 hover:border-gold/50 hover:bg-gray-800/50 transition group">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-white font-serif text-lg group-hover:text-gold transition"><?php echo htmlspecialchars($room['name']); ?></h3>
                            <span class="bg-purple-900/30 border border-purple-500/30 text-purple-300 px-3 py-1 rounded-full text-sm">
                                <?php echo $room['quiz_count']; ?> Quiz
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($room['description']); ?></p>
                        <div class="mt-4 text-gold text-sm opacity-0 group-hover:opacity-100 transition">
                            <i class="fas fa-arrow-right mr-2"></i> Manage Quizzes
                        </div>
                    </a>
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
