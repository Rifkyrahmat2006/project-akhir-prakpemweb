<?php
// Mock session data for frontend dev if not set
if (!isset($_SESSION)) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Visitor';
$level = $_SESSION['level'] ?? 1;
?>

<nav class="bg-dark-bg border-b border-gray-800 sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Brand -->
            <a href="index.php" class="flex items-center space-x-2">
                <i class="fas fa-landmark text-gold text-2xl"></i>
                <span class="font-serif text-xl font-bold text-white tracking-wider">VESPERA<span class="text-gold">VELORIA</span></span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <?php if ($isLoggedIn): ?>
                    <a href="lobby/" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Lobby</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="../../admin/" class="text-red-400 hover:text-red-300 transition duration-300 font-sans uppercase text-sm tracking-wide font-bold">Admin Panel</a>
                    <?php endif; ?>
                    
                    <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-700">
                        <div class="text-right">
                            <div class="text-xs text-gray-400">Welcome,</div>
                            <div class="text-gold font-serif text-sm"><?php echo htmlspecialchars($username); ?></div>
                        </div>
                        <div class="h-8 w-8 rounded-full bg-gray-800 border border-gold flex items-center justify-center text-gold">
                            <span class="font-bold text-xs">LV.<?php echo $level; ?></span>
                        </div>
                    </div>
                    
                    <a href="logout.php" class="ml-4 text-gray-400 hover:text-red-400 text-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-300 hover:text-gold transition duration-300 font-sans uppercase text-sm tracking-wide">Login</a>
                    <a href="register.php" class="btn-museum text-xs">Register</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button class="text-gray-300 hover:text-gold focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
