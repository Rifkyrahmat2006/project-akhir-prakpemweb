<?php
/**
 * My Collection Page
 * Uses Middleware for authentication
 */

// Load bootstrap (includes all middleware, models, and database)
require_once __DIR__ . '/../../app/bootstrap.php';

// Require authentication
requireAuth('../login.php');

$user_id = userId();
$user_level = userLevel();

// Get rank name using Model
$rank_name = User::getRankName($user_level);

// Fetch user's collected artifacts using Model
$my_collection = Artifact::getUserCollection($conn, $user_id);
$collection_count = count($my_collection);

// Determine collection badge
$badge = null;
if ($collection_count >= 20) {
    $badge = ['name' => 'Gold Collector', 'class' => 'text-yellow-400', 'icon' => 'fa-crown', 'bg' => 'bg-yellow-400/10 border-yellow-400/30'];
} elseif ($collection_count >= 10) {
    $badge = ['name' => 'Silver Collector', 'class' => 'text-gray-300', 'icon' => 'fa-medal', 'bg' => 'bg-gray-400/10 border-gray-400/30'];
} elseif ($collection_count >= 5) {
    $badge = ['name' => 'Bronze Collector', 'class' => 'text-orange-400', 'icon' => 'fa-award', 'bg' => 'bg-orange-400/10 border-orange-400/30'];
}

// Fetch user's unlocked hidden artifacts using Model
$hidden_artifacts = Artifact::getUserHiddenArtifacts($conn, $user_id);
$hidden_count = count($hidden_artifacts);

include '../header.php';
include '../navbar.php';
?>

<div class="min-h-screen bg-black text-gray-200" style="background-image: url('<?php echo BASE_URL; ?>/assets/img/pattern_dark.png');">
    
    <div class="container mx-auto px-4 py-8 page-fade-in max-w-7xl">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 border-b border-white/10 pb-8">
            <div>
                <h1 class="text-4xl text-white font-serif mb-2 tracking-wide">My Collection</h1>
                <p class="text-gray-500 max-w-xl font-light">Your personal Cabinet of Curiosities, showcasing the treasures you've discovered across eras.</p>
            </div>
            
            <div class="mt-6 md:mt-0 flex gap-6">
                <div class="text-right">
                    <div class="text-3xl font-serif text-white"><?php echo $collection_count; ?></div>
                    <div class="text-[10px] text-gray-500 uppercase tracking-[0.2em]">Items Found</div>
                </div>
                <!-- Badge Display (if earned) -->
                <?php if ($badge): ?>
                <div class="flex items-center gap-3 px-4 py-2 rounded-lg <?php echo $badge['bg']; ?> border">
                    <i class="fas <?php echo $badge['icon']; ?> <?php echo $badge['class']; ?> text-xl"></i>
                    <div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Badge</div>
                        <div class="font-medium <?php echo $badge['class']; ?>"><?php echo $badge['name']; ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Badges Mini-dashboard -->
        <div class="mb-12 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Bronze -->
            <div class="p-4 rounded-xl border <?php echo $collection_count >= 5 ? 'border-orange-500/30 bg-orange-900/10' : 'border-white/5 bg-white/5 opacity-40'; ?> flex items-center justify-center flex-col text-center transition-all">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2 <?php echo $collection_count >= 5 ? 'bg-orange-500/20 text-orange-400' : 'bg-white/5 text-gray-600'; ?>">
                    <i class="fas fa-award text-xl"></i>
                </div>
                <div class="text-sm font-medium <?php echo $collection_count >= 5 ? 'text-orange-200' : 'text-gray-500'; ?>">Bronze Collector</div>
                <div class="text-xs text-gray-500 mt-1">5 Items</div>
            </div>
            
             <!-- Silver -->
             <div class="p-4 rounded-xl border <?php echo $collection_count >= 10 ? 'border-gray-400/30 bg-gray-900/10' : 'border-white/5 bg-white/5 opacity-40'; ?> flex items-center justify-center flex-col text-center transition-all">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2 <?php echo $collection_count >= 10 ? 'bg-gray-400/20 text-gray-300' : 'bg-white/5 text-gray-600'; ?>">
                    <i class="fas fa-medal text-xl"></i>
                </div>
                <div class="text-sm font-medium <?php echo $collection_count >= 10 ? 'text-gray-200' : 'text-gray-500'; ?>">Silver Collector</div>
                <div class="text-xs text-gray-500 mt-1">10 Items</div>
            </div>

             <!-- Gold -->
             <div class="p-4 rounded-xl border <?php echo $collection_count >= 20 ? 'border-yellow-500/30 bg-yellow-900/10' : 'border-white/5 bg-white/5 opacity-40'; ?> flex items-center justify-center flex-col text-center transition-all">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2 <?php echo $collection_count >= 20 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-white/5 text-gray-600'; ?>">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <div class="text-sm font-medium <?php echo $collection_count >= 20 ? 'text-yellow-200' : 'text-gray-500'; ?>">Gold Collector</div>
                <div class="text-xs text-gray-500 mt-1">20 Items</div>
            </div>
        </div>

        <?php if (empty($my_collection)): ?>
            <div class="text-center py-20 rounded-xl border border-dashed border-white/10 bg-white/5">
                <div class="w-20 h-20 rounded-full bg-black/50 mx-auto flex items-center justify-center mb-6">
                    <i class="fas fa-box-open text-3xl text-gray-600"></i>
                </div>
                <h3 class="text-xl text-white font-serif mb-2">Collection Empty</h3>
                <p class="text-gray-500 mb-6">You haven't found any artifacts yet.</p>
                <a href="index.php" class="inline-block px-6 py-2 border border-gold/50 text-gold hover:bg-gold hover:text-black transition-colors rounded uppercase tracking-widest text-sm">Start Exploring</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($my_collection as $item): ?>
                    <div class="group relative bg-neutral-900 border border-white/5 rounded-xl overflow-hidden hover:border-gold/30 transition-all duration-300 hover:shadow-[0_0_30px_rgba(197,160,89,0.1)] cursor-pointer artifact-item"
                        data-id="<?php echo $item['id']; ?>" 
                        data-name="<?php echo htmlspecialchars($item['name']); ?>"
                        data-desc="<?php echo htmlspecialchars($item['description']); ?>"
                        data-collected="true">
                        
                        <!-- Image Container -->
                        <div class="relative h-56 overflow-hidden bg-black">
                             <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" 
                                  class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                             
                             <!-- Overlay Gradient -->
                             <div class="absolute inset-0 bg-gradient-to-t from-neutral-900 to-transparent opacity-80"></div>
                             
                             <!-- Checkmark -->
                             <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-green-500/20 text-green-500 border border-green-500/30 flex items-center justify-center backdrop-blur-sm">
                                <i class="fas fa-check text-xs"></i>
                             </div>
                        </div>

                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 w-full p-5 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-lg text-white font-serif mb-1 group-hover:text-gold transition-colors"><?php echo $item['name']; ?></h3>
                            <div class="flex items-center text-[10px] text-gray-500 uppercase tracking-wider mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i> <?php echo date('M j, Y', strtotime($item['collected_at'])); ?>
                            </div>
                            <p class="text-gray-400 text-sm line-clamp-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
                                <?php echo htmlspecialchars($item['description']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php include '../artifact_detail.php'; ?>
        
        <!-- Hidden Artifacts Section -->
        <?php if (!empty($hidden_artifacts)): ?>
        <div class="mt-20 border-t border-white/10 pt-12">
            <h2 class="text-2xl text-purple-200 font-serif mb-8 flex items-center gap-3">
                <span class="w-8 h-8 rounded bg-purple-500/20 flex items-center justify-center text-purple-400 text-sm"><i class="fas fa-gem"></i></span>
                Secret Artifacts
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($hidden_artifacts as $hidden): ?>
                    <div class="group bg-purple-900/10 border border-purple-500/20 rounded-xl overflow-hidden hover:bg-purple-900/20 hover:border-purple-500/40 transition-all duration-300">
                        <div class="relative h-48 bg-black/40 flex items-center justify-center overflow-hidden">
                             <?php if ($hidden['hidden_artifact_image']): ?>
                                <img src="<?php echo htmlspecialchars($hidden['hidden_artifact_image']); ?>" class="h-32 object-contain drop-shadow-[0_0_15px_rgba(168,85,247,0.5)] group-hover:scale-110 transition-transform duration-500">
                            <?php else: ?>
                                <i class="fas fa-gem text-5xl text-purple-500/30"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="text-lg text-purple-100 font-serif mb-1"><?php echo htmlspecialchars($hidden['hidden_artifact_name']); ?></h3>
                            <div class="text-xs text-purple-400/60 uppercase tracking-wider mb-3">
                                Found in <?php echo htmlspecialchars($hidden['room_name']); ?>
                            </div>
                            <div class="inline-block px-2 py-1 rounded bg-purple-500/20 border border-purple-500/30 text-purple-300 text-[10px] font-bold">
                                +<?php echo $hidden['hidden_artifact_xp']; ?> XP BONUS
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../footer.php'; ?>
