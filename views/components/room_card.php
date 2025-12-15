<?php
/**
 * Room Card Component
 * Reusable room selection card for admin pages
 * 
 * @param array $room - Room data (id, name, description, image_url, min_level)
 * @param string $linkTo - Base URL to link to (e.g., 'artifacts.php', will append ?room_id=X)
 * @param string $countLabel - Label for the count (e.g., 'Artifacts', 'Quizzes')
 * @param int $count - Item count to display
 * @param mysqli $conn - Database connection (optional, for auto-count)
 */

$roomId = $room['id'];
$roomName = htmlspecialchars($room['name']);
$roomDesc = htmlspecialchars($room['description'] ?? '');
$roomImage = $room['image_url'] ?: '/project-akhir/public/assets/img/room-placeholder.jpg';
$minLevel = $room['min_level'] ?? 1;
$href = ($linkTo ?? 'room_editor.php') . '?room_id=' . $roomId;
$label = $countLabel ?? 'Items';
$itemCount = $count ?? 0;
?>

<a href="<?php echo $href; ?>" class="bg-darker-bg border border-gray-800 rounded-lg overflow-hidden hover:border-gold/50 hover:bg-gray-800/50 transition group block">
    <!-- Room Image -->
    <div class="h-32 bg-cover bg-center relative" style="background-image: url('<?php echo htmlspecialchars($roomImage); ?>');">
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition"></div>
        <div class="absolute top-2 right-2 bg-gold/90 text-black text-xs px-2 py-1 rounded font-bold">
            Lvl <?php echo $minLevel; ?>+
        </div>
    </div>
    <!-- Room Info -->
    <div class="p-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-white font-serif text-lg group-hover:text-gold transition"><?php echo $roomName; ?></h3>
            <i class="fas fa-chevron-right text-gold opacity-0 group-hover:opacity-100 transition"></i>
        </div>
        <p class="text-gray-500 text-sm line-clamp-2"><?php echo $roomDesc; ?></p>
        <div class="mt-3 flex items-center gap-4 text-xs text-gray-400">
            <span><i class="fas fa-gem text-gold mr-1"></i> <?php echo $itemCount; ?> <?php echo $label; ?></span>
        </div>
    </div>
</a>
