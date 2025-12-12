<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin/");
    } else {
        header("Location: lobby/");
    }
    exit();
}
include 'header.php';
include 'navbar.php';
?>

<div class="flex-grow flex items-center justify-center relative py-20 px-4">
    <div class="absolute inset-0 z-0 bg-cover bg-center opacity-10" style="background-image: url('https://images.unsplash.com/photo-1545648507-ca9043228c2c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');"></div>
    
    <div class="w-full max-w-md bg-dark-bg/90 p-8 border border-gold/30 shadow-2xl relative z-10 backdrop-blur-sm">
        <div class="text-center mb-8">
            <h2 class="text-3xl text-gold mb-2">Member Login</h2>
            <p class="text-gray-500 text-sm italic">Welcome back, Curator.</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-900/20 border border-red-500/50 text-red-300 px-4 py-2 mb-6 text-sm text-center">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-900/20 border border-green-500/50 text-green-300 px-4 py-2 mb-6 text-sm text-center">
                Registration successful! Please login.
            </div>
        <?php endif; ?>

        <form action="../app/Handlers/auth_handler.php" method="POST" class="space-y-6">
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
                <a href="register.php" class="text-gold hover:text-gold-hover underline decoration-dotted">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
