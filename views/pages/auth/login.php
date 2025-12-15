<?php
/**
 * Login View
 * Pure presentation - receives $error, $success variables from controller
 */

include BASE_PATH . '/public/header.php';
?>

<div class="flex-grow flex items-center justify-center relative py-20 px-4 overflow-hidden">
    <!-- Video Background -->
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
        <source src="<?php echo BASE_URL; ?>/assets/img/endless-login.mp4" type="video/mp4">
    </video>
    <div class="absolute inset-0 z-0 bg-black/50"></div>
    
    <!-- Back Button -->
    <a href="<?php echo Router::url('/'); ?>" class="absolute top-6 left-6 z-20 w-12 h-12 flex items-center justify-center rounded-full bg-black/50 border border-gold/30 text-gold hover:bg-gold hover:text-black transition-all duration-300">
        <i class="fas fa-arrow-left text-lg"></i>
    </a>
    
    <div class="w-full max-w-md bg-dark-bg/60 p-8 border border-gold/30 shadow-2xl relative z-10 backdrop-blur-xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl text-gold mb-2">Member Login</h2>
            <p class="text-gray-500 text-sm italic">Welcome back, Curator.</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-900/20 border border-red-500/50 text-red-300 px-4 py-2 mb-6 text-sm text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="bg-green-900/20 border border-green-500/50 text-green-300 px-4 py-2 mb-6 text-sm text-center">
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <form action="/project-akhir/app/Handlers/auth_handler.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="login">
            
            <div>
                <label for="username" class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Username</label>
                <input type="text" id="username" name="username" required 
                    class="w-full bg-darker-bg border border-gray-700 focus:border-gold text-white px-4 py-3 outline-none transition duration-300"
                    placeholder="Enter your username">
            </div>

            <div>
                <label for="password" class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-darker-bg border border-gray-700 focus:border-gold text-white px-4 py-3 outline-none transition duration-300"
                    placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full btn-museum bg-gold text-darker-bg font-bold hover:bg-gold-hover mt-4">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-500 text-sm">
                Don't have a membership? 
                <a href="<?php echo Router::url('/register'); ?>" class="text-gold hover:text-gold-hover underline decoration-dotted">Register here</a>
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
