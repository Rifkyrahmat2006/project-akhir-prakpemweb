<?php
/**
 * Admin - Manage Artifacts
 * Uses Middleware and room-based navigation
 */

// Load bootstrap
require_once __DIR__ . '/../app/bootstrap.php';

// Require admin access
requireAdmin();

// Fetch Rooms with artifact counts
$rooms = [];
$result = $conn->query("SELECT r.*, COUNT(a.id) as artifact_count 
                        FROM rooms r 
                        LEFT JOIN artifacts a ON r.id = a.room_id 
                        GROUP BY r.id 
                        ORDER BY r.id");
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

// Check if a room is selected
$selected_room = null;
$artifacts = [];

if (isset($_GET['room_id'])) {
    $room_id = intval($_GET['room_id']);
    
    // Fetch Room Info
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $selected_room = $stmt->get_result()->fetch_assoc();
    
    if ($selected_room) {
        // Fetch Artifacts for this room
        $stmt = $conn->prepare("SELECT * FROM artifacts WHERE room_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $artifacts[] = $row;
        }
    }
}

include '../public/header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar Component -->
    <?php adminSidebar('artifacts'); ?>

    <!-- Main Content -->
    <main class="flex-grow p-8 overflow-y-auto">
        <?php if (!$selected_room): ?>
            <!-- Room Selection View -->
            <div class="mb-8">
                <h2 class="text-3xl text-white font-serif mb-2">Manage Artifacts</h2>
                <p class="text-gray-400">Select a room to manage its artifacts</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($rooms as $room): ?>
                    <?php roomCard($room, 'artifacts.php', 'Artifacts', $room['artifact_count']); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Artifacts List for Selected Room -->
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <a href="artifacts.php" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-3xl text-white font-serif"><?php echo htmlspecialchars($selected_room['name']); ?> - Artifacts</h2>
                        <p class="text-gray-400 text-sm"><?php echo count($artifacts); ?> artifact(s) in this room</p>
                    </div>
                </div>
                <a href="add_artifact.php?room_id=<?php echo $selected_room['id']; ?>" class="bg-gold hover:bg-gold-hover text-black font-bold py-2 px-4 rounded transition">
                    <i class="fas fa-plus mr-2"></i> Add New Artifact
                </a>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="mb-6">
                    <?php alertBox($_GET['msg'], 'success'); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($artifacts)): ?>
                <div class="bg-darker-bg rounded-lg border border-gray-800 p-12 text-center">
                    <i class="fas fa-gem text-6xl text-gray-700 mb-4"></i>
                    <p class="text-gray-500 mb-4">No artifacts in this room yet.</p>
                    <a href="add_artifact.php?room_id=<?php echo $selected_room['id']; ?>" class="inline-block bg-gold hover:bg-gold-hover text-black font-bold py-2 px-4 rounded transition">
                        <i class="fas fa-plus mr-2"></i> Add First Artifact
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-darker-bg rounded-lg border border-gray-800 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-900 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Image</th>
                                <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Name</th>
                                <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Description</th>
                                <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">XP Reward</th>
                                <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <?php foreach ($artifacts as $item): ?>
                                <tr class="hover:bg-gray-800/50 transition">
                                    <td class="px-6 py-4">
                                        <img src="<?php echo $item['image_url']; ?>" alt="Artifact" class="w-12 h-12 object-cover rounded border border-gray-700">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-sm truncate max-w-xs"><?php echo htmlspecialchars($item['description']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gold font-bold">+<?php echo $item['xp_reward']; ?> XP</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="edit_artifact.php?id=<?php echo $item['id']; ?>" class="text-blue-400 hover:text-blue-300 transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../app/Handlers/admin_handler.php?action=delete_artifact&id=<?php echo $item['id']; ?>&room_id=<?php echo $selected_room['id']; ?>" 
                                           class="text-red-400 hover:text-red-300 transition"
                                           onclick="return confirm('Are you sure you want to delete this artifact?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>
