<?php
/**
 * Router Entry Point
 * Front controller that handles all routed requests
 */

// Load bootstrap
require_once '../app/bootstrap.php';

// Load routes and dispatch
$router = require_once APP_PATH . '/routes.php';
$router->dispatch();
