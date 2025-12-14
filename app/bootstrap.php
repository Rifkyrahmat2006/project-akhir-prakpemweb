<?php
/**
 * Bootstrap / Autoloader
 * Central file to load all middlewares and common dependencies
 * Include this file at the top of any page that needs middleware
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);

// Autoload Middleware classes
require_once APP_PATH . '/Middleware/AuthMiddleware.php';
require_once APP_PATH . '/Middleware/CsrfMiddleware.php';

// Autoload Models
require_once APP_PATH . '/Models/User.php';
require_once APP_PATH . '/Models/Room.php';
require_once APP_PATH . '/Models/Artifact.php';
require_once APP_PATH . '/Models/Quiz.php';

// Database connection
require_once APP_PATH . '/Config/database.php';

// Load Router
require_once APP_PATH . '/Core/Router.php';

// Load App Configuration
$GLOBALS['app_config'] = require APP_PATH . '/Config/app.php';

/**
 * Get configuration value
 * @param string $key Dot notation key (e.g., 'room_music.Medieval Hall')
 * @param mixed $default Default value if key not found
 */
function config($key, $default = null) {
    $config = $GLOBALS['app_config'];
    $keys = explode('.', $key);
    
    foreach ($keys as $k) {
        if (!isset($config[$k])) {
            return $default;
        }
        $config = $config[$k];
    }
    
    return $config;
}

/**
 * Get room music file
 */
function getRoomMusic($roomName) {
    $musicMap = config('room_music', []);
    return $musicMap[$roomName] ?? config('default_music', 'lobby.mp3');
}

/**
 * Helper function shortcuts for cleaner code
 */

// Auth helpers
function requireAuth($redirect = '/project-akhir/public/login.php') {
    return AuthMiddleware::requireAuth($redirect);
}

function requireAdmin($redirect = '/project-akhir/public/lobby/') {
    return AuthMiddleware::requireAdmin($redirect);
}

function requireLevel($level, $redirect = '/project-akhir/public/lobby/') {
    return AuthMiddleware::requireLevel($level, $redirect);
}

function isLoggedIn() {
    return AuthMiddleware::isAuthenticated();
}

function isAdmin() {
    return AuthMiddleware::isAdmin();
}

function userId() {
    return AuthMiddleware::getUserId();
}

function userLevel() {
    return AuthMiddleware::getUserLevel();
}

// CSRF helpers
function csrfField() {
    return CsrfMiddleware::tokenField();
}

function csrfToken() {
    return CsrfMiddleware::generateToken();
}

function verifyCsrf() {
    return CsrfMiddleware::verifyToken();
}

function requireCsrf() {
    return CsrfMiddleware::requireToken();
}

// Autoload View Helper
require_once APP_PATH . '/Helpers/View.php';

// View helpers
function view($view, $data = []) {
    return View::render($view, $data);
}

function partial($partial, $data = []) {
    View::partial($partial, $data);
}

function component($component, $data = []) {
    View::component($component, $data);
}

function adminSidebar($active = 'dashboard') {
    View::adminSidebar($active);
}

function statCard($title, $value, $icon, $color = 'blue', $description = '') {
    View::statCard($title, $value, $icon, $color, $description);
}

function alertBox($message, $type = 'info', $dismissible = true) {
    View::component('alert', [
        'message' => $message,
        'type' => $type,
        'dismissible' => $dismissible
    ]);
}

function roomCard($room, $linkTo, $countLabel, $count) {
    View::component('room_card', [
        'room' => $room,
        'linkTo' => $linkTo,
        'countLabel' => $countLabel,
        'count' => $count
    ]);
}
?>
