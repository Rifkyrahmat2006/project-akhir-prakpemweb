<?php
// Mock session data for frontend dev if not set
if (!isset($_SESSION)) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Visitor';
$level = $_SESSION['level'] ?? 1;

// XP thresholds for each level
$xp_thresholds = [
    1 => ['min' => 0, 'max' => 100],
    2 => ['min' => 101, 'max' => 300],
    3 => ['min' => 301, 'max' => 600],
    4 => ['min' => 601, 'max' => 1000]
];

// Rank names
$rank_names = [
    1 => 'Visitor',
    2 => 'Explorer',
    3 => 'Historian',
    4 => 'Royal Curator'
];

$current_xp = $_SESSION['xp'] ?? 0;
$current_threshold = $xp_thresholds[$level] ?? $xp_thresholds[1];
$xp_progress = 0;
if ($level < 4) {
    $range = $current_threshold['max'] - $current_threshold['min'];
    $progress = $current_xp - $current_threshold['min'];
    $xp_progress = min(100, max(0, ($progress / $range) * 100));
} else {
    $xp_progress = 100; // Max level
}
$rank_name = $rank_names[$level] ?? 'Visitor';
?>

<nav class="bg-dark-bg border-b border-gray-800 sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Brand -->
            <a href="index.php" class="flex items-center space-x-2">
                <i class="fas fa-landmark text-gold text-2xl"></i>
                <span class="font-serif text-xl font-bold text-white tracking-wider">VESPERA<span class="text-gold">VELORIA</span></span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-6">
                <?php if ($isLoggedIn): ?>
                    <a href="lobby/" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Lobby</a>
                    <a href="lobby/my_collection.php" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Collection</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/" class="text-red-400 hover:text-red-300 transition duration-300 font-sans uppercase text-sm tracking-wide font-bold">Admin Panel</a>
                    <?php endif; ?>
                    
                    <!-- User Info with XP Bar -->
                    <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-700">
                        <div class="text-right">
                            <div class="flex items-center gap-2">
                                <span class="text-gold font-serif text-sm"><?php echo htmlspecialchars($username); ?></span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 border border-gold/30 text-gold"><?php echo $rank_name; ?></span>
                            </div>
                            <!-- XP Progress Bar -->
                            <div class="w-32 mt-1">
                                <div class="xp-bar-container h-1.5 rounded-full bg-gray-800">
                                    <div class="xp-bar-fill h-full rounded-full" style="width: <?php echo $xp_progress; ?>%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-0.5">
                                    <span><?php echo number_format($current_xp); ?> XP</span>
                                    <span>LV.<?php echo $level; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gold/20 to-gold/5 border-2 border-gold flex items-center justify-center text-gold">
                            <i class="fas fa-crown text-sm"></i>
                        </div>
                    </div>
                    
                    <a href="logout.php" class="ml-2 text-gray-400 hover:text-red-400 text-sm" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Login</a>
                    <a href="register.php" class="btn-museum text-xs">Register</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button data-menu-toggle class="text-gray-300 hover:text-gold focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu (Hidden by default) -->
<div class="mobile-menu fixed inset-0 z-40 bg-black/95 md:hidden">
    <div class="flex flex-col h-full p-6">
        <div class="flex justify-between items-center mb-8">
            <span class="font-serif text-xl font-bold text-gold">Menu</span>
            <button data-menu-toggle class="text-white text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <?php if ($isLoggedIn): ?>
            <!-- User Info Mobile -->
            <div class="mb-8 p-4 bg-gray-900 rounded-lg border border-gold/20">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-gold/20 to-gold/5 border-2 border-gold flex items-center justify-center text-gold">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <div class="text-gold font-serif"><?php echo htmlspecialchars($username); ?></div>
                        <div class="text-xs text-gray-400"><?php echo $rank_name; ?> • Level <?php echo $level; ?></div>
                    </div>
                </div>
                <div class="xp-bar-container h-2 rounded-full bg-gray-800">
                    <div class="xp-bar-fill h-full rounded-full" style="width: <?php echo $xp_progress; ?>%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1"><?php echo number_format($current_xp); ?> XP</div>
            </div>
            
            <nav class="flex flex-col space-y-4">
                <a href="lobby/" class="text-gray-300 hover:text-gold text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-door-open mr-3 w-6"></i> Lobby
                </a>
                <a href="lobby/my_collection.php" class="text-gray-300 hover:text-gold text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-gem mr-3 w-6"></i> My Collection
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="../admin/" class="text-red-400 hover:text-red-300 text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-cog mr-3 w-6"></i> Admin Panel
                </a>
                <?php endif; ?>
            </nav>
            
            <div class="mt-auto">
                <a href="logout.php" class="flex items-center justify-center gap-2 w-full py-3 bg-red-900/20 text-red-400 rounded-lg">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        <?php else: ?>
            <nav class="flex flex-col space-y-4">
                <a href="login.php" class="btn-museum text-center py-3">Login</a>
                <a href="register.php" class="btn-museum bg-gold text-black text-center py-3">Register</a>
            </nav>
        <?php endif; ?>
    </div>
</div>
