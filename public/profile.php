<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../app/Config/database.php';

$user_id = $_SESSION['user_id'];

// Fetch user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Update session with latest data
$_SESSION['xp'] = $user['xp'];
$_SESSION['level'] = $user['level'];

// Fetch collection stats
$collection_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_collections WHERE user_id = ?");
$collection_stmt->bind_param("i", $user_id);
$collection_stmt->execute();
$collection_count = $collection_stmt->get_result()->fetch_assoc()['count'];

// Fetch quiz stats
$quiz_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_quizzes WHERE user_id = ?");
$quiz_stmt->bind_param("i", $user_id);
$quiz_stmt->execute();
$quiz_count = $quiz_stmt->get_result()->fetch_assoc()['count'];

// Total artifacts in museum
$total_artifacts = $conn->query("SELECT COUNT(*) as count FROM artifacts")->fetch_assoc()['count'];

// Total quizzes
$total_quizzes = $conn->query("SELECT COUNT(*) as count FROM quizzes")->fetch_assoc()['count'];

// XP thresholds
$xp_thresholds = [
    1 => ['min' => 0, 'max' => 100],
    2 => ['min' => 101, 'max' => 300],
    3 => ['min' => 301, 'max' => 600],
    4 => ['min' => 601, 'max' => 1000]
];

$ranks = [
    1 => ['name' => 'Visitor', 'icon' => 'fa-user', 'color' => 'text-gray-400'],
    2 => ['name' => 'Explorer', 'icon' => 'fa-compass', 'color' => 'text-blue-400'],
    3 => ['name' => 'Historian', 'icon' => 'fa-book', 'color' => 'text-purple-400'],
    4 => ['name' => 'Royal Curator', 'icon' => 'fa-crown', 'color' => 'text-gold']
];

$current_level = $user['level'];
$current_xp = $user['xp'];
$current_threshold = $xp_thresholds[$current_level] ?? $xp_thresholds[1];
$rank = $ranks[$current_level] ?? $ranks[1];

// Calculate XP progress
$xp_progress = 0;
$xp_needed = 0;
if ($current_level < 4) {
    $range = $current_threshold['max'] - $current_threshold['min'];
    $progress = $current_xp - $current_threshold['min'];
    $xp_progress = min(100, max(0, ($progress / $range) * 100));
    $xp_needed = $current_threshold['max'] - $current_xp;
} else {
    $xp_progress = 100;
}

// Collection badge
$collection_badge = null;
if ($collection_count >= 20) {
    $collection_badge = ['name' => 'Gold Collector', 'class' => 'badge-gold', 'icon' => 'fa-crown'];
} elseif ($collection_count >= 10) {
    $collection_badge = ['name' => 'Silver Collector', 'class' => 'badge-silver', 'icon' => 'fa-medal'];
} elseif ($collection_count >= 5) {
    $collection_badge = ['name' => 'Bronze Collector', 'class' => 'badge-bronze', 'icon' => 'fa-award'];
}

include 'header.php';
include 'navbar.php';
?>

<div class="flex-grow container mx-auto px-4 py-8 page-fade-in">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-8 mb-8 border border-gold/20">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- Avatar -->
            <div class="relative">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-gold/30 to-gold/10 border-4 border-gold flex items-center justify-center">
                    <i class="fas <?php echo $rank['icon']; ?> text-5xl <?php echo $rank['color']; ?>"></i>
                </div>
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-gold text-black px-3 py-1 rounded-full text-xs font-bold">
                    LV.<?php echo $current_level; ?>
                </div>
            </div>
            
            <!-- User Info -->
            <div class="flex-grow text-center md:text-left">
                <h1 class="text-3xl font-serif text-gold mb-2"><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="text-lg <?php echo $rank['color']; ?> mb-4">
                    <i class="fas <?php echo $rank['icon']; ?> mr-2"></i><?php echo $rank['name']; ?>
                </p>
                
                <!-- XP Bar -->
                <div class="max-w-md mx-auto md:mx-0">
                    <div class="flex justify-between text-sm text-gray-400 mb-1">
                        <span><?php echo number_format($current_xp); ?> XP</span>
                        <?php if ($current_level < 4): ?>
                            <span><?php echo number_format($xp_needed); ?> XP to Level <?php echo $current_level + 1; ?></span>
                        <?php else: ?>
                            <span>MAX LEVEL</span>
                        <?php endif; ?>
                    </div>
                    <div class="xp-bar-container h-3 rounded-full bg-gray-700">
                        <div class="xp-bar-fill h-full rounded-full" style="width: <?php echo $xp_progress; ?>%"></div>
                    </div>
                </div>
                
                <!-- Member Since -->
                <p class="text-gray-500 text-sm mt-4">
                    <i class="fas fa-calendar-alt mr-2"></i>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex md:flex-col gap-4">
                <div class="text-center px-6 py-3 bg-gray-800/50 rounded-lg border border-gray-700">
                    <div class="text-2xl font-bold text-gold"><?php echo $collection_count; ?></div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider">Artifacts</div>
                </div>
                <div class="text-center px-6 py-3 bg-gray-800/50 rounded-lg border border-gray-700">
                    <div class="text-2xl font-bold text-gold"><?php echo $quiz_count; ?></div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider">Quizzes</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Progress Stats -->
        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
            <h2 class="text-xl font-serif text-gold mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line"></i> Progress
            </h2>
            
            <!-- Collection Progress -->
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-300">Artifacts Collected</span>
                    <span class="text-gold"><?php echo $collection_count; ?>/<?php echo $total_artifacts; ?></span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2">
                    <div class="bg-gradient-to-r from-gold to-yellow-500 h-2 rounded-full transition-all" 
                         style="width: <?php echo $total_artifacts > 0 ? ($collection_count / $total_artifacts * 100) : 0; ?>%"></div>
                </div>
            </div>
            
            <!-- Quiz Progress -->
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-300">Quizzes Completed</span>
                    <span class="text-gold"><?php echo $quiz_count; ?>/<?php echo $total_quizzes; ?></span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all" 
                         style="width: <?php echo $total_quizzes > 0 ? ($quiz_count / $total_quizzes * 100) : 0; ?>%"></div>
                </div>
            </div>
            
            <!-- Level Progress -->
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-300">Level Progress</span>
                    <span class="text-gold"><?php echo $current_level; ?>/4</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all" 
                         style="width: <?php echo ($current_level / 4) * 100; ?>%"></div>
                </div>
            </div>
        </div>
        
        <!-- Achievements -->
        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
            <h2 class="text-xl font-serif text-gold mb-6 flex items-center gap-2">
                <i class="fas fa-trophy"></i> Achievements
            </h2>
            
            <div class="space-y-4">
                <!-- Rank Achievement -->
                <div class="flex items-center gap-4 p-4 rounded-lg <?php echo $current_level >= 1 ? 'bg-gold/10 border border-gold/30' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                    <div class="w-12 h-12 rounded-full <?php echo $current_level >= 1 ? 'bg-gold/20' : 'bg-gray-700'; ?> flex items-center justify-center">
                        <i class="fas fa-user <?php echo $current_level >= 1 ? 'text-gold' : 'text-gray-500'; ?>"></i>
                    </div>
                    <div>
                        <div class="font-bold <?php echo $current_level >= 1 ? 'text-gold' : 'text-gray-500'; ?>">First Steps</div>
                        <div class="text-xs text-gray-400">Join the museum as a Visitor</div>
                    </div>
                    <?php if ($current_level >= 1): ?>
                        <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Explorer Achievement -->
                <div class="flex items-center gap-4 p-4 rounded-lg <?php echo $current_level >= 2 ? 'bg-blue-900/20 border border-blue-500/30' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                    <div class="w-12 h-12 rounded-full <?php echo $current_level >= 2 ? 'bg-blue-500/20' : 'bg-gray-700'; ?> flex items-center justify-center">
                        <i class="fas fa-compass <?php echo $current_level >= 2 ? 'text-blue-400' : 'text-gray-500'; ?>"></i>
                    </div>
                    <div>
                        <div class="font-bold <?php echo $current_level >= 2 ? 'text-blue-400' : 'text-gray-500'; ?>">Explorer</div>
                        <div class="text-xs text-gray-400">Reach Level 2 (100+ XP)</div>
                    </div>
                    <?php if ($current_level >= 2): ?>
                        <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Historian Achievement -->
                <div class="flex items-center gap-4 p-4 rounded-lg <?php echo $current_level >= 3 ? 'bg-purple-900/20 border border-purple-500/30' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                    <div class="w-12 h-12 rounded-full <?php echo $current_level >= 3 ? 'bg-purple-500/20' : 'bg-gray-700'; ?> flex items-center justify-center">
                        <i class="fas fa-book <?php echo $current_level >= 3 ? 'text-purple-400' : 'text-gray-500'; ?>"></i>
                    </div>
                    <div>
                        <div class="font-bold <?php echo $current_level >= 3 ? 'text-purple-400' : 'text-gray-500'; ?>">Historian</div>
                        <div class="text-xs text-gray-400">Reach Level 3 (300+ XP)</div>
                    </div>
                    <?php if ($current_level >= 3): ?>
                        <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Royal Curator Achievement -->
                <div class="flex items-center gap-4 p-4 rounded-lg <?php echo $current_level >= 4 ? 'bg-yellow-900/20 border border-yellow-500/30' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                    <div class="w-12 h-12 rounded-full <?php echo $current_level >= 4 ? 'bg-yellow-500/20' : 'bg-gray-700'; ?> flex items-center justify-center">
                        <i class="fas fa-crown <?php echo $current_level >= 4 ? 'text-yellow-400' : 'text-gray-500'; ?>"></i>
                    </div>
                    <div>
                        <div class="font-bold <?php echo $current_level >= 4 ? 'text-yellow-400' : 'text-gray-500'; ?>">Royal Curator</div>
                        <div class="text-xs text-gray-400">Reach Level 4 (600+ XP)</div>
                    </div>
                    <?php if ($current_level >= 4): ?>
                        <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Collection Badge -->
                <?php if ($collection_badge): ?>
                <div class="flex items-center gap-4 p-4 rounded-lg bg-amber-900/20 border border-amber-500/30">
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <i class="fas <?php echo $collection_badge['icon']; ?> text-amber-400"></i>
                    </div>
                    <div>
                        <div class="font-bold text-amber-400"><?php echo $collection_badge['name']; ?></div>
                        <div class="text-xs text-gray-400">Collected <?php echo $collection_count; ?> artifacts</div>
                    </div>
                    <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="mt-8 flex flex-wrap gap-4 justify-center">
        <a href="lobby/" class="btn-museum">
            <i class="fas fa-door-open mr-2"></i> Explore Rooms
        </a>
        <a href="lobby/my_collection.php" class="btn-museum">
            <i class="fas fa-gem mr-2"></i> My Collection
        </a>
    </div>
</div>

<?php 
$conn->close();
include 'footer.php'; 
?>
