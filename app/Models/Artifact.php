<?php
/**
 * Artifact Model
 * Handles all artifact-related database operations
 */

class Artifact {
    
    /**
     * Get all artifacts
     */
    public static function getAll($conn) {
        $sql = "SELECT a.*, r.name as room_name FROM artifacts a 
                JOIN rooms r ON a.room_id = r.id ORDER BY a.room_id, a.id";
        $result = $conn->query($sql);
        $artifacts = [];
        while ($row = $result->fetch_assoc()) {
            $artifacts[] = $row;
        }
        return $artifacts;
    }
    
    /**
     * Get artifact by ID
     */
    public static function findById($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM artifacts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get artifacts by room ID
     */
    public static function getByRoomId($conn, $roomId) {
        $stmt = $conn->prepare("SELECT * FROM artifacts WHERE room_id = ? ORDER BY id");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $artifacts = [];
        while ($row = $result->fetch_assoc()) {
            $artifacts[] = $row;
        }
        return $artifacts;
    }
    
    /**
     * Create new artifact
     */
    public static function create($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO artifacts (room_id, name, description, image_url, xp_reward) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $data['room_id'], $data['name'], $data['description'], $data['image_url'], $data['xp_reward']);
        return $stmt->execute();
    }
    
    /**
     * Update artifact
     */
    public static function update($conn, $id, $data) {
        $stmt = $conn->prepare("UPDATE artifacts SET room_id = ?, name = ?, description = ?, image_url = ?, xp_reward = ? WHERE id = ?");
        $stmt->bind_param("isssii", $data['room_id'], $data['name'], $data['description'], $data['image_url'], $data['xp_reward'], $id);
        return $stmt->execute();
    }
    
    /**
     * Delete artifact
     */
    public static function delete($conn, $id) {
        // First delete from user_collections
        $conn->query("DELETE FROM user_collections WHERE artifact_id = $id");
        
        $stmt = $conn->prepare("DELETE FROM artifacts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Update artifact position
     */
    public static function updatePosition($conn, $id, $top, $left) {
        $stmt = $conn->prepare("UPDATE artifacts SET position_top = ?, position_left = ? WHERE id = ?");
        $stmt->bind_param("ssi", $top, $left, $id);
        return $stmt->execute();
    }
    
    /**
     * Collect artifact for user
     */
    public static function collect($conn, $userId, $artifactId) {
        // Check if already collected
        $stmt = $conn->prepare("SELECT id FROM user_collections WHERE user_id = ? AND artifact_id = ?");
        $stmt->bind_param("ii", $userId, $artifactId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Already collected'];
        }
        
        // Get artifact info
        $artifact = self::findById($conn, $artifactId);
        if (!$artifact) {
            return ['success' => false, 'message' => 'Artifact not found'];
        }
        
        // Insert collection record
        $stmt = $conn->prepare("INSERT INTO user_collections (user_id, artifact_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $artifactId);
        $stmt->execute();
        
        // Add XP - this returns xp_progress, rank_name, and other data
        require_once __DIR__ . '/User.php';
        $xpResult = User::addXp($conn, $userId, $artifact['xp_reward']);
        
        // Merge all XP data into response (includes xp_progress, rank_name)
        return array_merge([
            'success' => true,
            'artifact' => $artifact,
            'xp_earned' => $artifact['xp_reward']
        ], $xpResult);
    }
    
    /**
     * Get user's collected artifacts
     */
    public static function getUserCollection($conn, $userId) {
        $sql = "SELECT a.*, uc.collected_at 
                FROM user_collections uc 
                JOIN artifacts a ON uc.artifact_id = a.id 
                WHERE uc.user_id = ? 
                ORDER BY uc.collected_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $collection = [];
        while ($row = $result->fetch_assoc()) {
            $collection[] = $row;
        }
        return $collection;
    }
    
    /**
     * Get user's hidden artifacts
     */
    public static function getUserHiddenArtifacts($conn, $userId) {
        $sql = "SELECT uha.*, r.hidden_artifact_name, r.hidden_artifact_desc, 
                r.hidden_artifact_image, r.hidden_artifact_xp, r.name as room_name
                FROM user_hidden_artifacts uha
                JOIN rooms r ON uha.room_id = r.id
                WHERE uha.user_id = ?
                ORDER BY uha.unlocked_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $hidden = [];
        while ($row = $result->fetch_assoc()) {
            $hidden[] = $row;
        }
        return $hidden;
    }
}
