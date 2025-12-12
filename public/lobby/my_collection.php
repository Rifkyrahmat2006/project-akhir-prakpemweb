<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database MUST be loaded before includes
require_once '../../app/Config/database.php';

$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['level'] ?? 1;

// Rank names based on level
$ranks = [
    1 => 'Visitor',
    2 => 'Explorer', 
    3 => 'Historian',
    4 => 'Royal Curator'
];
$rank_name = $ranks[$user_level] ?? 'Visitor';

// Fetch user's collected artifacts
$sql = "SELECT a.*, uc.collected_at 
        FROM user_collections uc 
        JOIN artifacts a ON uc.artifact_id = a.id 
        WHERE uc.user_id = ? 
        ORDER BY uc.collected_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$my_collection = [];
while($row = $result->fetch_assoc()) {
    $my_collection[] = $row;
}

$collection_count = count($my_collection);

// Determine collection badge
$badge = null;
if ($collection_count >= 20) {
    $badge = ['name' => 'Gold Collector', 'class' => 'badge-gold', 'icon' => 'fa-crown'];
} elseif ($collection_count >= 10) {
    $badge = ['name' => 'Silver Collector', 'class' => 'badge-silver', 'icon' => 'fa-medal'];
} elseif ($collection_count >= 5) {
    $badge = ['name' => 'Bronze Collector', 'class' => 'badge-bronze', 'icon' => 'fa-award'];
}

include '../header.php';
include '../navbar.php';
?>

<div class="flex-grow container mx-auto px-4 py-8 page-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
        <div>
            <h1 class="text-4xl text-gold font-serif mb-2">My Collection</h1>
            <p class="text-gray-400">Your personal Cabinet of Curiosities.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-4">
            <!-- Collection Count -->
            <div class="text-center px-4 py-2 border border-gold/30 rounded bg-darker-bg">
                <div class="text-2xl text-white font-bold"><?php echo $collection_count; ?></div>
                <div class="text-xs text-gold uppercase tracking-wider">Items</div>
            </div>
            <!-- Rank -->
            <div class="text-center px-4 py-2 border border-gold/30 rounded bg-darker-bg">
                <div class="text-2xl text-white font-bold"><?php echo $rank_name; ?></div>
                <div class="text-xs text-gold uppercase tracking-wider">Rank</div>
            </div>
            <!-- Badge (if earned) -->
            <?php if ($badge): ?>
            <div class="flex items-center px-4 py-2 <?php echo $badge['class']; ?> rounded">
                <i class="fas <?php echo $badge['icon']; ?> mr-2"></i>
                <span class="font-bold"><?php echo $badge['name']; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Badge Progress -->
    <div class="mb-8 p-4 bg-gray-900/50 rounded-lg border border-gray-800">
        <h3 class="text-sm text-gray-400 uppercase tracking-wider mb-3">Collection Badges</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-3 rounded <?php echo $collection_count >= 5 ? 'bg-gradient-to-br from-amber-900/50 to-amber-700/20 border border-amber-600/50' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                <i class="fas fa-award text-2xl <?php echo $collection_count >= 5 ? 'text-amber-500' : 'text-gray-600'; ?> mb-1"></i>
                <div class="text-xs <?php echo $collection_count >= 5 ? 'text-amber-400' : 'text-gray-500'; ?>">Bronze</div>
                <div class="text-xs text-gray-500"><?php echo $collection_count; ?>/5</div>
            </div>
            <div class="text-center p-3 rounded <?php echo $collection_count >= 10 ? 'bg-gradient-to-br from-gray-600/50 to-gray-400/20 border border-gray-400/50' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                <i class="fas fa-medal text-2xl <?php echo $collection_count >= 10 ? 'text-gray-300' : 'text-gray-600'; ?> mb-1"></i>
                <div class="text-xs <?php echo $collection_count >= 10 ? 'text-gray-300' : 'text-gray-500'; ?>">Silver</div>
                <div class="text-xs text-gray-500"><?php echo $collection_count; ?>/10</div>
            </div>
            <div class="text-center p-3 rounded <?php echo $collection_count >= 20 ? 'bg-gradient-to-br from-yellow-600/50 to-yellow-400/20 border border-yellow-500/50' : 'bg-gray-800/50 border border-gray-700 opacity-50'; ?>">
                <i class="fas fa-crown text-2xl <?php echo $collection_count >= 20 ? 'text-yellow-400' : 'text-gray-600'; ?> mb-1"></i>
                <div class="text-xs <?php echo $collection_count >= 20 ? 'text-yellow-400' : 'text-gray-500'; ?>">Gold</div>
                <div class="text-xs text-gray-500"><?php echo $collection_count; ?>/20</div>
            </div>
        </div>
    </div>

    <?php if (empty($my_collection)): ?>
        <div class="text-center py-20 bg-gray-900/50 rounded-lg border border-dashed border-gray-700">
            <i class="fas fa-box-open text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-xl text-gray-400 mb-2">Collection Empty</h3>
            <p class="text-gray-600 mb-6">You haven't found any artifacts yet. Start exploring!</p>
            <a href="index.php" class="btn-museum">Go to Lobby</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($my_collection as $item): ?>
                <div class="museum-card p-4 rounded-lg group cursor-pointer artifact-item"
                     data-id="<?php echo $item['id']; ?>" 
                     data-name="<?php echo htmlspecialchars($item['name']); ?>"
                     data-desc="<?php echo htmlspecialchars($item['description']); ?>"
                     data-collected="true">
                    
                    <div class="relative h-48 mb-4 overflow-hidden rounded bg-gray-800">
                        <div class="absolute inset-0 flex items-center justify-center bg-gray-900">
                            <i class="fas fa-gem text-4xl text-gray-700"></i>
                        </div>
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" 
                             class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition duration-500 opacity-90 group-hover:opacity-100">
                        <div class="absolute top-2 right-2 w-8 h-8 rounded-full bg-green-500/80 flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-lg text-gold font-serif mb-1"><?php echo $item['name']; ?></h3>
                    <p class="text-gray-500 text-xs mb-3 italic">Collected: <?php echo date('M j, Y', strtotime($item['collected_at'])); ?></p>
                    <p class="text-gray-400 text-sm line-clamp-2 leading-relaxed">
                        <?php echo $item['description']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php include '../artifact_detail.php'; ?>
</div>

<?php 
$conn->close();
include '../footer.php'; 
?>
