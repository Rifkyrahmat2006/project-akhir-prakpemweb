<?php
/**
 * Admin Sidebar Component
 * Reusable sidebar for admin panel
 * 
 * @param string $active - Active page identifier
 */

$pages = [
    'dashboard' => ['icon' => 'fa-chart-line', 'label' => 'Dashboard', 'href' => 'index.php'],
    'artifacts' => ['icon' => 'fa-boxes', 'label' => 'Manage Artifacts', 'href' => 'artifacts.php'],
    'quizzes' => ['icon' => 'fa-question-circle', 'label' => 'Manage Quizzes', 'href' => 'quizzes.php'],
    'users' => ['icon' => 'fa-users', 'label' => 'Visitors', 'href' => 'users.php'],
    'room_editor' => ['icon' => 'fa-map', 'label' => 'Room Editor', 'href' => 'room_editor.php'],
];
?>

<aside class="w-64 bg-darker-bg border-r border-gold/20 flex flex-col">
    <div class="p-6 border-b border-gold/20">
        <h1 class="text-gold font-serif text-2xl font-bold">Curator Panel</h1>
        <p class="text-gray-500 text-xs uppercase tracking-widest mt-1">Admin Access</p>
    </div>
    
    <nav class="flex-grow p-4 space-y-2">
        <?php foreach ($pages as $key => $page): ?>
            <?php 
            $isActive = ($active ?? 'dashboard') === $key;
            $activeClass = $isActive 
                ? 'bg-gold/10 text-gold border-l-4 border-gold' 
                : 'text-gray-400 hover:text-white hover:bg-gray-800 transition';
            ?>
            <a href="<?php echo $page['href']; ?>" class="block px-4 py-3 rounded <?php echo $activeClass; ?>">
                <i class="fas <?php echo $page['icon']; ?> w-6"></i> <?php echo $page['label']; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="p-4 border-t border-gold/20">
        <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/index.php" class="block w-full text-center py-2 border border-gray-700 text-gray-400 hover:text-white hover:border-white rounded transition mb-2">
            <i class="fas fa-eye mr-2"></i> View Site
        </a>
        <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/logout.php" class="block w-full text-center py-2 bg-red-900/30 border border-red-800 text-red-400 hover:bg-red-900/50 rounded transition">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</aside>
