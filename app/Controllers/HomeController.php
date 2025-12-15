<?php
/**
 * Home Controller
 * Handles landing/home page
 */

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    
    /**
     * Display home/landing page
     */
    public function index() {
        // If logged in, redirect to lobby
        if (isLoggedIn()) {
            $this->redirect('/lobby');
        }
        
        $this->view('pages/home/index');
    }
}
