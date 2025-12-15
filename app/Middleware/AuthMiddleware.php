<?php
/**
 * Authentication Middleware
 * Handles all authentication-related checks
 */

class AuthMiddleware {
    
    /**
     * Ensure user is logged in
     * Redirects to login page if not authenticated
     */
    public static function requireAuth($redirectUrl = '/project-akhir/public/login.php') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . $redirectUrl);
            exit();
        }
        
        return true;
    }
    
    /**
     * Ensure user is admin
     * Redirects to lobby if not admin
     */
    public static function requireAdmin($redirectUrl = '/project-akhir/public/lobby/') {
        self::requireAuth();
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: " . $redirectUrl);
            exit();
        }
        
        return true;
    }
    
    /**
     * Ensure user meets minimum level requirement
     */
    public static function requireLevel($minLevel, $redirectUrl = '/project-akhir/public/lobby/') {
        self::requireAuth();
        
        $userLevel = $_SESSION['level'] ?? 1;
        if ($userLevel < $minLevel) {
            header("Location: " . $redirectUrl);
            exit();
        }
        
        return true;
    }
    
    /**
     * Check if user is logged in (without redirect)
     */
    public static function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin (without redirect)
     */
    public static function isAdmin() {
        return self::isAuthenticated() && 
               isset($_SESSION['role']) && 
               $_SESSION['role'] === 'admin';
    }
    
    /**
     * Get current user ID or null
     */
    public static function getUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user level
     */
    public static function getUserLevel() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['level'] ?? 1;
    }
    
    /**
     * Redirect if already logged in (for login/register pages)
     */
    public static function redirectIfAuthenticated($redirectUrl = '/project-akhir/public/lobby/') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                header("Location: /project-akhir/admin/");
            } else {
                header("Location: " . $redirectUrl);
            }
            exit();
        }
    }
}
?>
