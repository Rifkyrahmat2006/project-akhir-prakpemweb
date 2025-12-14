<?php
// Secure page
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database and Models
require_once '../../app/Config/database.php';
require_once '../../app/Models/Room.php';

$user_level = $_SESSION['level'] ?? 1;

// Get rooms using Model
$all_rooms = Room::getAll($conn);
$rooms = [];
foreach ($all_rooms as $room) {
    $room['is_locked'] = ($user_level < $room['min_level']);
    $rooms[] = $room;
}

// Door images in order: Medieval, Renaissance, Baroque, Archive
$door_images = [
    '/project-akhir/public/assets/img/medievaldoor.png',
    '/project-akhir/public/assets/img/renaissancedoor.png',
    '/project-akhir/public/assets/img/baroquedoor.png',
    '/project-akhir/public/assets/img/archivedoor.png',
];

// Door positions - uniform size, same bottom alignment, slightly smaller
$door_positions = [
    ['left' => '20%', 'bottom' => '5%', 'height' => '60%'],
    ['left' => '40%', 'bottom' => '8%', 'height' => '50%'],
    ['left' => '60%', 'bottom' => '8%', 'height' => '55%'],
    ['left' => '80%', 'bottom' => '8%', 'height' => '50%'],
];

include '../header.php';
include '../navbar.php';
?>

<!-- Transition Overlay -->
<div id="transition-overlay" class="fixed inset-0 z-[200] pointer-events-none opacity-0 bg-black transition-opacity duration-700"></div>

<!-- Lobby Container -->
<div class="relative w-full h-[calc(100vh-64px)] overflow-hidden bg-black">
    <!-- Background Image - roomchoice.png -->
    <img src="/project-akhir/public/assets/img/roomchoice.png" 
         alt="Museum Lobby" 
         class="w-full h-full object-cover object-center"
         id="lobby-bg">
    
    <!-- Door Images - No containers, natural placement -->
    <?php for ($i = 0; $i < 4 && $i < count($rooms); $i++): ?>
        <?php 
        $room = $rooms[$i];
        $door_img = $door_images[$i];
        $pos = $door_positions[$i];
        ?>
        
        <div class="door-item absolute cursor-pointer transition-all duration-300"
             style="left: <?php echo $pos['left']; ?>; bottom: <?php echo $pos['bottom']; ?>; height: <?php echo $pos['height']; ?>; transform: translateX(-50%);"
             data-room-id="<?php echo $room['id']; ?>"
             data-locked="<?php echo $room['is_locked'] ? 'true' : 'false'; ?>"
             data-room-name="<?php echo htmlspecialchars($room['name']); ?>"
             data-min-level="<?php echo $room['min_level']; ?>">
            
            <!-- Door Image -->
            <img src="<?php echo $door_img; ?>" 
                 alt="<?php echo htmlspecialchars($room['name']); ?>"
                 class="h-full w-auto object-contain mx-auto transition-all duration-300 door-image"
                 style="filter: brightness(0.9);">
            
            <!-- Lock Badge (appears over door) -->
            <?php if ($room['is_locked']): ?>
            <div class="absolute top-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center z-10 pointer-events-none">
                <div class="bg-black/85 border-2 border-gray-500 rounded-full p-4 mb-2 shadow-2xl">
                    <i class="fas fa-lock text-3xl text-gray-400"></i>
                </div>
                <div class="bg-gold px-4 py-1 rounded-full shadow-lg">
                    <span class="text-black text-xs font-bold">Lvl <?php echo $room['min_level']; ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Room Name Tooltip -->
            <div class="door-tooltip absolute -top-24 left-1/2 transform -translate-x-1/2 opacity-0 transition-all duration-300 pointer-events-none z-50 whitespace-nowrap">
                <div class="bg-gradient-to-b from-amber-900/95 to-amber-950/95 border-2 border-gold px-6 py-3 rounded-lg shadow-2xl">
                    <span class="text-gold font-serif text-lg tracking-wide"><?php echo htmlspecialchars($room['name']); ?></span>
                    <?php if (!$room['is_locked']): ?>
                    <span class="text-amber-200 text-xs block text-center mt-1">Click to enter</span>
                    <?php endif; ?>
                </div>
                <!-- Tooltip Arrow -->
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-amber-950 border-r-2 border-b-2 border-gold rotate-45"></div>
            </div>
        </div>
    <?php endfor; ?>
</div>

<style>
/* Door hover effects */
.door-item:hover .door-image {
    filter: brightness(1.3) drop-shadow(0 0 30px rgba(212, 175, 55, 0.9));
    transform: scale(1.08);
}

.door-item:hover .door-tooltip {
    opacity: 1;
    transform: translate(-50%, -10px);
}

.door-item:not([data-locked="true"]):hover {
    z-index: 50;
}

/* Locked doors have reduced hover effect */
.door-item[data-locked="true"]:hover .door-image {
    filter: brightness(0.95);
    transform: scale(1.02);
}

/* Zoom effect for transition */
@keyframes zoomFade {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

.zoom-fade-out {
    animation: zoomFade 1s ease-in forwards;
}

/* Pulse animation for unlocked doors */
@keyframes subtlePulse {
    0%, 100% {
        filter: brightness(0.85);
    }
    50% {
        filter: brightness(0.95);
    }
}

.door-item:not([data-locked="true"]) .door-image {
    animation: subtlePulse 4s ease-in-out infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const doors = document.querySelectorAll('.door-item');
    const overlay = document.getElementById('transition-overlay');
    const lobbyBg = document.getElementById('lobby-bg');
    
    doors.forEach(door => {
        door.addEventListener('click', (e) => {
            const isLocked = door.dataset.locked === 'true';
            const roomId = door.dataset.roomId;
            const roomName = door.dataset.roomName;
            const minLevel = door.dataset.minLevel;
            
            if (isLocked) {
                // Locked door feedback
                door.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    door.style.animation = '';
                }, 500);
                
                alert(`🔒 ${roomName} is locked!\nYou need Level ${minLevel} to enter this room.`);
                return;
            }
            
            // Start transition animation
            const rect = door.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            // Set transform origin to the door
            lobbyBg.style.transformOrigin = `${centerX}px ${centerY}px`;
            lobbyBg.classList.add('zoom-fade-out');
            
            // Fade to black
            setTimeout(() => {
                overlay.style.pointerEvents = 'auto';
                overlay.style.opacity = '1';
            }, 300);
            
            // Navigate after animation
            setTimeout(() => {
                window.location.href = `room.php?id=${roomId}`;
            }, 1000);
        });
    });
});

// Add shake animation for locked doors
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
`;
document.head.appendChild(style);
</script>

<?php 
$conn->close();
include '../footer.php'; 
?>