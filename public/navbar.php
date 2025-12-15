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
$base_path = defined('BASE_URL') ? BASE_URL : '/project-akhir/public';
$avatarUrl = $_SESSION['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=C5A059&color=000&size=128&font-size=0.5';
?>

<nav class="bg-dark-bg border-b border-gray-800 sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Brand -->
            <a href="<?php echo $base_path; ?>/index.php" class="flex items-center space-x-4">
                <img src="<?php echo $base_path; ?>/icon.png" alt="Museum Logo" class="h-12 w-12 mt-[-4px]">
                <span class="font-serif text-xl font-bold text-white tracking-wider">VESPERA<span class="text-gold">VELORIA</span></span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-6">
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo $base_path; ?>/lobby/" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Lobby</a>
                    <a href="<?php echo $base_path; ?>/lobby/my_collection.php" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Collection</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="<?php echo $base_path; ?>/../admin/" class="text-red-400 hover:text-red-300 transition duration-300 font-sans uppercase text-sm tracking-wide font-bold">Admin Panel</a>
                    <?php endif; ?>
                    
                    <!-- User Info with XP Bar -->
                    <a href="<?php echo $base_path; ?>/profile.php" class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-700 hover:opacity-80 transition">
                        <div class="text-right">
                            <div class="flex items-center gap-2">
                                <span class="text-gold font-serif text-sm"><?php echo htmlspecialchars($username); ?></span>
                                <span id="rank-text-desktop" class="text-xs px-2 py-0.5 rounded-full bg-gray-800 border border-gold/30 text-gold"><?php echo $rank_name; ?></span>
                            </div>
                            <!-- XP Progress Bar -->
                            <div class="w-32 mt-1">
                                <div class="xp-bar-container h-1.5 rounded-full bg-gray-800">
                                    <div id="xp-bar-fill-desktop" class="xp-bar-fill h-full rounded-full" style="width: <?php echo $xp_progress; ?>%" data-current-xp="<?php echo $current_xp; ?>" data-level="<?php echo $level; ?>"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-0.5">
                                    <span id="xp-text-desktop"><?php echo number_format($current_xp); ?> XP</span>
                                    <span id="level-text-desktop">LV.<?php echo $level; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-gold">
                             <img src="<?php echo $avatarUrl; ?>" alt="User" class="w-full h-full object-cover">
                        </div>
                    </a>
                    
                    <a href="<?php echo $base_path; ?>/logout.php" class="ml-2 text-gray-400 hover:text-red-400 text-sm" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>/login.php" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Login</a>
                    <a href="<?php echo $base_path; ?>/register.php" class="btn-museum text-xs">Register</a>
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
            <a href="<?php echo $base_path; ?>/profile.php" class="mb-8 p-4 bg-gray-900 rounded-lg border border-gold/20 block hover:bg-gray-800 transition">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-gold">
                         <img src="<?php echo $avatarUrl; ?>" alt="User" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <div class="text-gold font-serif"><?php echo htmlspecialchars($username); ?></div>
                        <div class="text-xs text-gray-400"><span id="rank-text-mobile"><?php echo $rank_name; ?></span> • Level <span id="level-text-mobile"><?php echo $level; ?></span></div>
                    </div>
                </div>
                <div class="xp-bar-container h-2 rounded-full bg-gray-800">
                    <div id="xp-bar-fill-mobile" class="xp-bar-fill h-full rounded-full" style="width: <?php echo $xp_progress; ?>%"></div>
                </div>
                <div id="xp-text-mobile" class="text-xs text-gray-500 mt-1"><?php echo number_format($current_xp); ?> XP</div>
            </a>
            
            <nav class="flex flex-col space-y-4">
                <a href="<?php echo $base_path; ?>/lobby/" class="text-gray-300 hover:text-gold text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-door-open mr-3 w-6"></i> Lobby
                </a>
                <a href="<?php echo $base_path; ?>/lobby/my_collection.php" class="text-gray-300 hover:text-gold text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-gem mr-3 w-6"></i> My Collection
                </a>
                <a href="<?php echo $base_path; ?>/profile.php" class="text-gray-300 hover:text-gold text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-user mr-3 w-6"></i> Profile
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?php echo $base_path; ?>/../admin/" class="text-red-400 hover:text-red-300 text-lg py-2 border-b border-gray-800">
                    <i class="fas fa-cog mr-3 w-6"></i> Admin Panel
                </a>
                <?php endif; ?>
            </nav>
            
            <div class="mt-auto">
                <a href="<?php echo $base_path; ?>/logout.php" class="flex items-center justify-center gap-2 w-full py-3 bg-red-900/20 text-red-400 rounded-lg hover:bg-red-900/40 transition">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        <?php else: ?>
            <nav class="flex flex-col space-y-4">
                <a href="<?php echo $base_path; ?>/login.php" class="btn-museum text-center py-3">Login</a>
                <a href="<?php echo $base_path; ?>/register.php" class="btn-museum bg-gold text-black text-center py-3">Register</a>
            </nav>
        <?php endif; ?>
    </div>
</div>
