<?php
/**
 * Routes Configuration
 * Define all application routes here
 */

// Require the router class
require_once APP_PATH . '/Core/Router.php';

$router = new Router();

/**
 * Public Routes (No auth required)
 */

// Home/Landing
$router->get('/', 'HomeController@index');

// Authentication
$router->get('/login', 'AuthController@showLogin');
$router->get('/register', 'AuthController@showRegister');
$router->get('/logout', 'AuthController@logout');

/**
 * Protected Routes (Auth required)
 */

// Profile
$router->get('/profile', 'ProfileController@show');
$router->get('/settings', 'ProfileController@settings');

// Lobby
$router->get('/lobby', 'LobbyController@index');
$router->get('/lobby/room/{id}', 'RoomController@show');
$router->get('/lobby/my-collection', 'LobbyController@myCollection');

// Quiz (keeping existing structure for now)
$router->get('/lobby/quiz', 'QuizController@show');
$router->get('/lobby/quiz-answer', 'QuizController@answer');

/**
 * 404 Not Found Handler
 */
$router->notFound(function() {
    http_response_code(404);
    include BASE_PATH . '/views/errors/404.php';
});

return $router;
