<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database and Models
require_once '../app/Config/database.php';
require_once '../app/Models/User.php';

$user_id = $_SESSION['user_id'];

// Fetch user data using Model
$user = User::findById($conn, $user_id);

// Get XP and level data using Model (level is calculated, not stored)
$xp_data = User::getXpAndLevel($conn, $user_id);
$current_level = $xp_data['level'];  // Use calculated level, not from DB!
$current_xp = $xp_data['xp'];

// Update session with latest data
$_SESSION['xp'] = $current_xp;
$_SESSION['level'] = $current_level;  // Use calculated level

// Fetch stats using Model
$stats = User::getStats($conn, $user_id);
$collection_count = $stats['artifacts_collected'];
$quiz_count = $stats['quizzes_completed'];

// Total artifacts in museum
$total_artifacts = $conn->query("SELECT COUNT(*) as count FROM artifacts")->fetch_assoc()['count'];

// Total quizzes
$total_quizzes = $conn->query("SELECT COUNT(*) as count FROM quizzes")->fetch_assoc()['count'];
$xp_progress = $xp_data['progress'];
$xp_needed = $xp_data['xp_for_next_level'] - $current_xp;

// Get rank info
$ranks = [
    1 => ['name' => 'Visitor', 'icon' => 'fa-user', 'color' => 'text-gray-400'],
    2 => ['name' => 'Explorer', 'icon' => 'fa-compass', 'color' => 'text-blue-400'],
    3 => ['name' => 'Historian', 'icon' => 'fa-book', 'color' => 'text-purple-400'],
    4 => ['name' => 'Royal Curator', 'icon' => 'fa-crown', 'color' => 'text-gold']
];
$rank = $ranks[$current_level] ?? $ranks[1];

// Get Avatar URL
$avatarUrl = User::getAvatarUrl($user);

// Collection badge
$collection_badge = null;
if ($collection_count >= 20) {
    $collection_badge = ['name' => 'Gold Collector', 'class' => 'text-yellow-400', 'icon' => 'fa-crown'];
} elseif ($collection_count >= 10) {
    $collection_badge = ['name' => 'Silver Collector', 'class' => 'text-gray-300', 'icon' => 'fa-medal'];
} elseif ($collection_count >= 5) {
    $collection_badge = ['name' => 'Bronze Collector', 'class' => 'text-orange-400', 'icon' => 'fa-award'];
}

include 'header.php';
include 'navbar.php';
?>

<div class="min-h-screen bg-black text-gray-200" style="background-image: url('/project-akhir/public/assets/img/pattern_dark.png');">
    
    <div class="container mx-auto px-4 py-8 page-fade-in max-w-5xl">
        
        <!-- Profile Header with Glass Effect -->
        <div class="relative overflow-hidden rounded-2xl p-8 mb-8 border border-white/10 bg-white/5 backdrop-blur-md shadow-2xl">
            <!-- Decorative Glow -->
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-white/10 shadow-lg group-hover:border-gold/50 transition-colors duration-300">
                         <img src="<?php echo $avatarUrl; ?>" alt="Profile Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-neutral-900 border border-gold/30 text-gold px-4 py-1 rounded-full text-xs font-bold tracking-widest shadow-lg">
                        LV.<?php echo $current_level; ?>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="flex-grow text-center md:text-left pt-2">
                    <h1 class="text-4xl font-serif text-white mb-2 tracking-wide"><?php echo htmlspecialchars($user['username']); ?></h1>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                        <p class="text-sm font-medium tracking-wider uppercase <?php echo $rank['color']; ?> bg-white/5 px-3 py-1 rounded border border-white/5">
                            <i class="fas <?php echo $rank['icon']; ?> mr-2"></i><?php echo $rank['name']; ?>
                        </p>
                        <p class="text-gray-500 text-xs flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>Joined <?php echo date('F Y', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                    
                    <!-- New Minimal XP Bar -->
                    <div class="max-w-lg mx-auto md:mx-0">
                        <div class="flex justify-between text-xs text-gray-400 mb-2 font-mono">
                            <span>CURRENT XP: <span class="text-white"><?php echo number_format($current_xp); ?></span></span>
                            <?php if ($current_level < 4): ?>
                                <span>NEXT LEVEL: <span class="text-white"><?php echo number_format($xp_needed); ?></span></span>
                            <?php else: ?>
                                <span class="text-gold">MAX LEVEL</span>
                            <?php endif; ?>
                        </div>
                        <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-gold to-amber-200 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(197,160,89,0.5)]" style="width: <?php echo $xp_progress; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Simplified Quick Stats -->
                <div class="flex gap-6 mt-6 md:mt-0 border-t md:border-t-0 md:border-l border-white/10 pt-6 md:pt-0 md:pl-8">
                    <div class="text-center">
                        <div class="text-3xl font-serif text-white mb-1"><?php echo $collection_count; ?></div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-[0.2em]">Artifacts</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-serif text-white mb-1"><?php echo $quiz_count; ?></div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-[0.2em]">Quizzes</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Progress Section -->
            <div class="p-6 rounded-xl border border-white/5 bg-neutral-900/50 backdrop-blur-sm">
                <h2 class="text-lg font-serif text-gray-200 mb-6 flex items-center border-b border-white/5 pb-4">
                    <i class="fas fa-chart-line text-gold mr-3"></i> Progress Overview
                </h2>
                
                <!-- Collection Progress -->
                <div class="mb-8 group">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-400 group-hover:text-gray-300 transition-colors">Artifact Collection</span>
                        <span class="font-mono text-gold"><?php echo $collection_count; ?> <span class="text-gray-600">/</span> <?php echo $total_artifacts; ?></span>
                    </div>
                    <div class="w-full bg-gray-800/50 rounded-full h-1">
                        <div class="bg-gray-400 h-1 rounded-full transition-all group-hover:bg-gold duration-500" 
                             style="width: <?php echo $total_artifacts > 0 ? ($collection_count / $total_artifacts * 100) : 0; ?>%"></div>
                    </div>
                </div>
                
                <!-- Quiz Progress -->
                <div class="mb-4 group">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-gray-400 group-hover:text-gray-300 transition-colors">Quiz Mastery</span>
                        <span class="font-mono text-gold"><?php echo $quiz_count; ?> <span class="text-gray-600">/</span> <?php echo $total_quizzes; ?></span>
                    </div>
                    <div class="w-full bg-gray-800/50 rounded-full h-1">
                        <div class="bg-gray-400 h-1 rounded-full transition-all group-hover:bg-gold duration-500" 
                             style="width: <?php echo $total_quizzes > 0 ? ($quiz_count / $total_quizzes * 100) : 0; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Achievements Section -->
            <div class="p-6 rounded-xl border border-white/5 bg-neutral-900/50 backdrop-blur-sm">
                <h2 class="text-lg font-serif text-gray-200 mb-6 flex items-center border-b border-white/5 pb-4">
                    <i class="fas fa-certificate text-gold mr-3"></i> Badges & Rank
                </h2>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded bg-black/50 border border-white/10 flex items-center justify-center">
                        <i class="fas <?php echo $rank['icon']; ?> <?php echo $rank['color']; ?> text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400">Current Rank</div>
                        <div class="text-lg text-white font-serif"><?php echo $rank['name']; ?></div>
                    </div>
                </div>

                <?php if ($collection_badge): ?>
                    <div class="flex items-center gap-4 p-4 rounded-lg bg-gradient-to-r from-white/5 to-transparent border border-white/5">
                        <div class="w-10 h-10 rounded-full bg-black/50 flex items-center justify-center">
                             <i class="fas <?php echo $collection_badge['icon']; ?> <?php echo $collection_badge['class']; ?>"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Unlocked Badge</div>
                            <div class="text-white font-medium"><?php echo $collection_badge['name']; ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-sm text-gray-500 italic p-4 text-center border border-dashed border-white/10 rounded-lg">
                        Collect more artifacts to earn badges.
                    </div>
                <?php endif; ?>
                
                <div class="mt-6 pt-4 border-t border-white/5">
                    <a href="lobby/my_collection.php" class="block w-full text-center py-2 rounded bg-white/5 hover:bg-gold/10 hover:text-gold text-gray-400 text-sm transition-all border border-transparent hover:border-gold/30">
                        View Full Collection
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="mt-8 flex justify-end gap-4">
             <a href="settings.php" class="px-6 py-2 rounded border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 transition-colors text-sm">
                <i class="fas fa-cog mr-2"></i>Settings
            </a>
            <a href="logout.php" class="px-6 py-2 rounded bg-red-900/20 border border-red-900/50 text-red-500 hover:bg-red-900/40 transition-colors text-sm">
                <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
            </a>
        </div>
        
    </div>
</div>

<?php include 'footer.php'; ?>
