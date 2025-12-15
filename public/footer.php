    <footer class="bg-dark-bg border-t border-gray-800 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <div class="mb-4">
                <img src="<?php echo BASE_URL; ?>/icon.png" alt="Museum Logo" class="h-8 w-8 mx-auto mb-2">
                <span class="font-serif text-lg text-gray-400">Classic Old Europe Museum</span>
            </div>
            <p class="text-gray-600 text-sm">&copy; <?php echo date('Y'); ?> Museum Interactive Experience. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    
    <!-- Background Music Control Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bgMusic = document.getElementById('bg-music');
            
            if (bgMusic) {
                // Check if we're on a room page (room.php)
                const isRoomPage = window.location.pathname.includes('room.php');
                
                // Only play lobby music if NOT on a room page
                if (!isRoomPage) {
                    // Set volume to 30% for subtle background music
                    bgMusic.volume = 0.3;
                    
                    // Try to play the music
                    const playPromise = bgMusic.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            // Music started playing successfully
                            console.log('Lobby music started');
                        }).catch(error => {
                            // Autoplay was prevented
                            console.log('Autoplay prevented. User interaction required:', error);
                            
                            // Add click listener to start music on first user interaction
                            document.body.addEventListener('click', function startMusic() {
                                bgMusic.play().then(() => {
                                    console.log('Lobby music started after user interaction');
                                }).catch(e => console.log('Music play failed:', e));
                                
                                // Remove listener after first click
                                document.body.removeEventListener('click', startMusic);
                            }, { once: true });
                        });
                    }
                }
            }
        });
    </script>
</body>
</html>
