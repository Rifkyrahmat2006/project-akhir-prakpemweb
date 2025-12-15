<?php
/**
 * Alert Component
 * Reusable alert/notification messages
 * 
 * @param string $message - Alert message
 * @param string $type - Alert type (success, error, warning, info)
 * @param bool $dismissible - Whether alert can be dismissed
 */

$typeConfig = [
    'success' => [
        'bg' => 'bg-green-500/10',
        'border' => 'border-green-500/50',
        'text' => 'text-green-500',
        'icon' => 'fa-check-circle'
    ],
    'error' => [
        'bg' => 'bg-red-500/10',
        'border' => 'border-red-500/50',
        'text' => 'text-red-500',
        'icon' => 'fa-exclamation-circle'
    ],
    'warning' => [
        'bg' => 'bg-yellow-500/10',
        'border' => 'border-yellow-500/50',
        'text' => 'text-yellow-500',
        'icon' => 'fa-exclamation-triangle'
    ],
    'info' => [
        'bg' => 'bg-blue-500/10',
        'border' => 'border-blue-500/50',
        'text' => 'text-blue-500',
        'icon' => 'fa-info-circle'
    ],
];

$config = $typeConfig[$type ?? 'info'] ?? $typeConfig['info'];
$alertId = 'alert-' . uniqid();
?>

<div id="<?php echo $alertId; ?>" class="<?php echo $config['bg']; ?> border <?php echo $config['border']; ?> <?php echo $config['text']; ?> p-4 rounded mb-4 flex items-center justify-between">
    <div class="flex items-center">
        <i class="fas <?php echo $config['icon']; ?> mr-3"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php if ($dismissible ?? true): ?>
        <button onclick="document.getElementById('<?php echo $alertId; ?>').remove()" class="<?php echo $config['text']; ?> hover:opacity-70 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
    <?php endif; ?>
</div>
