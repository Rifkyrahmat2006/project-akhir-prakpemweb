<?php
/**
 * Base Controller
 * Parent class for all controllers
 */

class BaseController {
    
    protected $conn;
    
    /**
     * Constructor - initialize database connection
     */
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Render a view file
     * 
     * @param string $view View path relative to views/ (e.g., 'pages/auth/login')
     * @param array $data Data to pass to the view
     * @param string $layout Layout to use (null for no layout)
     */
    protected function view($view, $data = [], $layout = null) {
        // Extract data to local variables for the view
        extract($data);
        
        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new Exception("View not found: {$view}");
        }
        
        // Start output buffering
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        // If layout specified, wrap content in layout
        if ($layout) {
            $layoutPath = BASE_PATH . '/views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    /**
     * Redirect to another URL
     */
    protected function redirect($path) {
        header("Location: " . Router::url($path));
        exit();
    }
    
    /**
     * Redirect to external/full URL
     */
    protected function redirectTo($url) {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Send JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Get POST data
     */
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    /**
     * Get query parameter
     */
    protected function query($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Check if request is POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Get current user ID
     */
    protected function userId() {
        return userId();
    }
    
    /**
     * Get current user level
     */
    protected function userLevel() {
        return userLevel();
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Require admin role
     */
    protected function requireAdmin() {
        requireAdmin();
    }
}
