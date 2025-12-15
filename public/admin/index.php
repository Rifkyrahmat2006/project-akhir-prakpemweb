<?php
/**
 * Admin Dashboard
 * Uses Middleware for authentication and View components
 */

// Load bootstrap
require_once __DIR__ . '/../../app/bootstrap.php';

// Require admin access
requireAdmin();

// Fetch Stats using Model
$stats = User::getDashboardStats($conn);

include __DIR__ . '/../header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar Component -->
    <?php adminSidebar('dashboard'); ?>

    <!-- Main Content -->
    <main class="flex-grow p-8 overflow-y-auto">
        <h2 class="text-3xl text-white font-serif mb-8">Dashboard Overview</h2>

        <!-- Stats Grid using Components -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <?php statCard('Total Visitors', $stats['total_visitors'], 'fa-users', 'blue', 'Registered explorers'); ?>
            <?php statCard('Artifacts', $stats['total_artifacts'], 'fa-gem', 'gold', 'Items in exhibition'); ?>
            <?php statCard('Collections', $stats['total_collections'], 'fa-hand-holding', 'green', 'Items found by users'); ?>
        </div>
        
        <!-- Additional Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <?php statCard('Quizzes Answered', $stats['total_quizzes_answered'], 'fa-question-circle', 'purple', 'Total quiz completions'); ?>
            <?php statCard('Hidden Artifacts', $stats['total_hidden_unlocked'], 'fa-key', 'gold', 'Secret items unlocked'); ?>
            <?php statCard('Rooms', $stats['total_rooms'], 'fa-door-open', 'blue', 'Exhibition rooms'); ?>
        </div>

        <!-- Recent Activity -->
        <div class="border border-gold/10 rounded-lg overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 border-b border-gold/10">
                <h3 class="text-white font-bold">Admin Notices</h3>
            </div>
            <div class="p-6 bg-black/50">
                <p class="text-gray-400">Welcome to the VesperaVeloria curator panel. From here you can manage the museum collection and monitor visitor progress.</p>
                <div class="mt-4">
                    <?php alertBox('Tip: Use the "Manage Artifacts" page to add new items to rooms. Ensure you have the image URL ready.', 'info', false); ?>
                </div>
            </div>
        </div>
    </main>
</div>
