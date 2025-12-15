<?php
/**
 * Register Page
 * Uses Middleware to redirect if already authenticated
 */

// Load bootstrap (includes all middleware, models, and database)
require_once __DIR__ . '/../app/bootstrap.php';

// Redirect if already logged in
AuthMiddleware::redirectIfAuthenticated('lobby/');

include __DIR__ . '/header.php';
?>

<!-- Hide Scrollbar (non-scrollable page) -->
<style>
    html, body { overflow: hidden; }
</style>

<div class="flex-grow flex items-center justify-center relative py-20 px-4 overflow-hidden">
    <!-- Video Background -->
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
        <source src="<?php echo BASE_URL; ?>/assets/img/endless-login.mp4" type="video/mp4">
    </video>
    <div class="absolute inset-0 z-0 bg-black/50"></div>
    
    <!-- Back Button -->
    <a href="index.php" class="absolute top-6 left-6 z-20 w-12 h-12 flex items-center justify-center rounded-full bg-black/50 border border-gold/30 text-gold hover:bg-gold hover:text-black transition-all duration-300">
        <i class="fas fa-arrow-left text-lg"></i>
    </a>
    
    <div class="w-full max-w-md bg-dark-bg/60 p-8 border border-gold/30 shadow-2xl relative z-10 backdrop-blur-xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl text-gold mb-2">Become a Member</h2>
            <p class="text-gray-500 text-sm italic">Begin your journey as a Visitor.</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-900/20 border border-red-500/50 text-red-300 px-4 py-2 mb-6 text-sm text-center">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/app/Handlers/auth_handler.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="register">
            
            <div>
                <label for="username" class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Username</label>
                <input type="text" id="username" name="username" required 
                    class="w-full bg-darker-bg border border-gray-700 focus:border-gold text-white px-4 py-3 outline-none transition duration-300"
                    placeholder="Choose a unique username">
            </div>

            <div>
                <label for="password" class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-darker-bg border border-gray-700 focus:border-gold text-white px-4 py-3 outline-none transition duration-300"
                    placeholder="Create a password">
            </div>

            <div>
                <label for="confirm_password" class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                    class="w-full bg-darker-bg border border-gray-700 focus:border-gold text-white px-4 py-3 outline-none transition duration-300"
                    placeholder="Repeat your password">
            </div>

            <button type="submit" class="w-full btn-museum bg-gold text-darker-bg font-bold hover:bg-gold-hover mt-4">
                Register
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-500 text-sm">
                Already have an account? 
                <a href="login.php" class="text-gold hover:text-gold-hover underline decoration-dotted">Login here</a>
            </p>
        </div>
    </div>
</div>

<script>
// Stop video when leaving the page
const bgVideo = document.querySelector('video');
if (bgVideo) {
    window.addEventListener('beforeunload', () => {
        bgVideo.pause();
        bgVideo.src = '';
    });
    
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            bgVideo.pause();
        } else {
            bgVideo.play();
        }
    });
}
</script>
