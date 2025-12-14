<?php
/**
 * Modal Component
 * Reusable modal dialog
 * 
 * @param string $id - Modal ID for JavaScript targeting
 * @param string $title - Modal title
 * @param string $content - Optional content (or use slot)
 * @param string $size - Modal size (sm, md, lg, xl, full)
 */

$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    'full' => 'max-w-4xl'
];

$modalSize = $sizeClasses[$size ?? 'md'] ?? $sizeClasses['md'];
?>

<div id="<?php echo htmlspecialchars($id); ?>" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300">
    <div class="relative <?php echo $modalSize; ?> w-full mx-4 bg-darker-bg border border-gray-700 rounded-xl shadow-2xl transform scale-90 transition-transform duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <h3 class="text-xl font-serif text-gold"><?php echo htmlspecialchars($title); ?></h3>
            <button onclick="closeModal('<?php echo htmlspecialchars($id); ?>')" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 modal-body">
            <?php if (!empty($content)): ?>
                <?php echo $content; ?>
            <?php endif; ?>
        </div>
        
        <!-- Footer (optional, for slots) -->
        <div class="modal-footer"></div>
    </div>
</div>

<script>
// Generic modal functions
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.bg-darker-bg').classList.remove('scale-90');
        }, 10);
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('opacity-0');
        modal.querySelector('.bg-darker-bg').classList.add('scale-90');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
}
</script>
