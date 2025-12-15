<?php
/**
 * Settings Page
 * Uses Middleware for authentication
 */

// Load bootstrap (includes all middleware, models, and database)
require_once '../app/bootstrap.php';

// Require authentication
requireAuth('login.php');

$user_id = userId();
$user = User::findById($conn, $user_id);
$avatarUrl = User::getAvatarUrl($user);

include 'header.php';
include 'navbar.php';
?>

<div class="min-h-screen bg-black text-gray-200" style="background-image: url('<?php echo BASE_URL; ?>/assets/img/pattern_dark.png');">
    <div class="container mx-auto px-4 py-12 page-fade-in max-w-3xl">
        
        <h1 class="text-3xl text-white font-serif mb-8 border-b border-white/10 pb-4">Account Settings</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-neutral-900/50 border border-white/10 rounded-xl p-8 backdrop-blur-sm">
            
            <!-- Avatar Section -->
            <div class="mb-10">
                <h2 class="text-xl text-gold font-serif mb-6">Profile Avatar</h2>
                
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white/10 shadow-lg group-hover:border-gold/50 transition-colors">
                            <img src="<?php echo $avatarUrl; ?>?t=<?php echo time(); ?>" alt="Current Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute bottom-0 right-0 bg-gold text-black p-2 rounded-full shadow-lg border border-black/50">
                            <i class="fas fa-camera text-sm"></i>
                        </div>
                    </div>
                    
                    <div class="flex-grow w-full md:w-auto">
                        <form action="../app/Handlers/update_avatar.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label class="block text-gray-400 text-sm mb-2">Upload New Avatar</label>
                                <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-gold/10 file:text-gold
                                  hover:file:bg-gold/20
                                  cursor-pointer
                                " required>
                                <p class="text-xs text-gray-600 mt-1">Recommended size: 500x500px. Max size: 2MB. Format: JPG, PNG.</p>
                            </div>
                            <button type="submit" class="btn-museum bg-gold text-black hover:bg-white w-full md:w-auto">
                                Update Avatar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <hr class="border-white/10 mb-10">

            <!-- Profile Info Section (Read Only for now or expanded later) -->
            <div>
                <h2 class="text-xl text-gold font-serif mb-6">Personal Information</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-2">Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly 
                               class="w-full bg-black/50 border border-white/10 rounded p-3 text-gray-300 focus:outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-gray-500 text-xs uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly 
                               class="w-full bg-black/50 border border-white/10 rounded p-3 text-gray-300 focus:outline-none cursor-not-allowed">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-white/10 flex justify-between items-center">
                <a href="profile.php" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                </a>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
