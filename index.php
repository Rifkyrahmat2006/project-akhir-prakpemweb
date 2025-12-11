<?php
session_start();
// Redirect to lobby if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: lobby/");
    exit();
}
include 'public/header.php';
include 'public/navbar.php';
?>

<div class="flex-grow flex flex-col items-center justify-center relative overflow-hidden">
    <!-- Background Overlay -->
    <div class="absolute inset-0 z-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1544967082-d9d25d867d66?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');"></div>
    <div class="absolute inset-0 z-0 bg-gradient-to-b from-darker-bg via-transparent to-darker-bg"></div>

    <!-- Content -->
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <div class="mb-6 animate-fade-in-down">
            <span class="text-gold tracking-[0.3em] text-sm uppercase font-bold">Welcome to the</span>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-serif font-bold text-white mb-6 text-shadow tracking-wide leading-tight">
            Classic Old Europe <br> <span class="text-gold">Museum</span>
        </h1>
        
        <p class="text-gray-300 text-lg md:text-xl mb-10 max-w-2xl mx-auto font-light leading-relaxed">
            Step into the past. Explore history, collect rare artifacts, and rise through the ranks from Visitor to Royal Curator.
        </p>
        
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="login.php" class="btn-museum text-lg px-8 py-3 bg-dark-bg/50 hover:bg-gold hover:text-darker-bg">
                Enter Museum
            </a>
            <a href="register.php" class="px-8 py-3 text-gold hover:text-gold-hover border-b border-transparent hover:border-gold transition-all duration-300 font-serif uppercase tracking-widest text-sm flex items-center justify-center">
                Become a Member
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-dark-bg py-20 border-t border-gray-800 relative z-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
            <div class="p-6 border border-gray-800 rounded-lg hover:border-gold/30 transition duration-300">
                <i class="fas fa-search text-gold text-4xl mb-4"></i>
                <h3 class="text-xl text-white mb-2">Explore</h3>
                <p class="text-gray-500 text-sm">Wander through themed halls from the Renaissance to the Baroque era.</p>
            </div>
            <div class="p-6 border border-gray-800 rounded-lg hover:border-gold/30 transition duration-300">
                <i class="fas fa-gem text-gold text-4xl mb-4"></i>
                <h3 class="text-xl text-white mb-2">Collect</h3>
                <p class="text-gray-500 text-sm">Find hidden artifacts and build your own private collection.</p>
            </div>
            <div class="p-6 border border-gray-800 rounded-lg hover:border-gold/30 transition duration-300">
                <i class="fas fa-crown text-gold text-4xl mb-4"></i>
                <h3 class="text-xl text-white mb-2">Rank Up</h3>
                <p class="text-gray-500 text-sm">Gain experience and unlock exclusive access to the Royal Archives.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'public/footer.php'; ?>
