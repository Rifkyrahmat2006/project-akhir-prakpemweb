<?php
/**
 * Lobby Controller
 * Handles lobby/room selection and collection pages
 */

require_once __DIR__ . '/BaseController.php';

class LobbyController extends BaseController {
    
    /**
     * Display lobby/room selection
     */
    public function index() {
        $this->requireAuth();
        
        $user_id = $this->userId();
        $user_level = $this->userLevel();
        
        // Fetch all rooms
        $rooms = Room::getAll($this->conn);
        
        // Add room status for each room
        foreach ($rooms as &$room) {
            $room['is_accessible'] = $user_level >= $room['min_level'];
            $room['completion'] = Room::getCompletionStatus($this->conn, $room['id'], $user_id);
        }
        
        // Fetch user data for XP bar
        $user = User::findById($this->conn, $user_id);
        $xpProgress = User::getXpProgressData($user['xp'], $user['level']);
        
        $this->view('pages/lobby/index', [
            'rooms' => $rooms,
            'user' => $user,
            'xpProgress' => $xpProgress,
            'user_level' => $user_level
        ]);
    }
    
    /**
     * Display user's collection
     */
    public function myCollection() {
        $this->requireAuth();
        
        $user_id = $this->userId();
        $user_level = $this->userLevel();
        
        // Get user data
        $user = User::findById($this->conn, $user_id);
        $xpProgress = User::getXpProgressData($user['xp'], $user['level']);
        
        // Get collected artifacts
        $collection = Artifact::getUserCollection($this->conn, $user_id);
        
        // Get hidden artifacts unlocked
        $hiddenArtifacts = Artifact::getUserHiddenArtifacts($this->conn, $user_id);
        
        $this->view('pages/lobby/my_collection', [
            'user' => $user,
            'xpProgress' => $xpProgress,
            'collection' => $collection,
            'hiddenArtifacts' => $hiddenArtifacts,
            'user_level' => $user_level
        ]);
    }
}
