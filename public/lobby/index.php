<?php
// Secure page
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../header.php';
include '../navbar.php';

// Fetch Rooms from DB
require_once '../../app/Config/database.php';

$user_level = $_SESSION['level'] ?? 1;

$sql = "SELECT * FROM rooms ORDER BY min_level ASC";
$result = $conn->query($sql);

$rooms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['is_locked'] = ($user_level < $row['min_level']);
        $rooms[] = $row;
    }
}
$conn->close();
?>

<div class="flex-grow container mx-auto px-4 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl text-gold mb-2">Museum Hall</h1>
        <p class="text-gray-400">Select a room to begin your exploration.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php foreach ($rooms as $room): ?>
            <div class="museum-card relative group rounded-lg overflow-hidden h-96">
                <!-- Background Image -->
                <div class="absolute inset-0 bg-cover bg-center transition duration-500 group-hover:scale-110"
                     style="background-image: url('<?php echo $room['image_url']; ?>');">
                </div>
                
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-60 transition duration-300 group-hover:bg-opacity-40"></div>

                <!-- Lock Overlay -->
                <?php if ($room['is_locked']): ?>
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-70 z-20">
                        <i class="fas fa-lock text-4xl text-gray-500 mb-2"></i>
                        <span class="text-gray-400 uppercase tracking-widest text-xs">Locked</span>
                        <span class="text-gold text-xs mt-1">Lvl <?php echo $room['min_level']; ?> Required</span>
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <div class="absolute bottom-0 left-0 right-0 p-6 z-10 bg-gradient-to-t from-black to-transparent">
                    <h3 class="text-xl text-white font-serif mb-2 border-b border-gold/50 pb-2 inline-block">
                        <?php echo $room['name']; ?>
                    </h3>
                    <p class="text-gray-300 text-sm mb-4 line-clamp-2">
                        <?php echo $room['description']; ?>
                    </p>
                    
                    <?php if (!$room['is_locked']): ?>
                        <a href="room.php?id=<?php echo $room['id']; ?>" class="inline-block text-gold text-sm hover:text-white transition uppercase tracking-wider">
                            Enter Room <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../footer.php'; ?>
