<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

// Fetch Artifacts with Room Name
$sql = "SELECT artifacts.*, rooms.name as room_name 
        FROM artifacts 
        JOIN rooms ON artifacts.room_id = rooms.id 
        ORDER BY artifacts.id DESC";
$result = $conn->query($sql);
$artifacts = [];
while ($row = $result->fetch_assoc()) {
    $artifacts[] = $row;
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
            <a href="artifacts.php" class="block px-4 py-3 rounded bg-gold/10 text-gold border-l-4 border-gold">
                <i class="fas fa-boxes w-6"></i> Manage Artifacts
            </a>
            <a href="quizzes.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
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
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl text-white font-serif">Museum Artifacts</h2>
            <a href="add_artifact.php" class="bg-gold hover:bg-gold-hover text-black font-bold py-2 px-4 rounded transition">
                <i class="fas fa-plus mr-2"></i> Add New Artifact
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-900/20 border border-green-500/50 text-green-300 px-4 py-2 mb-6 rounded">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-darker-bg rounded-lg border border-gray-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-900 border-b border-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Image</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Name</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Room</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">XP Reward</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    <?php if (empty($artifacts)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No artifacts found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($artifacts as $item): ?>
                            <tr class="hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4">
                                    <img src="<?php echo $item['image_url']; ?>" alt="Artifact" class="w-12 h-12 object-cover rounded border border-gray-700">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="text-gray-600 text-xs truncate w-48"><?php echo htmlspecialchars($item['description']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-800 rounded text-xs text-gray-300">
                                        <?php echo htmlspecialchars($item['room_name']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gold font-bold">+<?php echo $item['xp_reward']; ?> XP</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="edit_artifact.php?id=<?php echo $item['id']; ?>" class="text-blue-400 hover:text-blue-300 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../app/Handlers/admin_handler.php?action=delete_artifact&id=<?php echo $item['id']; ?>" 
                                       class="text-red-400 hover:text-red-300 transition"
                                       onclick="return confirm('Are you sure you want to delete this artifact?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
