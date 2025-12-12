            <p class="text-gray-400">Your personal collection of historical artifacts.</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-4">
            <div class="text-center px-4 py-2 border border-gold/30 rounded bg-darker-bg">
                <div class="text-2xl text-white font-bold"><?php echo count($my_collection); ?></div>
                <div class="text-xs text-gold uppercase tracking-wider">Items</div>
            </div>
            <div class="text-center px-4 py-2 border border-gold/30 rounded bg-darker-bg">
                <div class="text-2xl text-white font-bold"><?php echo $rank_name; ?></div>
                <div class="text-xs text-gold uppercase tracking-wider">Rank</div>
            </div>
        </div>
    </div>

    <?php if (empty($my_collection)): ?>
        <div class="text-center py-20 bg-gray-900/50 rounded-lg border border-dashed border-gray-700">
            <i class="fas fa-box-open text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-xl text-gray-400 mb-2">Collection Empty</h3>
            <p class="text-gray-600 mb-6">You haven't found any artifacts yet.</p>
            <a href="index.php" class="btn-museum">Go to Lobby</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($my_collection as $item): ?>
                <div class="museum-card p-4 rounded-lg group cursor-pointer artifact-item"
                     data-id="<?php echo $item['artifact_id'] ?? $item['id']; ?>" 
                     data-name="<?php echo htmlspecialchars($item['name']); ?>"
                     data-desc="<?php echo htmlspecialchars($item['description']); ?>"
                     data-collected="true">
                    
                    <div class="relative h-48 mb-4 overflow-hidden rounded bg-gray-800">
                         <!-- Fallback icon if no image -->
                        <div class="absolute inset-0 flex items-center justify-center bg-gray-900">
                            <i class="fas fa-gem text-4xl text-gray-700"></i>
                        </div>
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" 
                             class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition duration-500 opacity-90 group-hover:opacity-100">
                    </div>
                    <h3 class="text-lg text-gold font-serif mb-1"><?php echo $item['name']; ?></h3>
                    <p class="text-gray-500 text-xs mb-3 italic">Collected: <?php echo $item['collected_at']; ?></p>
                    <p class="text-gray-400 text-sm line-clamp-3 leading-relaxed">
                        <?php echo $item['description']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php include '../artifact_detail.php'; ?>
</div>

<?php include '../footer.php'; ?>
