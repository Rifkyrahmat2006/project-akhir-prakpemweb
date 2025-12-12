<?php
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

// Fetch Users
$sql = "SELECT id, username, level, xp, created_at, 
        (SELECT COUNT(*) FROM user_collections WHERE user_collections.user_id = users.id) as collected_count
        FROM users 
        WHERE role = 'visitor' 
        ORDER BY xp DESC";
$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
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
            <a href="users.php" class="block px-4 py-3 rounded bg-gold/10 text-gold border-l-4 border-gold">
                <i class="fas fa-users w-6"></i> Visitors
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
            <h2 class="text-3xl text-white font-serif">Visitor Records</h2>
            <div class="text-gray-400 text-sm">
                Total Visitors: <span class="text-white font-bold"><?php echo count($users); ?></span>
            </div>
        </div>

        <div class="bg-darker-bg rounded-lg border border-gray-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-900 border-b border-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Username</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider">Rank</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-center">Level</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-right">XP</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-center">Collection</th>
                        <th class="px-6 py-4 text-gray-400 font-normal uppercase text-xs tracking-wider text-right">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">No visitors found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="text-gray-600 text-xs">ID: #<?php echo $user['id']; ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $badges = [
                                            1 => ['Visitor', 'bg-gray-700 text-gray-300'],
                                            2 => ['Explorer', 'bg-blue-900 text-blue-300'],
                                            3 => ['Historian', 'bg-purple-900 text-purple-300'],
                                            4 => ['Royal Curator', 'bg-gold/20 text-gold']
                                        ];
                                        $badge = $badges[$user['level']] ?? $badges[1];
                                    ?>
                                    <span class="px-2 py-1 rounded text-xs font-bold <?php echo $badge[1]; ?>">
                                        <?php echo $badge[0]; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-white font-bold text-lg"><?php echo $user['level']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-gold font-mono"><?php echo number_format($user['xp']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-gray-300"><?php echo $user['collected_count']; ?></span>
                                    <span class="text-gray-600 text-xs">items</span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-500 text-xs">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
