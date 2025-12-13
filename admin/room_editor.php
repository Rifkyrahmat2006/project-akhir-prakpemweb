<?php
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

// Fetch Rooms
$rooms_result = $conn->query("SELECT * FROM rooms");
$rooms = [];
while($row = $rooms_result->fetch_assoc()) {
    $rooms[] = $row;
}

$selected_room = null;
$artifacts = [];

if (isset($_GET['room_id'])) {
    $room_id = intval($_GET['room_id']);
    
    // Fetch Room Info
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $selected_room = $stmt->get_result()->fetch_assoc();
    
    // Fetch Artifacts
    $stmt = $conn->prepare("SELECT * FROM artifacts WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        // Use saved position or default to random-ish center if null
        $row['top'] = $row['position_top'] ?? '50%';
        $row['left'] = $row['position_left'] ?? '50%';
        $artifacts[] = $row;
    }
}

include '../public/header.php';
?>

<div class="flex h-screen bg-black overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-darker-bg border-r border-gold/20 flex flex-col shrink-0 overflow-y-auto z-20">
        <div class="p-6 border-b border-gold/20">
            <h1 class="text-gold font-serif text-2xl font-bold">Curator Panel</h1>
            <p class="text-gray-500 text-xs uppercase tracking-widest mt-1">Room Editor</p>
        </div>
        
        <nav class="flex-grow p-4 space-y-2">
            <a href="index.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left w-6"></i> Back to Dashboard
            </a>
            
            <div class="mt-4 mb-2 px-4 text-xs text-gray-500 uppercase tracking-widest">Select Room</div>
            
            <?php foreach($rooms as $room): ?>
                <a href="?room_id=<?php echo $room['id']; ?>" class="block px-4 py-3 rounded transition flex items-center justify-between <?php echo ($selected_room && $selected_room['id'] == $room['id']) ? 'bg-gold/10 text-gold border-l-4 border-gold' : 'text-gray-400 hover:text-white hover:bg-gray-800'; ?>">
                    <span><?php echo htmlspecialchars($room['name']); ?></span>
                    <i class="fas fa-chevron-right text-xs opacity-50"></i>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow relative flex flex-col h-full">
        <?php if ($selected_room): ?>
            <!-- Toolbar -->
            <div class="bg-gray-900 border-b border-gray-700 p-4 flex justify-between items-center z-10 shadow-lg">
                <div>
                    <h2 class="text-white font-serif text-xl border-l-4 border-gold pl-3"><?php echo $selected_room['name']; ?></h2>
                    <p class="text-gray-400 text-xs mt-1 ml-4">Drag artifacts to reposition. Click Save when done.</p>
                </div>
                <button id="btn-save" class="btn-museum bg-green-600 border-green-500 text-white hover:bg-green-500">
                    <i class="fas fa-save mr-2"></i> Save Positions
                </button>
            </div>
            
            <!-- Editor Canvas -->
            <div class="flex-grow relative bg-black overflow-hidden select-none bg-checkered" id="canvas-container">
                <!-- Room Background -->
                <div class="absolute inset-0 bg-cover bg-center pointer-events-none opacity-50"
                     style="background-image: url('<?php echo htmlspecialchars($selected_room['image_url']); ?>');">
                </div>
                
                <!-- Artifacts -->
                <?php foreach ($artifacts as $artifact): ?>
                    <div class="absolute w-12 h-12 cursor-move group artifact-draggable"
                         data-id="<?php echo $artifact['id']; ?>"
                         style="top: <?php echo $artifact['top']; ?>; left: <?php echo $artifact['left']; ?>;">
                        
                        <!-- Visual representation (Glowing Dot) -->
                        <div class="relative w-12 h-12 flex items-center justify-center">
                            <!-- 1. Outer Diffuse Glow -->
                            <div class="absolute inset-[-5px] rounded-full bg-gold/20 blur-xl animate-pulse group-hover:bg-gold/60 group-hover:blur-2xl transition duration-500"></div>
                            
                            <!-- 2. Inner Focused Glow -->
                            <div class="absolute inset-1 rounded-full bg-gold/40 blur-md group-hover:bg-gold/80 group-hover:bg-opacity-100 transition duration-300"></div>
                            
                            <!-- 3. Core Light -->
                            <div class="absolute w-1 h-1 bg-white rounded-full shadow-[0_0_10px_rgba(255,255,255,0.8)] group-hover:w-2 group-hover:h-2 group-hover:shadow-[0_0_30px_rgba(255,255,255,1)] transition-all duration-300"></div>
                            
                            <!-- Label (Always visible + Arrow) -->
                            <div class="absolute top-full mt-0 left-1/2 transform -translate-x-1/2 whitespace-nowrap z-50 flex flex-col items-center">
                                <!-- <div class="w-0.5 h-4 bg-gold/50 mb-1"></div> Connectivity Line -->
                                <div class="bg-black/90 text-gold text-xs px-3 py-1 rounded border border-gold/30 shadow-lg font-serif tracking-wide">
                                    <?php echo htmlspecialchars($artifact['name']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="flex items-center justify-center h-full bg-gray-900 text-gray-500 flex-col">
                <i class="fas fa-map-marked-alt text-6xl mb-4 text-gray-700"></i>
                <p>Select a room from the sidebar to begin editing.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
/* Checkered pattern for editor background clarity */
.bg-checkered {
    background-image: 
        linear-gradient(45deg, #1a1a1a 25%, transparent 25%), 
        linear-gradient(-45deg, #1a1a1a 25%, transparent 25%), 
        linear-gradient(45deg, transparent 75%, #1a1a1a 75%), 
        linear-gradient(-45deg, transparent 75%, #1a1a1a 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const draggables = document.querySelectorAll('.artifact-draggable');
    const container = document.getElementById('canvas-container');
    const saveBtn = document.getElementById('btn-save');
    
    if (!container) return; // No room selected
    
    let activeDrag = null;
    let initialX, initialY;
    let initialLeft, initialTop;
    
    // Drag Logic
    draggables.forEach(el => {
        el.addEventListener('mousedown', startDrag);
    });
    
    function startDrag(e) {
        activeDrag = this;
        // Calculate initial mouse position relative to element
        const rect = activeDrag.getBoundingClientRect();
        
        // We want to work with percentages for responsiveness
        // But dragging is pixel-based.
        
        initialX = e.clientX;
        initialY = e.clientY;
        
        // Get current computed style
        const style = window.getComputedStyle(activeDrag);
        // Parse left/top (which might be in px or %)
        // For dragging simplicity, we'll convert to px, move, then save as % logic later?
        // Actually, let's keep it simple.
        
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', endDrag);
        activeDrag.style.zIndex = 100;
    }
    
    function drag(e) {
        if (!activeDrag) return;
        e.preventDefault();
        
        const dx = e.clientX - initialX;
        const dy = e.clientY - initialY;
        
        // Get current left/top in pixels
        // Warning: activeDrag.style.left might be empty or %.
        // offsetLeft gives px relative to offsetParent.
        
        let newLeftCtx = activeDrag.offsetLeft + dx;
        let newTopCtx = activeDrag.offsetTop + dy;
        
        // Boundaries
        const containerRect = container.getBoundingClientRect();
        // Constrain
        newLeftCtx = Math.max(0, Math.min(newLeftCtx, containerRect.width - activeDrag.offsetWidth));
        newTopCtx = Math.max(0, Math.min(newTopCtx, containerRect.height - activeDrag.offsetHeight));
        
        // Update position (temporarily in px)
        activeDrag.style.left = newLeftCtx + 'px';
        activeDrag.style.top = newTopCtx + 'px';
        
        initialX = e.clientX;
        initialY = e.clientY;
    }
    
    function endDrag() {
        if (!activeDrag) return;
        
        // Convert final position to percentages
        const containerRect = container.getBoundingClientRect();
        const leftPercent = (activeDrag.offsetLeft / containerRect.width) * 100;
        const topPercent = (activeDrag.offsetTop / containerRect.height) * 100;
        
        // Apply % style
        activeDrag.style.left = leftPercent.toFixed(2) + '%';
        activeDrag.style.top = topPercent.toFixed(2) + '%';
        
        activeDrag.style.zIndex = '';
        activeDrag = null;
        
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', endDrag);
        
        // Show unsaved indicator?
        saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Positions *';
    }
    
    // Save Logic
    saveBtn.addEventListener('click', () => {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        
        const positions = [];
        draggables.forEach(el => {
            positions.push({
                id: el.dataset.id,
                top: el.style.top,
                left: el.style.left
            });
        });
        
        fetch('../app/Handlers/save_artifact_position.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ positions: positions })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                saveBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Saved!';
                setTimeout(() => {
                    saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Positions';
                    saveBtn.disabled = false;
                }, 2000);
            } else {
                alert('Error saving: ' + (data.message || 'Unknown error'));
                saveBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Request failed');
            saveBtn.disabled = false;
        });
    });
});
</script>
