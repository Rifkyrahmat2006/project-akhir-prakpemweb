<?php
/**
 * Room Controller
 * Handles room display with artifacts, quizzes, and hidden items
 */

require_once __DIR__ . '/BaseController.php';

class RoomController extends BaseController {
    
    /**
     * Arrow position configurations per room
     */
    private $arrowPositions = [
        1 => ['prev' => ['bottom' => '20%', 'left' => '5%'], 'next' => ['bottom' => '20%', 'right' => '13%']],
        2 => ['prev' => ['bottom' => '40%', 'left' => '25%'], 'next' => ['bottom' => '20%', 'right' => '18%']],
        3 => ['prev' => ['bottom' => '20%', 'left' => '5%'], 'next' => ['bottom' => '24%', 'right' => '16%']],
        4 => ['prev' => ['bottom' => '20%', 'left' => '5%'], 'next' => ['bottom' => '20%', 'right' => '5%']],
    ];
    
    /**
     * Chest position configurations per room
     */
    private $chestPositions = [
        1 => ['top' => '63%', 'left' => '48%'],
        2 => ['top' => '68%', 'left' => '23%'],
        3 => ['top' => '63%', 'left' => '60%'],
        4 => ['top' => '80%', 'left' => '70%'],
    ];
    
    /**
     * Default artifact positions for fallback
     */
    private $fallbackPositions = [
        ['top' => '60%', 'left' => '20%'],
        ['top' => '70%', 'left' => '50%'],
        ['top' => '55%', 'left' => '80%'],
        ['top' => '40%', 'left' => '30%'],
        ['top' => '50%', 'left' => '60%'],
    ];
    
    /**
     * Display room with artifacts
     */
    public function show($id) {
        $this->requireAuth();
        
        $room_id = intval($id);
        $user_id = $this->userId();
        $user_level = $this->userLevel();
        
        // Fetch room from database
        $room = Room::findById($this->conn, $room_id);
        
        if (!$room) {
            $this->redirect('/lobby');
        }
        
        // Check access level
        if ($user_level < $room['min_level']) {
            $this->redirect('/lobby');
        }
        
        // Fetch artifacts with collection status
        $raw_artifacts = Room::getArtifacts($this->conn, $room_id, $user_id);
        $artifacts = $this->processArtifacts($raw_artifacts);
        
        // Calculate collection stats
        $collected_count = 0;
        foreach ($artifacts as $artifact) {
            if ($artifact['is_collected'] > 0) {
                $collected_count++;
            }
        }
        $total_artifacts = count($artifacts);
        $all_collected = ($collected_count > 0 && $collected_count >= $total_artifacts);
        
        // Hidden artifact data
        $hidden_artifact_unlocked = Room::isHiddenArtifactUnlocked($this->conn, $room_id, $user_id);
        $hidden_artifact = Room::getHiddenArtifact($this->conn, $room_id);
        if ($hidden_artifact) {
            $hidden_artifact['unlocked'] = $hidden_artifact_unlocked;
        }
        
        // Navigation (prev/next rooms)
        $prev_room = $this->getPreviousRoom($room_id, $user_level);
        $next_room = $this->getNextRoom($room_id, $user_level);
        
        // Get room-specific configurations
        $arrow_pos = $this->arrowPositions[$room_id] ?? 
            ['prev' => ['bottom' => '20%', 'left' => '5%'], 'next' => ['bottom' => '20%', 'right' => '5%']];
        $chest_pos = $this->chestPositions[$room_id] ?? ['top' => '65%', 'left' => '48%'];
        
        // Get room music
        $room_music = getRoomMusic($room['name']);
        
        // User data for XP bar updates
        $user = User::findById($this->conn, $user_id);
        
        // Determine if should show intro (skip if already collected any artifact in this room)
        $first_visit = ($collected_count == 0);
        
        $this->view('pages/lobby/room', [
            'room' => $room,
            'room_id' => $room_id,
            'artifacts' => $artifacts,
            'collected_count' => $collected_count,
            'total_artifacts' => $total_artifacts,
            'all_collected' => $all_collected,
            'hidden_artifact' => $hidden_artifact,
            'hidden_artifact_unlocked' => $hidden_artifact_unlocked,
            'prev_room' => $prev_room,
            'next_room' => $next_room,
            'arrow_pos' => $arrow_pos,
            'chest_pos' => $chest_pos,
            'room_music' => $room_music,
            'user' => $user,
            'user_level' => $user_level,
            'first_visit' => $first_visit,
            'show_arrows' => $hidden_artifact_unlocked  // Show arrows after hidden artifact collected
        ]);
    }
    
    /**
     * Process artifacts with positioning
     */
    private function processArtifacts($raw_artifacts) {
        $artifacts = [];
        $i = 0;
        
        foreach ($raw_artifacts as $row) {
            // Use DB positions or fallback
            if ($row['position_top'] && $row['position_left']) {
                $row['top'] = $row['position_top'];
                $row['left'] = $row['position_left'];
            } else {
                $pos = $this->fallbackPositions[$i % count($this->fallbackPositions)];
                $row['top'] = $pos['top'];
                $row['left'] = $pos['left'];
            }
            
            $artifacts[] = $row;
            $i++;
        }
        
        return $artifacts;
    }
    
    /**
     * Get previous accessible room
     */
    private function getPreviousRoom($room_id, $user_level) {
        if ($room_id <= 1) {
            return null;
        }
        
        $prev_room = Room::findById($this->conn, $room_id - 1);
        if ($prev_room && $user_level >= $prev_room['min_level']) {
            return $prev_room;
        }
        
        return null;
    }
    
    /**
     * Get next accessible room
     */
    private function getNextRoom($room_id, $user_level) {
        $next_room = Room::findById($this->conn, $room_id + 1);
        if ($next_room && $user_level >= $next_room['min_level']) {
            return $next_room;
        }
        
        return null;
    }
}
