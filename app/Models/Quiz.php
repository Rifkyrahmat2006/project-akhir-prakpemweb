<?php
/**
 * Quiz Model
 * Handles all quiz-related database operations
 */

class Quiz {
    
    /**
     * Get all quizzes with room info
     */
    public static function getAll($conn) {
        $sql = "SELECT q.*, r.name as room_name FROM quizzes q 
                JOIN rooms r ON q.room_id = r.id ORDER BY q.room_id, q.id";
        $result = $conn->query($sql);
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
        return $quizzes;
    }
    
    /**
     * Get quiz by ID
     */
    public static function findById($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get quizzes by room ID
     */
    public static function getByRoomId($conn, $roomId) {
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE room_id = ? ORDER BY id");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
        return $quizzes;
    }
    
    /**
     * Get quizzes for user (with answered status)
     */
    public static function getForUser($conn, $roomId, $userId) {
        $sql = "SELECT q.*, 
                (SELECT COUNT(*) FROM user_quizzes WHERE user_id = ? AND quiz_id = q.id) as is_answered,
                (SELECT is_correct FROM user_quizzes WHERE user_id = ? AND quiz_id = q.id LIMIT 1) as user_is_correct
                FROM quizzes q WHERE q.room_id = ? ORDER BY q.id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $userId, $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
        return $quizzes;
    }
    
    /**
     * Create new quiz
     */
    public static function create($conn, $data) {
        $stmt = $conn->prepare("INSERT INTO quizzes (room_id, question, option_a, option_b, option_c, correct_option, xp_reward) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $data['room_id'], $data['question'], $data['option_a'], $data['option_b'], $data['option_c'], $data['correct_option'], $data['xp_reward']);
        return $stmt->execute();
    }
    
    /**
     * Update quiz
     */
    public static function update($conn, $id, $data) {
        $stmt = $conn->prepare("UPDATE quizzes SET room_id = ?, question = ?, option_a = ?, option_b = ?, option_c = ?, correct_option = ?, xp_reward = ? WHERE id = ?");
        $stmt->bind_param("isssssii", $data['room_id'], $data['question'], $data['option_a'], $data['option_b'], $data['option_c'], $data['correct_option'], $data['xp_reward'], $id);
        return $stmt->execute();
    }
    
    /**
     * Delete quiz
     */
    public static function delete($conn, $id) {
        // First delete user answers
        $conn->query("DELETE FROM user_quizzes WHERE quiz_id = $id");
        
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Submit quiz answer
     */
    public static function submit($conn, $userId, $quizId, $answer) {
        // Check if already answered
        $stmt = $conn->prepare("SELECT id FROM user_quizzes WHERE user_id = ? AND quiz_id = ?");
        $stmt->bind_param("ii", $userId, $quizId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Already answered'];
        }
        
        // Get quiz info
        $quiz = self::findById($conn, $quizId);
        if (!$quiz) {
            return ['success' => false, 'message' => 'Quiz not found'];
        }
        
        // Check answer
        $isCorrect = (strtolower($answer) === strtolower($quiz['correct_option'])) ? 1 : 0;
        
        // Insert answer record (without storing the actual answer, just the result)
        $stmt = $conn->prepare("INSERT INTO user_quizzes (user_id, quiz_id, is_correct) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $quizId, $isCorrect);
        $stmt->execute();
        
        // Add XP if correct
        $xpEarned = 0;
        $xpResult = null;
        if ($isCorrect) {
            require_once __DIR__ . '/User.php';
            $xpResult = User::addXp($conn, $userId, $quiz['xp_reward']);
            $xpEarned = $quiz['xp_reward'];
        }
        
        return [
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $quiz['correct_option'],
            'xp_earned' => $xpEarned,
            'new_xp' => $xpResult ? $xpResult['new_xp'] : null,
            'new_level' => $xpResult ? $xpResult['new_level'] : null,
            'leveled_up' => $xpResult ? $xpResult['leveled_up'] : false
        ];
    }
    
    /**
     * Check if user qualifies for hidden artifact unlock
     * Requirements: All quizzes attempted AND 50%+ correct
     */
    public static function checkHiddenArtifactUnlock($conn, $roomId, $userId) {
        // Get total quizzes in room
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM quizzes WHERE room_id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];
        
        if ($total == 0) return false;
        
        // Get user's quiz attempts in this room
        $sql = "SELECT COUNT(*) as attempted, SUM(is_correct) as correct 
                FROM user_quizzes uq 
                JOIN quizzes q ON uq.quiz_id = q.id 
                WHERE q.room_id = ? AND uq.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $roomId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $attempted = $result['attempted'];
        $correct = $result['correct'] ?? 0;
        
        // Check conditions: all attempted AND 50%+ correct
        if ($attempted >= $total && ($correct / $total) >= 0.5) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get quiz count by room
     */
    public static function getCountByRoom($conn, $roomId) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM quizzes WHERE room_id = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    /**
     * Get user quiz stats for a room
     */
    public static function getUserRoomStats($conn, $roomId, $userId) {
        $total = self::getCountByRoom($conn, $roomId);
        
        $sql = "SELECT COUNT(*) as attempted, COALESCE(SUM(is_correct), 0) as correct 
                FROM user_quizzes uq 
                JOIN quizzes q ON uq.quiz_id = q.id 
                WHERE q.room_id = ? AND uq.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $roomId, $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return [
            'total' => $total,
            'attempted' => $result['attempted'],
            'correct' => $result['correct'],
            'percentage' => $total > 0 ? ($result['correct'] / $total) * 100 : 0
        ];
    }
}
