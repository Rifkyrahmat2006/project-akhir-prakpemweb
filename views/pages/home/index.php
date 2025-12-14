<?php
/**
 * Home/Landing Page View
 * Pure presentation - no logic, just the home page HTML
 */

include BASE_PATH . '/public/header.php';
include BASE_PATH . '/public/navbar.php';
?>

<!-- Hero Section -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[20s] hover:scale-110" 
             style="background-image: url('/project-akhir/public/assets/img/home/hero.png');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-transparent to-black/40"></div>
    </div>
    
    <!-- Floating Particles Effect -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto py-20">
        <!-- Ornamental Element -->
        <div class="mb-8 flex items-center justify-center gap-4">
            <div class="w-16 h-px bg-gradient-to-r from-transparent to-gold"></div>
            <i class="fas fa-landmark text-gold text-2xl"></i>
            <div class="w-16 h-px bg-gradient-to-l from-transparent to-gold"></div>
        </div>
        
        <div class="mb-6">
            <span class="inline-block text-gold tracking-[0.4em] text-xs uppercase font-bold bg-gold/10 px-6 py-2 rounded-full border border-gold/30 backdrop-blur-sm">
                Welcome to the
            </span>
        </div>
        
        <h1 class="text-6xl md:text-8xl font-serif font-bold text-white mb-8 tracking-wide leading-tight">
            <span class="block text-shadow-lg">VESPERA</span>
            <span class="text-gold text-glow">VELORIA</span>
        </h1>
        
        <p class="text-gray-300 text-lg md:text-xl mb-6 max-w-2xl mx-auto font-light leading-relaxed">
            The Classic Old Europe Museum Experience
        </p>
        
        <p class="text-gray-500 text-sm md:text-base mb-12 max-w-xl mx-auto leading-relaxed">
            Step into the past. Explore history, collect rare artifacts, and rise through the ranks from <span class="text-gray-300">Visitor</span> to <span class="text-gold">Royal Curator</span>.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <a href="<?php echo Router::url('/login'); ?>" class="group relative overflow-hidden bg-gold text-black font-bold text-lg px-10 py-4 rounded-sm transition-all duration-500 hover:shadow-[0_0_40px_rgba(197,160,89,0.5)] hover:scale-105">
                <span class="relative z-10 flex items-center gap-3 font-serif uppercase tracking-widest">
                    <i class="fas fa-door-open"></i>
                    Enter Museum
                </span>
                <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 skew-x-12"></div>
            </a>
            
            <a href="<?php echo Router::url('/register'); ?>" class="group flex items-center gap-3 px-8 py-4 text-gold hover:text-white border border-gold/50 hover:border-gold hover:bg-gold/10 transition-all duration-300 font-serif uppercase tracking-widest text-sm backdrop-blur-sm">
                <i class="fas fa-user-plus text-xs group-hover:rotate-12 transition-transform"></i>
                Become a Member
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="bg-gradient-to-b from-black via-darker-bg to-darker-bg py-24 relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-20 bg-gradient-to-b from-gold/50 to-transparent"></div>
    
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <span class="text-gold text-xs uppercase tracking-[0.3em] font-bold">Experience History</span>
            <h2 class="text-4xl md:text-5xl font-serif text-white mt-4 mb-6">Your Journey Awaits</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-gold to-transparent mx-auto"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="group relative bg-gradient-to-b from-gray-900/80 to-black/80 p-8 rounded-lg border border-gray-800 hover:border-gold/50 transition-all duration-500 hover:transform hover:-translate-y-2 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-full bg-gold/10 border border-gold/30 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-gold/20 transition-all duration-300">
                        <i class="fas fa-compass text-gold text-2xl group-hover:rotate-45 transition-transform duration-500"></i>
                    </div>
                    <h3 class="text-2xl text-white mb-4 font-serif">Explore</h3>
                    <p class="text-gray-500 leading-relaxed">Wander through themed halls spanning Medieval, Renaissance, Baroque eras, and unlock the secret Royal Archives.</p>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="group relative bg-gradient-to-b from-gray-900/80 to-black/80 p-8 rounded-lg border border-gray-800 hover:border-gold/50 transition-all duration-500 hover:transform hover:-translate-y-2 overflow-hidden md:mt-8">
                <div class="absolute inset-0 bg-gradient-to-b from-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-full bg-gold/10 border border-gold/30 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-gold/20 transition-all duration-300">
                        <i class="fas fa-gem text-gold text-2xl group-hover:rotate-12 transition-transform duration-500"></i>
                    </div>
                    <h3 class="text-2xl text-white mb-4 font-serif">Collect</h3>
                    <p class="text-gray-500 leading-relaxed">Discover hidden artifacts scattered throughout each hall. Build your personal Cabinet of Curiosities.</p>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="group relative bg-gradient-to-b from-gray-900/80 to-black/80 p-8 rounded-lg border border-gray-800 hover:border-gold/50 transition-all duration-500 hover:transform hover:-translate-y-2 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-gold/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-full bg-gold/10 border border-gold/30 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-gold/20 transition-all duration-300">
                        <i class="fas fa-crown text-gold text-2xl group-hover:rotate-12 transition-transform duration-500"></i>
                    </div>
                    <h3 class="text-2xl text-white mb-4 font-serif">Rank Up</h3>
                    <p class="text-gray-500 leading-relaxed">Gain experience points to level up. Progress from Visitor to Explorer, Historian, and finally Royal Curator.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="relative py-24 overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('https://images.unsplash.com/photo-1596128827407-9f1f1a23e9e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 to-black"></div>
    
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h2 class="text-3xl md:text-4xl font-serif text-white mb-6">Ready to Begin Your Journey?</h2>
        <p class="text-gray-400 mb-10 max-w-xl mx-auto">Join thousands of explorers discovering the wonders of Classic Old Europe.</p>
        <a href="<?php echo Router::url('/register'); ?>" class="inline-flex items-center gap-3 bg-gold hover:bg-gold-hover text-black font-bold px-10 py-4 rounded-sm transition-all duration-300 hover:shadow-[0_0_40px_rgba(197,160,89,0.4)] font-serif uppercase tracking-widest">
            <i class="fas fa-user-plus"></i>
            Start Exploring Now
        </a>
    </div>
</div>

<style>
    .text-glow {
        text-shadow: 0 0 40px rgba(197, 160, 89, 0.4), 0 0 80px rgba(197, 160, 89, 0.2);
    }
    
    .text-shadow-lg {
        text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.8);
    }
    
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(197, 160, 89, 0.6);
        border-radius: 50%;
        animation: float 15s infinite;
    }
    
    .particle-1 { left: 10%; top: 20%; animation-delay: 0s; }
    .particle-2 { left: 30%; top: 60%; animation-delay: 3s; }
    .particle-3 { left: 60%; top: 40%; animation-delay: 6s; }
    .particle-4 { left: 80%; top: 70%; animation-delay: 9s; }
    .particle-5 { left: 90%; top: 30%; animation-delay: 12s; }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        50% {
            transform: translateY(-100px) translateX(20px);
            opacity: 0.5;
        }
        90% {
            opacity: 1;
        }
    }
</style>

<?php include BASE_PATH . '/public/footer.php'; ?>
