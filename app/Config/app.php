<?php
/**
 * Application Configuration
 * Centralized configuration for app settings
 */

return [
    /**
     * Room Background Music Mapping
     * Maps room names to their background music files
     * Files should be placed in /public/assets/music/
     */
    'room_music' => [
        'Medieval Hall' => 'medieval.mp3',
        'Renaissance Gallery' => 'Renaissance.mp3',
        'Baroque Palace' => 'baroque.mp3',
        'Royal Archives' => 'archive.mp3',
        // Add more rooms as needed
    ],
    
    /**
     * Default Music
     * Fallback music when room doesn't have specific mapping
     */
    'default_music' => 'lobby.mp3',
    
    /**
     * XP Thresholds for Levels
     * Maps level number to XP required
     */
    'xp_levels' => [
        1 => 0,
        2 => 50,
        3 => 200,
        4 => 500,
        5 => 1000
    ],
    
    /**
     * Rank Names for Levels
     */
    'rank_names' => [
        1 => 'Visitor',
        2 => 'Explorer',
        3 => 'Historian',
        4 => 'Royal Curator'
    ],
    
    /**
     * XP Thresholds for Progress Bar
     */
    'xp_thresholds' => [
        1 => ['min' => 0, 'max' => 50],
        2 => ['min' => 50, 'max' => 200],
        3 => ['min' => 200, 'max' => 500],
        4 => ['min' => 500, 'max' => 1000]
    ],
    
    /**
     * Quiz Settings
     */
    'quiz' => [
        'hidden_artifact_unlock_percent' => 50, // Percentage of correct answers to unlock hidden artifact
    ],
    
    /**
     * Assets Paths
     */
    'assets' => [
        'music_path' => '/project-akhir/public/assets/music/',
        'images_path' => '/project-akhir/public/assets/img/',
    ]
];
