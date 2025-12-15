<?php
/**
 * View Helper Functions
 * Provides functions to load views, partials, and components
 */

class View {
    
    private static $viewPath;
    private static $data = [];
    
    /**
     * Initialize the view path
     */
    public static function init() {
        self::$viewPath = defined('BASE_PATH') ? BASE_PATH . '/views' : dirname(__DIR__, 2) . '/views';
    }
    
    /**
     * Render a view file with data
     */
    public static function render($view, $data = []) {
        self::init();
        $filePath = self::$viewPath . '/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($filePath)) {
            throw new Exception("View not found: {$view}");
        }
        
        // Extract data to local variables
        extract($data);
        
        // Start output buffering and include the view
        ob_start();
        include $filePath;
        return ob_get_clean();
    }
    
    /**
     * Include a partial (similar to render but echoes directly)
     */
    public static function partial($partial, $data = []) {
        echo self::render('partials.' . $partial, $data);
    }
    
    /**
     * Include a component
     */
    public static function component($component, $data = []) {
        echo self::render('components.' . $component, $data);
    }
    
    /**
     * Include admin sidebar
     */
    public static function adminSidebar($activePage = 'dashboard') {
        self::component('admin_sidebar', ['active' => $activePage]);
    }
    
    /**
     * Include a stat card
     */
    public static function statCard($title, $value, $icon, $color = 'blue', $description = '') {
        self::component('stat_card', [
            'title' => $title,
            'value' => $value,
            'icon' => $icon,
            'color' => $color,
            'description' => $description
        ]);
    }
    
    /**
     * Include a modal
     */
    public static function modal($id, $title, $content = '', $size = 'md') {
        self::component('modal', [
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'size' => $size
        ]);
    }
    
    /**
     * Include a button
     */
    public static function button($text, $type = 'primary', $attrs = []) {
        self::component('button', [
            'text' => $text,
            'type' => $type,
            'attrs' => $attrs
        ]);
    }
}
?>
