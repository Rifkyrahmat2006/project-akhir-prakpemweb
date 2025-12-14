<?php
/**
 * Stat Card Component
 * Reusable statistic card for dashboards
 * 
 * @param string $title - Card title
 * @param mixed $value - Main statistic value
 * @param string $icon - FontAwesome icon class (e.g., 'fa-users')
 * @param string $color - Color theme (blue, gold, green, purple, red)
 * @param string $description - Optional description text
 */

$colorClasses = [
    'blue' => [
        'text' => 'text-white',
        'bg' => 'bg-blue-900/30',
        'icon' => 'text-blue-500',
        'desc' => 'text-blue-400'
    ],
    'gold' => [
        'text' => 'text-gold',
        'bg' => 'bg-yellow-900/30',
        'icon' => 'text-gold',
        'desc' => 'text-yellow-400'
    ],
    'green' => [
        'text' => 'text-green-500',
        'bg' => 'bg-green-900/30',
        'icon' => 'text-green-500',
        'desc' => 'text-green-400'
    ],
    'purple' => [
        'text' => 'text-purple-400',
        'bg' => 'bg-purple-900/30',
        'icon' => 'text-purple-500',
        'desc' => 'text-purple-400'
    ],
    'red' => [
        'text' => 'text-red-400',
        'bg' => 'bg-red-900/30',
        'icon' => 'text-red-500',
        'desc' => 'text-red-400'
    ],
];

$colors = $colorClasses[$color ?? 'blue'] ?? $colorClasses['blue'];
?>

<div class="bg-gray-900 border border-gray-800 p-6 rounded-lg">
    <div class="flex justify-between items-start mb-4">
        <div>
            <p class="text-gray-500 text-sm uppercase"><?php echo htmlspecialchars($title); ?></p>
            <h3 class="text-4xl <?php echo $colors['text']; ?> font-bold mt-1"><?php echo htmlspecialchars($value); ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full <?php echo $colors['bg']; ?> flex items-center justify-center <?php echo $colors['icon']; ?>">
            <i class="fas <?php echo $icon; ?> text-xl"></i>
        </div>
    </div>
    <?php if (!empty($description)): ?>
        <div class="text-xs <?php echo $colors['desc']; ?>"><?php echo htmlspecialchars($description); ?></div>
    <?php endif; ?>
</div>
