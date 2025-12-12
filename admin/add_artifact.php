<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../app/Config/database.php';

// Fetch Rooms for Dropdown
$rooms = $conn->query("SELECT id, name FROM rooms");

include '../public/header.php';
?>

<div class="flex h-screen bg-black">
    <aside class="w-64 bg-darker-bg border-r border-gold/20 flex flex-col">
        <div class="p-6 border-b border-gold/20">
            <h1 class="text-gold font-serif text-2xl font-bold">Curator Panel</h1>
        </div>
        <nav class="flex-grow p-4 space-y-2">
             <a href="index.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-chart-line w-6"></i> Dashboard
            </a>
            <a href="artifacts.php" class="block px-4 py-3 rounded bg-gold/10 text-gold border-l-4 border-gold">
                <i class="fas fa-boxes w-6"></i> Manage Artifacts
            </a>
            <a href="users.php" class="block px-4 py-3 rounded text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <i class="fas fa-users w-6"></i> Visitors
            </a>
        </nav>
        <div class="p-4 border-t border-gold/20">
            <a href="../public/index.php" class="block w-full text-center py-2 border border-gray-700 text-gray-400 hover:text-white hover:border-white rounded transition mb-2">View Site</a>
        </div>
    </aside>

    <main class="flex-grow p-8 overflow-y-auto">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="artifacts.php" class="text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i> Back</a>
                <h2 class="text-3xl text-white font-serif">Add New Artifact</h2>
            </div>

            <form action="../app/Handlers/admin_handler.php" method="POST" class="bg-darker-bg p-8 rounded-lg border border-gray-800 space-y-6">
                <input type="hidden" name="action" value="add_artifact">
                
                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Artifact Name</label>
                    <input type="text" name="name" required class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 text-sm uppercase mb-2">Room</label>
                        <select name="room_id" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                            <?php while($room = $rooms->fetch_assoc()): ?>
                                <option value="<?php echo $room['id']; ?>"><?php echo $room['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm uppercase mb-2">XP Reward</label>
                        <input type="number" name="xp_reward" value="50" class="w-full bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-sm uppercase mb-2">Image URL</label>
                    <div class="flex gap-2">
                        <input type="text" name="image_url" id="img_url" required class="flex-grow bg-black border border-gray-700 text-white px-4 py-2 rounded focus:border-gold outline-none" placeholder="https://...">
                        <button type="button" onclick="previewImage()" class="px-4 py-2 bg-gray-800 text-gray-300 rounded hover:bg-gray-700">Preview</button>
                    </div>
                    <div class="mt-4 border border-dashed border-gray-700 rounded h-40 flex items-center justify-center overflow-hidden bg-black/50">
                        <img id="preview" src="" class="hidden w-full h-full object-contain">
                        <span id="preview-text" class="text-gray-600">Image Preview</span>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-800 flex justify-end">
                    <button type="submit" class="bg-gold hover:bg-gold-hover text-black font-bold py-3 px-8 rounded transition">
                        Save Artifact
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function previewImage() {
    const url = document.getElementById('img_url').value;
    const img = document.getElementById('preview');
    const text = document.getElementById('preview-text');
    
    if (url) {
        img.src = url;
        img.classList.remove('hidden');
        text.classList.add('hidden');
    }
}
</script>
