<?php
session_start();
// Check Auth
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database and Models
require_once '../../app/Config/database.php';
require_once '../../app/Models/User.php';
require_once '../../app/Models/Quiz.php';
require_once '../../app/Models/Room.php';

$user_id = $_SESSION['user_id'];
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$answer = isset($_GET['answer']) ? strtolower(trim($_GET['answer'])) : '';

// Validate input
if ($room_id <= 0 || $quiz_id <= 0 || !in_array($answer, ['a', 'b', 'c'])) {
    header("Location: quiz.php?room_id=" . $room_id);
    exit();
}

// Submit quiz answer using Model
$result = Quiz::submit($conn, $user_id, $quiz_id, $answer);

// Update session
if (isset($result['new_xp']) && $result['new_xp']) {
    $_SESSION['xp'] = $result['new_xp'];
}
if (isset($result['leveled_up']) && $result['leveled_up']) {
    $_SESSION['level'] = $result['new_level'];
}

// Get quiz for correct answer display
$quiz = Quiz::findById($conn, $quiz_id);

// Check for hidden artifact unlock
$hidden_unlocked = false;
$hidden_artifact = null;

if (!Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id)) {
    if (Quiz::checkHiddenArtifactUnlock($conn, $room_id, $user_id)) {
        Room::unlockHiddenArtifact($conn, $room_id, $user_id);
        $hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
        if ($hidden_artifact) {
            $hidden_unlocked = true;
            User::addXp($conn, $user_id, $hidden_artifact['xp']);
        }
    }
}

// Get room info for display
$room = Room::findById($conn, $room_id);

include '../header.php';
?>

<style>
    .btn-museum {
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem;
        border: 1px solid #C5A059;
        font-family: 'Cinzel', serif;
    }
</style>

<div class="min-h-screen bg-black text-gray-200 flex flex-col">
    <!-- Background -->
    <div class="fixed inset-0 z-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo $room['image_url']; ?>');"></div>
    
    <!-- Main Content -->
    <div class="flex-grow relative z-10 flex flex-col justify-end">
        
        <!-- Professor Container - z-40 to be in front of dialog box -->
        <div class="absolute bottom-0 left-0 md:left-4 h-[70vh] w-[300px] z-40 pointer-events-none flex items-end">
            <img src="/project-akhir/public/assets/img/professor.gif" alt="Professor" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)]">
        </div>

        <!-- Dialogue Box -->
        <div class="relative w-full bg-black/95 border-t-2 border-gold z-30 p-6 pb-8 md:pl-[280px]">
            <!-- Name Tag -->
            <div class="absolute -top-8 left-6 md:left-[280px] bg-gold text-black px-4 py-1 font-serif font-bold text-lg rounded-t shadow-[0_0_15px_rgba(197,160,89,0.5)]">
                Professor Aldric
            </div>

            <div class="py-4">
                <?php if (isset($result['is_correct']) && $result['is_correct']): ?>
                    <!-- Correct Answer -->
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-2 text-green-500 text-2xl font-bold mb-2">
                            <i class="fas fa-check-circle"></i> Correct!
                        </span>
                        <p class="font-serif text-xl text-gray-200 mt-2">
                            <?php 
                            $correct_msgs = [
                                "Marvelous! Your knowledge is impressive.",
                                "Precisely correct! You have a keen eye for history.",
                                "Excellent! You understand this well.",
                                "Splendid! That is exactly right."
                            ];
                            echo $correct_msgs[array_rand($correct_msgs)];
                            ?>
                        </p>
                        <p class="text-gold mt-2">+<?php echo $result['xp_earned']; ?> XP earned!</p>
                    </div>
                <?php else: ?>
                    <!-- Incorrect Answer -->
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-2 text-red-500 text-2xl font-bold mb-2">
                            <i class="fas fa-times-circle"></i> Incorrect
                        </span>
                        <p class="font-serif text-xl text-gray-200 mt-2">
                            <?php 
                            $incorrect_msgs = [
                                "Not quite right, I'm afraid.",
                                "Ah, a common misconception.",
                                "Incorrect. But do not be discouraged.",
                                "That is not the answer, but keep trying."
                            ];
                            echo $incorrect_msgs[array_rand($incorrect_msgs)];
                            ?>
                        </p>
                        <p class="text-gray-400 mt-2">
                            The correct answer was: <span class="text-gold font-bold uppercase"><?php echo strtoupper($quiz['correct_option']); ?></span>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($hidden_unlocked && $hidden_artifact): ?>
                    <!-- Hidden Artifact Unlocked -->
                    <div class="mt-6 p-6 border-2 border-gold rounded-lg bg-gold/10 text-center">
                        <h3 class="text-2xl text-gold font-serif mb-4">Secret Artifact Discovered!</h3>
                        <?php if ($hidden_artifact['image']): ?>
                            <img src="<?php echo $hidden_artifact['image']; ?>" class="h-24 mx-auto mb-4">
                        <?php endif; ?>
                        <p class="text-xl text-white font-bold"><?php echo $hidden_artifact['name']; ?></p>
                        <p class="text-gray-400 mt-2"><?php echo $hidden_artifact['desc']; ?></p>
                        <p class="text-gold font-bold mt-2">+<?php echo $hidden_artifact['xp']; ?> Bonus XP!</p>
                    </div>
                <?php endif; ?>

                <!-- Continue Button -->
                <div class="mt-6">
                    <a href="quiz.php?room_id=<?php echo $room_id; ?>" class="btn-museum bg-gold text-black hover:bg-white">
                        <i class="fas fa-arrow-right mr-2"></i> Continue
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
