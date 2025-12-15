<?php
/**
 * Admin - Room Editor
 * Uses Middleware for admin authentication
 * Refactored to use standard sidebar with room selection in main content
 */

// Load bootstrap
require_once __DIR__ . '/../../app/bootstrap.php';

// Require admin access
requireAdmin();

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
        $row['top'] = $row['position_top'] ?? '50%';
        $row['left'] = $row['position_left'] ?? '50%';
        $artifacts[] = $row;
    }
}

include __DIR__ . '/../header.php';
?>

<div class="flex h-screen bg-black overflow-hidden">
    <!-- Sidebar Component (Standard) -->
    <?php adminSidebar('room_editor'); ?>

    <!-- Main Content -->
    <main class="flex-grow relative flex flex-col h-full overflow-hidden">
        <?php if (!$selected_room): ?>
            <!-- Room Selection View -->
            <div class="p-8 overflow-y-auto">
                <div class="mb-8">
                    <h2 class="text-3xl text-white font-serif mb-2">Room Editor</h2>
                    <p class="text-gray-400">Select a room to edit artifact positions and configure settings</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($rooms as $room): 
                        $artifactCount = $conn->query("SELECT COUNT(*) as c FROM artifacts WHERE room_id = {$room['id']}")->fetch_assoc()['c'];
                    ?>
                        <?php roomCard($room, 'room_editor.php', 'Artifacts', $artifactCount); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Toolbar -->
            <div class="bg-gray-900 border-b border-gray-700 p-4 flex justify-between items-center z-10 shadow-lg shrink-0">
                <div class="flex items-center gap-4">
                    <a href="room_editor.php" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-white font-serif text-xl border-l-4 border-gold pl-3"><?php echo $selected_room['name']; ?></h2>
                        <p class="text-gray-400 text-xs mt-1 ml-4">Drag artifacts to reposition. Click Save when done.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button id="btn-toggle-dialogs" class="btn-museum bg-purple-600 border-purple-500 text-white hover:bg-purple-500">
                        <i class="fas fa-comments mr-2"></i> Edit Dialogs
                    </button>
                    <button id="btn-save" class="btn-museum bg-green-600 border-green-500 text-white hover:bg-green-500">
                        <i class="fas fa-save mr-2"></i> Save Positions
                    </button>
                </div>
            </div>
            
            <!-- Dialog Editor Panel (Hidden by default) -->
            <div id="dialog-editor" class="hidden bg-gray-900 border-b border-gray-700 p-6 max-h-[70vh] overflow-y-auto shrink-0">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-gold font-serif text-lg"><i class="fas fa-book-open mr-2"></i>Professor Dialog Messages</h3>
                        <p class="text-gray-400 text-xs mt-1">Enter one message per line. These will be shown during room intro.</p>
                    </div>
                    <button id="btn-save-dialogs" class="btn-museum bg-gold text-black hover:bg-gold-hover">
                        <i class="fas fa-save mr-2"></i> Save Dialogs
                    </button>
                </div>
                <textarea id="dialogs-textarea" rows="8" class="w-full bg-black border border-gray-700 text-white p-4 rounded font-mono text-sm focus:border-gold outline-none" placeholder="Welcome, young explorer! I am Professor Aldric.
You have entered the Room Name. A magnificent place, isn't it?
Room description goes here.
Look for the glowing markers to find hidden artifacts.
Collect them all to gain knowledge and experience. Good luck!"><?php 
                    $dialogs = json_decode($selected_room['professor_dialogs'] ?? '[]', true);
                    echo htmlspecialchars(implode("\n", $dialogs ?: []));
                ?></textarea>
                
                <!-- Hidden Artifact Section -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <h3 class="text-purple-400 font-serif text-lg mb-4"><i class="fas fa-gem mr-2"></i>Hidden Artifact (Unlocks with 50% Quiz Score)</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">Artifact Name</label>
                            <input type="text" id="hidden-name" value="<?php echo htmlspecialchars($selected_room['hidden_artifact_name'] ?? ''); ?>" class="w-full bg-black border border-gray-700 text-white p-2 rounded focus:border-gold outline-none" placeholder="Mystery Artifact">
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs mb-1 block">XP Reward</label>
                            <input type="number" id="hidden-xp" value="<?php echo intval($selected_room['hidden_artifact_xp'] ?? 100); ?>" class="w-full bg-black border border-gray-700 text-white p-2 rounded focus:border-gold outline-none" placeholder="100">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-gray-400 text-xs mb-1 block">Description</label>
                        <textarea id="hidden-desc" rows="2" class="w-full bg-black border border-gray-700 text-white p-2 rounded focus:border-gold outline-none" placeholder="A mysterious artifact..."><?php echo htmlspecialchars($selected_room['hidden_artifact_desc'] ?? ''); ?></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="text-gray-400 text-xs mb-1 block">Image URL (optional)</label>
                        <input type="text" id="hidden-image" value="<?php echo htmlspecialchars($selected_room['hidden_artifact_image'] ?? ''); ?>" class="w-full bg-black border border-gray-700 text-white p-2 rounded focus:border-gold outline-none" placeholder="https://...">
                    </div>
                    <button id="btn-save-hidden" class="mt-4 btn-museum bg-purple-600 border-purple-500 text-white hover:bg-purple-500">
                        <i class="fas fa-save mr-2"></i> Save Hidden Artifact
                    </button>
                </div>
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
                            
                            <!-- Label -->
                            <div class="absolute top-full mt-0 left-1/2 transform -translate-x-1/2 whitespace-nowrap z-50 flex flex-col items-center">
                                <div class="bg-black/90 text-gold text-xs px-3 py-1 rounded border border-gold/30 shadow-lg font-serif tracking-wide">
                                    <?php echo htmlspecialchars($artifact['name']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
    
    // Drag Logic
    draggables.forEach(el => {
        el.addEventListener('mousedown', startDrag);
    });
    
    function startDrag(e) {
        activeDrag = this;
        initialX = e.clientX;
        initialY = e.clientY;
        
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', endDrag);
        activeDrag.style.zIndex = 100;
    }
    
    function drag(e) {
        if (!activeDrag) return;
        e.preventDefault();
        
        const dx = e.clientX - initialX;
        const dy = e.clientY - initialY;
        
        let newLeftCtx = activeDrag.offsetLeft + dx;
        let newTopCtx = activeDrag.offsetTop + dy;
        
        const containerRect = container.getBoundingClientRect();
        newLeftCtx = Math.max(0, Math.min(newLeftCtx, containerRect.width - activeDrag.offsetWidth));
        newTopCtx = Math.max(0, Math.min(newTopCtx, containerRect.height - activeDrag.offsetHeight));
        
        activeDrag.style.left = newLeftCtx + 'px';
        activeDrag.style.top = newTopCtx + 'px';
        
        initialX = e.clientX;
        initialY = e.clientY;
    }
    
    function endDrag() {
        if (!activeDrag) return;
        
        const containerRect = container.getBoundingClientRect();
        const leftPercent = (activeDrag.offsetLeft / containerRect.width) * 100;
        const topPercent = (activeDrag.offsetTop / containerRect.height) * 100;
        
        activeDrag.style.left = leftPercent.toFixed(2) + '%';
        activeDrag.style.top = topPercent.toFixed(2) + '%';
        
        activeDrag.style.zIndex = '';
        activeDrag = null;
        
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', endDrag);
        
        if (saveBtn) saveBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Positions *';
    }
    
    // Save Logic
    if (saveBtn) {
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
            
            fetch('../save_artifact_position.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
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
    }
    
    // Dialog Editor Toggle
    const toggleDialogsBtn = document.getElementById('btn-toggle-dialogs');
    const dialogEditor = document.getElementById('dialog-editor');
    const saveDialogsBtn = document.getElementById('btn-save-dialogs');
    const dialogsTextarea = document.getElementById('dialogs-textarea');
    
    if (toggleDialogsBtn && dialogEditor) {
        toggleDialogsBtn.addEventListener('click', () => {
            dialogEditor.classList.toggle('hidden');
            toggleDialogsBtn.innerHTML = dialogEditor.classList.contains('hidden') 
                ? '<i class="fas fa-comments mr-2"></i> Edit Dialogs'
                : '<i class="fas fa-times mr-2"></i> Close Dialogs';
        });
    }
    
    // Save Dialogs
    if (saveDialogsBtn && dialogsTextarea) {
        saveDialogsBtn.addEventListener('click', () => {
            saveDialogsBtn.disabled = true;
            saveDialogsBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            
            const lines = dialogsTextarea.value.split('\n').filter(line => line.trim() !== '');
            const roomId = <?php echo $selected_room ? $selected_room['id'] : 0; ?>;
            
            fetch('../admin_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=save_room_dialogs&room_id=${roomId}&dialogs=${encodeURIComponent(JSON.stringify(lines))}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    saveDialogsBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Saved!';
                    setTimeout(() => {
                        saveDialogsBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Dialogs';
                        saveDialogsBtn.disabled = false;
                    }, 2000);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                    saveDialogsBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Request failed');
                saveDialogsBtn.disabled = false;
            });
        });
    }
    
    // Save Hidden Artifact
    const saveHiddenBtn = document.getElementById('btn-save-hidden');
    if (saveHiddenBtn) {
        saveHiddenBtn.addEventListener('click', () => {
            saveHiddenBtn.disabled = true;
            saveHiddenBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            
            const data = {
                action: 'save_hidden_artifact',
                room_id: <?php echo $selected_room ? $selected_room['id'] : 0; ?>,
                name: document.getElementById('hidden-name').value,
                desc: document.getElementById('hidden-desc').value,
                image: document.getElementById('hidden-image').value,
                xp: document.getElementById('hidden-xp').value
            };
            
            fetch('../admin_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: Object.keys(data).map(key => `${key}=${encodeURIComponent(data[key])}`).join('&')
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    saveHiddenBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Saved!';
                    setTimeout(() => {
                        saveHiddenBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Hidden Artifact';
                        saveHiddenBtn.disabled = false;
                    }, 2000);
                } else {
                    alert('Error: ' + (result.message || 'Unknown error'));
                    saveHiddenBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Request failed');
                saveHiddenBtn.disabled = false;
            });
        });
    }
});
</script>
