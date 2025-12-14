<?php
/**
 * User Model
 * Handles all user-related database operations
 */

class User {
    
    /**
     * Find user by ID
     */
    public static function findById($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Find user by email
     */
    public static function findByEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Create new user
     */
    public static function create($conn, $name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        return $stmt->execute();
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($user, $password) {
        return password_verify($password, $user['password']);
    }
    
    /**
     * Get user XP and calculate level
     */
    public static function getXpAndLevel($conn, $userId) {
        $user = self::findById($conn, $userId);
        if (!$user) return null;
        
        $xp = $user['xp'] ?? 0;
        $level = self::calculateLevel($xp);
        $xpForNextLevel = self::getXpForLevel($level + 1);
        $xpForCurrentLevel = self::getXpForLevel($level);
        $progress = $xpForNextLevel > $xpForCurrentLevel ? 
            (($xp - $xpForCurrentLevel) / ($xpForNextLevel - $xpForCurrentLevel)) * 100 : 100;
        
        return [
            'xp' => $xp,
            'level' => $level,
            'xp_for_next_level' => $xpForNextLevel,
            'xp_for_current_level' => $xpForCurrentLevel,
            'progress' => $progress
        ];
    }
    
    /**
     * Calculate level from XP
     */
    public static function calculateLevel($xp) {
        if ($xp >= 500) return 4;
        if ($xp >= 200) return 3;
        if ($xp >= 50) return 2;
        return 1;
    }
    
    /**
     * Get XP required for a level
     */
    public static function getXpForLevel($level) {
        $levels = [1 => 0, 2 => 50, 3 => 200, 4 => 500, 5 => 1000];
        return $levels[$level] ?? 1000;
    }
    
    /**
     * Get rank name from level
     */
    public static function getRankName($level) {
        $ranks = [
            1 => 'Novice Explorer',
            2 => 'Apprentice Historian',
            3 => 'Master Curator',
            4 => 'Royal Archivist'
        ];
        return $ranks[$level] ?? 'Novice Explorer';
    }
    
    /**
     * Add XP to user and check for level up
     */
    public static function addXp($conn, $userId, $amount) {
        $user = self::findById($conn, $userId);
        $oldLevel = self::calculateLevel($user['xp']);
        $newXp = $user['xp'] + $amount;
        $newLevel = self::calculateLevel($newXp);
        
        $stmt = $conn->prepare("UPDATE users SET xp = ? WHERE id = ?");
        $stmt->bind_param("ii", $newXp, $userId);
        $stmt->execute();
        
        // Get complete XP progress data for client
        $progressData = self::getXpProgressData($newXp, $newLevel);
        
        return array_merge([
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'new_xp' => $newXp,
            'leveled_up' => $newLevel > $oldLevel
        ], $progressData);
    }
    
    /**
     * Calculate XP progress data for client-side display
     * Centralizes all XP threshold and rank calculations
     */
    public static function getXpProgressData($xp, $level = null) {
        if ($level === null) {
            $level = self::calculateLevel($xp);
        }
        
        // XP thresholds for each level
        $xp_thresholds = [
            1 => ['min' => 0, 'max' => 50],
            2 => ['min' => 50, 'max' => 200],
            3 => ['min' => 200, 'max' => 500],
            4 => ['min' => 500, 'max' => 1000]
        ];
        
        // Rank names for each level
        $rank_names = [
            1 => 'Visitor',
            2 => 'Explorer',
            3 => 'Historian',
            4 => 'Royal Curator'
        ];
        
        $current_threshold = $xp_thresholds[$level] ?? $xp_thresholds[1];
        $xp_progress = 0;
        
        if ($level < 4) {
            $range = $current_threshold['max'] - $current_threshold['min'];
            $progress = $xp - $current_threshold['min'];
            $xp_progress = $range > 0 ? min(100, max(0, ($progress / $range) * 100)) : 0;
        } else {
            $xp_progress = 100;
        }
        
        return [
            'xp_progress' => round($xp_progress, 2),
            'rank_name' => $rank_names[$level] ?? 'Visitor',
            'xp_for_next' => $current_threshold['max'],
            'xp_for_current' => $current_threshold['min']
        ];
    }
    
    /**
     * Get all users (for admin)
     */
    public static function getAll($conn) {
        $result = $conn->query("SELECT id, name, email, xp, role, avatar, created_at FROM users ORDER BY created_at DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $row['level'] = self::calculateLevel($row['xp']);
            $row['rank'] = self::getRankName($row['level']);
            $users[] = $row;
        }
        return $users;
    }
    
    /**
     * Get user statistics
     */
    public static function getStats($conn, $userId) {
        // Collected artifacts count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_collections WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $collected = $stmt->get_result()->fetch_assoc()['count'];
        
        // Completed quizzes count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_quizzes WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $quizzes = $stmt->get_result()->fetch_assoc()['count'];
        
        // Hidden artifacts unlocked
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_hidden_artifacts WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $hidden = $stmt->get_result()->fetch_assoc()['count'];
        
        return [
            'artifacts_collected' => $collected,
            'quizzes_completed' => $quizzes,
            'hidden_artifacts' => $hidden
        ];
    }
    /**
     * Update user avatar
     */
    public static function updateAvatar($conn, $userId, $avatarPath) {
        $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $avatarPath, $userId);
        return $stmt->execute();
    }

    /**
     * Get avatar URL or default
     */
    public static function getAvatarUrl($user) {
        if (!empty($user['avatar'])) {
            return $user['avatar'];
        }
        // Generate initial avatar using UI Avatars service
        return 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? $user['username'] ?? 'User') . '&background=C5A059&color=000&size=128&font-size=0.5';
    }
    
    /**
     * Get dashboard statistics (for admin)
     */
    public static function getDashboardStats($conn) {
        $stats = [];
        
        // Total visitors
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'visitor'");
        $stats['total_visitors'] = $result->fetch_assoc()['count'];
        
        // Total artifacts
        $result = $conn->query("SELECT COUNT(*) as count FROM artifacts");
        $stats['total_artifacts'] = $result->fetch_assoc()['count'];
        
        // Total collections
        $result = $conn->query("SELECT COUNT(*) as count FROM user_collections");
        $stats['total_collections'] = $result->fetch_assoc()['count'];
        
        // Total quizzes answered
        $result = $conn->query("SELECT COUNT(*) as count FROM user_quizzes");
        $stats['total_quizzes_answered'] = $result->fetch_assoc()['count'];
        
        // Total hidden artifacts unlocked
        $result = $conn->query("SELECT COUNT(*) as count FROM user_hidden_artifacts");
        $stats['total_hidden_unlocked'] = $result->fetch_assoc()['count'];
        
        // Total rooms
        $result = $conn->query("SELECT COUNT(*) as count FROM rooms");
        $stats['total_rooms'] = $result->fetch_assoc()['count'];
        
        return $stats;
    }
}
