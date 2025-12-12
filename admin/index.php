<?php
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

// Fetch Stats
$stats = [];
$stats['users'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'visitor'")->fetch_assoc()['count'];
$stats['artifacts'] = $conn->query("SELECT COUNT(*) as count FROM artifacts")->fetch_assoc()['count'];
$stats['collections'] = $conn->query("SELECT COUNT(*) as count FROM user_collections")->fetch_assoc()['count'];

include '../public/header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar -->
    <aside class="w-64 bg-darker-bg border-r border-gold/20 flex flex-col">
        <div class="p-6 border-b border-gold/20">
            <h1 class="text-gold font-serif text-2xl font-bold">Curator Panel</h1>
            <p class="text-gray-500 text-xs uppercase tracking-widest mt-1">Admin Access</p>
        </div>
        
        <nav class="flex-grow p-4 space-y-2">
            <a href="index.php" class="block px-4 py-3 rounded bg-gold/10 text-gold border-l-4 border-gold">
                <i class="fas fa-chart-line w-6"></i> Dashboard
            </a>
            <a href="artifacts.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-boxes w-6"></i> Manage Artifacts
            </a>
            <a href="users.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-users w-6"></i> Visitors
            </a>
        </nav>

        <div class="p-4 border-t border-gold/20">
            <a href="../public/index.php" class="block w-full text-center py-2 border border-gray-700 text-gray-400 hover:text-white hover:border-white rounded transition mb-2">
                <i class="fas fa-eye mr-2"></i> View Site
            </a>
            <a href="../public/logout.php" class="block w-full text-center py-2 bg-red-900/20 text-red-400 hover:bg-red-900/40 rounded transition">
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8 overflow-y-auto">
        <h2 class="text-3xl text-white font-serif mb-8">Dashboard Overview</h2>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Stat Card 1 -->
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-lg">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Total Visitors</p>
                        <h3 class="text-4xl text-white font-bold mt-1"><?php echo $stats['users']; ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-500">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="text-xs text-blue-400">Registered explorers</div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-lg">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Artifacts</p>
                        <h3 class="text-4xl text-gold font-bold mt-1"><?php echo $stats['artifacts']; ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-yellow-900/30 flex items-center justify-center text-gold">
                        <i class="fas fa-gem text-xl"></i>
                    </div>
                </div>
                <div class="text-xs text-yellow-400">Items in exhibition</div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-gray-900 border border-gray-800 p-6 rounded-lg">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Collections</p>
                        <h3 class="text-4xl text-green-500 font-bold mt-1"><?php echo $stats['collections']; ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-900/30 flex items-center justify-center text-green-500">
                        <i class="fas fa-hand-holding text-xl"></i>
                    </div>
                </div>
                <div class="text-xs text-green-400">Items found by users</div>
            </div>
        </div>

        <!-- Recent Activity (Optional / Static for now) -->
        <div class="border border-gold/10 rounded-lg overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 border-b border-gold/10">
                <h3 class="text-white font-bold">Admin Notices</h3>
            </div>
            <div class="p-6 bg-black/50">
                <p class="text-gray-400">Welcome to the VesperaVeloria curator panel. From here you can manage the museum collection and monitor visitor progress.</p>
                <div class="mt-4 p-4 bg-blue-900/10 border border-blue-500/20 rounded flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                    <p class="text-sm text-blue-300">Tip: Use the 'Manage Artifacts' page to add new items to rooms. Ensure you have the image URL ready.</p>
                </div>
            </div>
        </div>
    </main>
</div>
