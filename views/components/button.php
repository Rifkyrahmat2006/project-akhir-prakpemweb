<?php
/**
 * Button Component
 * Reusable button with various styles
 * 
 * @param string $text - Button text
 * @param string $type - Button type (primary, secondary, danger, success, outline)
 * @param array $attrs - Additional HTML attributes
 */

$typeClasses = [
    'primary' => 'bg-gold hover:bg-gold-hover text-black font-bold',
    'secondary' => 'bg-gray-700 hover:bg-gray-600 text-white',
    'danger' => 'bg-red-600 hover:bg-red-500 text-white',
    'success' => 'bg-green-600 hover:bg-green-500 text-white',
    'outline' => 'bg-transparent border-2 border-gold text-gold hover:bg-gold hover:text-black',
    'ghost' => 'bg-transparent text-gray-400 hover:text-white hover:bg-gray-800',
];

$btnClass = $typeClasses[$type ?? 'primary'] ?? $typeClasses['primary'];

// Build attributes string
$attrString = '';
foreach ($attrs ?? [] as $key => $value) {
    $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
}
?>

<button class="<?php echo $btnClass; ?> py-2 px-4 rounded transition-all duration-200"<?php echo $attrString; ?>>
    <?php echo htmlspecialchars($text); ?>
</button>
