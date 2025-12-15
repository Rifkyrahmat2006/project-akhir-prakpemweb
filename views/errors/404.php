<?php
/**
 * 404 Error Page
 */
include BASE_PATH . '/public/header.php';
?>

<div class="flex-grow flex items-center justify-center py-20">
    <div class="text-center">
        <div class="text-gold text-9xl font-serif mb-4">404</div>
        <h1 class="text-white text-3xl font-serif mb-4">Page Not Found</h1>
        <p class="text-gray-400 mb-8">The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?php echo Router::url('/'); ?>" class="btn-museum bg-gold text-black hover:bg-gold-hover">
            <i class="fas fa-home mr-2"></i> Return Home
        </a>
    </div>
</div>

<?php include BASE_PATH . '/public/footer.php'; ?>
