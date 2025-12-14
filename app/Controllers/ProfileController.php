<?php
/**
 * Profile Controller
 * Handles profile and settings pages
 */

require_once __DIR__ . '/BaseController.php';

class ProfileController extends BaseController {
    
    /**
     * Display user profile
     */
    public function show() {
        $this->requireAuth();
        
        $user_id = $this->userId();
        
        // Fetch user data
        $user = User::findById($this->conn, $user_id);
        if (!$user) {
            $this->redirect('/login');
        }
        
        // Fetch user statistics
        $stats = User::getStats($this->conn, $user_id);
        
        // Get totals for progress bars
        $total_artifacts = $this->conn->query("SELECT COUNT(*) as count FROM artifacts")->fetch_assoc()['count'];
        $total_quizzes = $this->conn->query("SELECT COUNT(*) as count FROM quizzes")->fetch_assoc()['count'];
        $stats['total_artifacts'] = $total_artifacts;
        $stats['total_quizzes'] = $total_quizzes;
        
        // Get XP progress data
        $xpProgress = User::getXpProgressData($user['xp'], $user['level']);
        
        $this->view('pages/profile/index', [
            'user' => $user,
            'stats' => $stats,
            'xpProgress' => $xpProgress
        ]);
    }
    
    /**
     * Display settings page
     */
    public function settings() {
        $this->requireAuth();
        
        $user_id = $this->userId();
        
        // Fetch user data
        $user = User::findById($this->conn, $user_id);
        if (!$user) {
            $this->redirect('/login');
        }
        
        $success = $this->query('success');
        $error = $this->query('error');
        
        $this->view('pages/profile/settings', [
            'user' => $user,
            'success' => $success,
            'error' => $error
        ]);
    }
}
