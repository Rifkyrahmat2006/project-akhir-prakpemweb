<?php
/**
 * Room Model
 * Handles all room-related database operations
 */

class Room {
    
    /**
     * Get all rooms
     */
    public static function getAll($conn) {
        $result = $conn->query("SELECT * FROM rooms ORDER BY id");
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        return $rooms;
    }
    
    /**
     * Get room by ID
     */
    public static function findById($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get room with artifact count and user progress
     */
    public static function getWithProgress($conn, $userId) {
        $sql = "SELECT r.*, 
                (SELECT COUNT(*) FROM artifacts WHERE room_id = r.id) as total_artifacts,
                (SELECT COUNT(*) FROM user_collections uc 
                 JOIN artifacts a ON uc.artifact_id = a.id 
                 WHERE a.room_id = r.id AND uc.user_id = ?) as collected_artifacts
                FROM rooms r ORDER BY r.id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $row['progress'] = $row['total_artifacts'] > 0 ? 
                ($row['collected_artifacts'] / $row['total_artifacts']) * 100 : 0;
            $rooms[] = $row;
        }
        return $rooms;
    }
    
    /**
     * Get artifacts in a room with user collection status
     */
    public static function getArtifacts($conn, $roomId, $userId) {
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM user_collections WHERE user_id = ? AND artifact_id = a.id) as is_collected
                FROM artifacts a WHERE a.room_id = ? ORDER BY a.id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $artifacts = [];
        while ($row = $result->fetch_assoc()) {
            $artifacts[] = $row;
        }
        return $artifacts;
    }
    
    /**
     * Check if all artifacts in room are collected
     */
    public static function allArtifactsCollected($conn, $roomId, $userId) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM artifacts WHERE room_id = ?) as total,
                (SELECT COUNT(*) FROM user_collections uc 
                 JOIN artifacts a ON uc.artifact_id = a.id 
                 WHERE a.room_id = ? AND uc.user_id = ?) as collected";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $roomId, $roomId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['total'] > 0 && $result['collected'] >= $result['total'];
    }
    
    /**
     * Get hidden artifact info for a room
     */
    public static function getHiddenArtifact($conn, $roomId) {
        $room = self::findById($conn, $roomId);
        if (!$room || !$room['hidden_artifact_name']) {
            return null;
        }
        return [
            'name' => $room['hidden_artifact_name'],
            'description' => $room['hidden_artifact_desc'],
            'image' => $room['hidden_artifact_image'],
            'xp' => $room['hidden_artifact_xp']
        ];
    }
    
    /**
     * Check if user has unlocked hidden artifact
     */
    public static function isHiddenArtifactUnlocked($conn, $roomId, $userId) {
        $stmt = $conn->prepare("SELECT id FROM user_hidden_artifacts WHERE user_id = ? AND room_id = ?");
        $stmt->bind_param("ii", $userId, $roomId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Unlock hidden artifact for user
     */
    public static function unlockHiddenArtifact($conn, $roomId, $userId) {
        $stmt = $conn->prepare("INSERT IGNORE INTO user_hidden_artifacts (user_id, room_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $roomId);
        return $stmt->execute();
    }
    
    /**
     * Get professor dialogs for a room
     */
    public static function getDialogs($conn, $roomId) {
        $room = self::findById($conn, $roomId);
        if ($room && $room['professor_dialogs']) {
            return json_decode($room['professor_dialogs'], true);
        }
        return null;
    }
    
    /**
     * Update professor dialogs
     */
    public static function updateDialogs($conn, $roomId, $dialogs) {
        $json = json_encode($dialogs);
        $stmt = $conn->prepare("UPDATE rooms SET professor_dialogs = ? WHERE id = ?");
        $stmt->bind_param("si", $json, $roomId);
        return $stmt->execute();
    }
    
    /**
     * Update hidden artifact info
     */
    public static function updateHiddenArtifact($conn, $roomId, $name, $desc, $image, $xp) {
        $stmt = $conn->prepare("UPDATE rooms SET hidden_artifact_name = ?, hidden_artifact_desc = ?, hidden_artifact_image = ?, hidden_artifact_xp = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $desc, $image, $xp, $roomId);
        return $stmt->execute();
    }
    
    /**
     * Get total artifact count in a room
     */
    public static function getTotalArtifactCount($conn, $roomId) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM artifacts WHERE room_id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
    
    /**
     * Get collected artifact count for a user in a room
     */
    public static function getCollectedArtifactCount($conn, $roomId, $userId) {
        $stmt = $conn->prepare("SELECT COUNT(*) as collected FROM user_collections uc JOIN artifacts a ON uc.artifact_id = a.id WHERE a.room_id = ? AND uc.user_id = ?");
        $stmt->bind_param("ii", $roomId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['collected'] ?? 0;
    }
}
