<?php
/**
 * Auth Controller
 * Handles authentication pages: login, register, logout
 */

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    
    /**
     * Display login page
     */
    public function showLogin() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirect('/lobby');
        }
        
        $error = $this->query('error');
        $success = $this->query('success');
        
        $this->view('pages/auth/login', [
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Display registration page
     */
    public function showRegister() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirect('/lobby');
        }
        
        $error = $this->query('error');
        
        $this->view('pages/auth/register', [
            'error' => $error
        ]);
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
