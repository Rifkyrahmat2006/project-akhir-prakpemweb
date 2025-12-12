<?php
session_start();
// Check Auth
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
// Check ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

require_once '../../app/Config/database.php';

$room_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch Room Info
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    echo "Room not found.";
    exit();
}

// Check Access (Security)
if ($_SESSION['level'] < $room['min_level']) {
    header("Location: index.php"); // Redirect if trying to bypass lock
    exit();
}

// Fetch Artifacts & Collection Status
$sql = "SELECT a.*, 
        (SELECT COUNT(*) FROM user_collections uc WHERE uc.artifact_id = a.id AND uc.user_id = ?) as is_collected
        FROM artifacts a 
        WHERE a.room_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $room_id);
$stmt->execute();
$result = $stmt->get_result();

$artifacts = [];
// Generate random positions for demo since DB doesn't have coords yet
// In a real app, coordinates would be stored in DB
$positions = [
    ['top' => '60%', 'left' => '20%'],
    ['top' => '70%', 'left' => '50%'],
    ['top' => '55%', 'left' => '80%'],
    ['top' => '40%', 'left' => '30%'],
    ['top' => '50%', 'left' => '60%'],
];

$i = 0;
while($row = $result->fetch_assoc()) {
    $pos = $positions[$i % count($positions)];
    $row['top'] = $pos['top'];
    $row['left'] = $pos['left'];
    $artifacts[] = $row;
    $i++;
}

include '../header.php';
include '../navbar.php';
?>

<div class="relative w-full h-[calc(100vh-64px)] overflow-hidden bg-black">
    <!-- Back Button -->
    <a href="index.php" class="absolute top-4 left-4 z-30 btn-museum bg-black/50 text-white border-white/30 hover:bg-gold hover:text-black">
        <i class="fas fa-arrow-left mr-2"></i> Back to Lobby
    </a>
    
    <!-- Quiz Button -->
    <a href="quiz.php?room_id=<?php echo $room_id; ?>" class="absolute top-4 left-48 z-30 btn-museum bg-black/50 text-gold border-gold/50 hover:bg-gold hover:text-black">
        <i class="fas fa-question-circle mr-2"></i> Take Quiz
    </a>

    <!-- Room Background -->
    <div class="absolute inset-0 bg-cover bg-center z-0" 
         style="background-image: url('<?php echo $room['image_url']; ?>');">
        <!-- Vignette -->
        <div class="absolute inset-0 bg-radial-gradient"></div>
    </div>

    <!-- Interactive Artifacts -->
    <div class="relative w-full h-full z-10" id="artifact-container">
        <?php foreach ($artifacts as $artifact): ?>
            <?php $is_collected = $artifact['is_collected'] > 0; ?>
            <div class="absolute cursor-pointer transform hover:scale-110 transition duration-300 group artifact-item"
                 data-id="<?php echo $artifact['id']; ?>"
                 data-name="<?php echo htmlspecialchars($artifact['name']); ?>"
                 data-desc="<?php echo htmlspecialchars($artifact['description']); ?>"
                 data-collected="<?php echo $is_collected ? 'true' : 'false'; ?>"
                 style="top: <?php echo $artifact['top']; ?>; left: <?php echo $artifact['left']; ?>;">
                
                <!-- Glowing effect (Yellow if new, Grey/Green if collected) -->
                <div class="absolute inset-0 <?php echo $is_collected ? 'bg-green-500' : 'bg-gold'; ?> blur-md opacity-20 group-hover:opacity-60 animate-pulse rounded-full status-glow"></div>
                
                <!-- Icon/Image -->
                <div class="relative w-12 h-12 <?php echo $is_collected ? 'bg-green-900/60 border-green-500 text-green-500' : 'bg-black/60 border-gold text-gold'; ?> border rounded-full flex items-center justify-center group-hover:bg-opacity-80 transition status-icon">
                    <i class="fas <?php echo $is_collected ? 'fa-check' : 'fa-gem'; ?> text-xl"></i>
                </div>

                <!-- Label Tooltip -->
                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap bg-black/80 text-gold text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition delay-100 pointer-events-none border border-gold/30">
                    <?php echo $artifact['name']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Room Title Overlay -->
    <div class="absolute bottom-10 right-10 z-20 text-right pointer-events-none">
        <h2 class="text-6xl text-white/10 font-serif font-bold uppercase select-none"><?php echo $room['name']; ?></h2>
    </div>

    <?php include '../artifact_detail.php'; ?>
</div>

<script>
    // Specific room logic if any (e.g. background audio)
</script>

<?php include '../footer.php'; ?>
