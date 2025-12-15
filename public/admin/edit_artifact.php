<?php
/**
 * Admin - Edit Artifact
 * Uses Middleware for admin authentication
 */

// Load bootstrap
require_once __DIR__ . '/../../app/bootstrap.php';

// Require admin access
requireAdmin();

if (!isset($_GET['id'])) {
    header("Location: artifacts.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM artifacts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artifact = $stmt->get_result()->fetch_assoc();

if (!$artifact) {
    echo "Artifact not found";
    exit();
}

// Fetch Rooms for Dropdown
$rooms = $conn->query("SELECT id, name FROM rooms");

include __DIR__ . '/../header.php';
?>

<div class="flex h-screen bg-black">
    <!-- Sidebar Component -->
    <?php adminSidebar('artifacts'); ?>

    <main class="flex-grow p-8 overflow-y-auto">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="artifacts.php" class="text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i> Back</a>
                <h2 class="text-3xl text-white font-serif">Edit Artifact</h2>
            </div>

            <form action="../app/Handlers/admin_handler.php" method="POST" enctype="multipart/form-data" class="bg-darker-bg p-8 rounded-lg border border-gray-800 space-y-6">
                <input type="hidden" name="action" value="edit_artifact">
                <input type="hidden" name="id" value="<?php echo $artifact['id']; ?>">
                
                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Artifact Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($artifact['name']); ?>" required class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none"><?php echo htmlspecialchars($artifact['description']); ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 text-sm uppercase mb-2">Room</label>
                        <select name="room_id" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                            <?php while($room = $rooms->fetch_assoc()): ?>
                                <option value="<?php echo $room['id']; ?>" <?php echo ($room['id'] == $artifact['room_id']) ? 'selected' : ''; ?>>
                                    <?php echo $room['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm uppercase mb-2">XP Reward</label>
                        <input type="number" name="xp_reward" value="<?php echo $artifact['xp_reward']; ?>" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Artifact Image</label>
                    
                    <!-- File Upload -->
                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 mb-1">Update File (Will replace current image)</label>
                        <input type="file" name="image_file" id="img_file" accept="image/*" class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gold file:text-black hover:file:bg-gold-hover file:cursor-pointer">
                    </div>

                    <!-- URL Fallback -->
                    <div class="mb-2">
                        <label class="block text-xs text-gray-500 mb-1">Or using URL</label>
                        <div class="flex gap-2">
                            <input type="text" name="image_url" id="img_url" value="<?php echo htmlspecialchars($artifact['image_url']); ?>" class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                            <button type="button" onclick="previewImage()" class="px-4 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">Preview</button>
                        </div>
                    </div>

                    <div class="mt-4 border border-dashed border-gray-700 rounded h-40 flex items-center justify-center overflow-hidden bg-black/50">
                        <img id="preview" src="<?php echo htmlspecialchars($artifact['image_url']); ?>" class="w-full h-full object-contain">
                        <span id="preview-text" class="text-gray-600 hidden">Image Preview</span>
                    </div>
                    <p class="text-red-500 text-xs mt-2 hidden" id="error-msg">* Please provide either an Image URL or Upload a File.</p>
                </div>

                <div class="pt-6 border-t border-gray-800 flex justify-end">
                    <button type="submit" id="btn-submit" class="bg-gold hover:bg-gold-hover text-black font-bold py-3 px-8 rounded transition">
                        Update Artifact
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function previewImage() {
    const url = document.getElementById('img_url').value;
    const fileInput = document.getElementById('img_file');
    const img = document.getElementById('preview');
    const text = document.getElementById('preview-text');
    
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.classList.remove('hidden');
            if(text) text.classList.add('hidden');
        }
        reader.readAsDataURL(fileInput.files[0]);
    } else if (url) {
        img.src = url;
        img.classList.remove('hidden');
        if(text) text.classList.add('hidden');
    }
}

function validateForm() {
    const url = document.getElementById('img_url').value.trim();
    const fileInput = document.getElementById('img_file');
    const btn = document.getElementById('btn-submit');
    const errorMsg = document.getElementById('error-msg');
    
    const hasFile = fileInput.files && fileInput.files.length > 0;
    const hasUrl = url.length > 0;
    
    if (hasFile || hasUrl) {
        btn.disabled = false;
        btn.classList.remove('bg-gray-600', 'text-gray-300', 'cursor-not-allowed');
        btn.classList.add('bg-gold', 'text-black', 'hover:bg-gold-hover');
        errorMsg.classList.add('hidden');
    } else {
        btn.disabled = true;
        btn.classList.add('bg-gray-600', 'text-gray-300', 'cursor-not-allowed');
        btn.classList.remove('bg-gold', 'text-black', 'hover:bg-gold-hover');
        errorMsg.classList.remove('hidden');
    }
}

// Event Listeners
document.getElementById('img_file').addEventListener('change', () => {
    previewImage();
    validateForm();
});
document.getElementById('img_url').addEventListener('input', () => {
    validateForm();
});

// Initial validation
validateForm();
</script>
