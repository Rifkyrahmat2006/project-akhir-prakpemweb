<?php
/**
 * Quiz Page - Room quiz questions
 * Uses Middleware for authentication and access control
 */

// Load bootstrap (includes all middleware, models, and database)
require_once __DIR__ . '/../../app/bootstrap.php';

// Require authentication
requireAuth('../login.php');

// Check room_id parameter
if (!isset($_GET['room_id'])) {
    header("Location: index.php");
    exit();
}

$room_id = intval($_GET['room_id']);
$user_id = userId();

// Fetch Room Info using Model
$room = Room::findById($conn, $room_id);

if (!$room) {
    echo "Room not found.";
    exit();
}

// Check Access (Security) - Require minimum level
requireLevel($room['min_level'], 'index.php');

// Fetch Quizzes with user status
$quizzes = Quiz::getForUser($conn, $room_id, $user_id);

// Find first unanswered quiz
$current_quiz = null;
$current_index = 0;
$answered_count = 0;
$total_count = count($quizzes);

foreach ($quizzes as $index => $q) {
    if ($q['is_answered'] > 0) {
        $answered_count++;
    } else if ($current_quiz === null) {
        $current_quiz = $q;
        $current_index = $index;
    }
}

// Check if all quizzes completed
$all_completed = ($answered_count >= $total_count && $total_count > 0);

// Calculate progress
$progress = $total_count > 0 ? ($answered_count / $total_count) * 100 : 0;

// Check if hidden artifact is unlocked for this room
$hidden_unlocked = Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id);

// Get next room info (id + 1)
$next_room = null;
if ($hidden_unlocked) {
    $next_room = Room::findById($conn, $room_id + 1);
    // Check if user has access (level requirement)
    if ($next_room && $_SESSION['level'] < $next_room['min_level']) {
        $next_room = null; // Don't show if user doesn't have access yet
    }
}

include '../header.php';
?>

<style>
    .quiz-option {
        transition: all 0.2s ease;
    }
    .quiz-option:hover {
        transform: translateY(-2px);
        border-color: #C5A059;
        background: rgba(197, 160, 89, 0.1);
    }
    .typewriter-cursor {
        animation: blink 0.7s infinite;
    }
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
</style>

<div class="min-h-screen bg-black text-gray-200 flex flex-col">
    <!-- Background -->
    <div class="fixed inset-0 z-0 bg-cover bg-center opacity-30" style="background-image: url('<?php echo $room['image_url']; ?>');"></div>
    
    <!-- Back Button -->
    <a href="room.php?id=<?php echo $room_id; ?>" class="fixed top-6 left-6 z-50 text-gold hover:text-white uppercase tracking-widest text-sm">
        <i class="fas fa-arrow-left mr-2"></i> Return to Room
    </a>

    <!-- Main Content -->
    <div class="flex-grow relative z-10 flex flex-col justify-end">
        
        <!-- Professor Container - Same size as room.php intro -->
        <div class="absolute bottom-0 left-0 md:left-[-2rem] h-[125vh] w-[400px] z-40 pointer-events-none flex items-end">
            <img id="prof-talking" src="<?php echo BASE_URL; ?>/assets/img/professor.gif" alt="Professor" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)] hidden">
            <img id="prof-idle" src="<?php echo BASE_URL; ?>/assets/img/professor-diam.png" alt="Professor" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)]">
        </div>

        <!-- Dialogue Box -->
        <div class="relative w-full bg-black/95 border-t-2 border-gold z-30 p-6 pb-8 md:pl-[350px]">
            <!-- Name Tag -->
            <div class="absolute -top-8 left-6 md:left-[350px] bg-gold text-black px-4 py-1 font-serif font-bold text-lg rounded-t shadow-[0_0_15px_rgba(197,160,89,0.5)]">
                Professor Aldric
            </div>

            <!-- Progress Bar -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gray-800">
                <div class="h-full bg-gold transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
            </div>

            <?php if ($total_count == 0): ?>
                <!-- No Quizzes -->
                <div class="text-center py-8">
                    <p class="font-serif text-xl text-gray-400">There are no quizzes available for this room yet.</p>
                    <a href="room.php?id=<?php echo $room_id; ?>" class="inline-block mt-4 btn-museum bg-gold text-black">Return to Room</a>
                </div>
            <?php elseif ($all_completed): ?>
                <!-- All Completed - Fixed height container -->
                <div class="py-4 min-h-[150px]">
                    <p id="typewriter-text" class="font-serif text-xl md:text-2xl text-gray-200 mb-6"></p>
                    <div id="completion-content" class="min-h-[60px]" style="opacity: 0; transition: opacity 0.3s ease;">
                        <div class="flex items-center gap-4 text-gold mb-6">
                            <i class="fas fa-trophy text-3xl"></i>
                            <span class="text-lg">Quiz Complete: <?php echo $answered_count; ?>/<?php echo $total_count; ?></span>
                            <?php if ($hidden_unlocked): ?>
                                <span class="text-green-500 ml-4"><i class="fas fa-gem mr-1"></i> Hidden Artifact Unlocked!</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <a href="room.php?id=<?php echo $room_id; ?>" class="btn-museum bg-gray-800 text-white border-gray-600 hover:bg-gold hover:text-black">
                                <i class="fas fa-door-open mr-2"></i> Return to Room
                            </a>
                            
                            <?php if ($next_room): ?>
                                <a href="room.php?id=<?php echo $next_room['id']; ?>" class="btn-museum bg-gold text-black hover:bg-white">
                                    <i class="fas fa-arrow-right mr-2"></i> Next: <?php echo htmlspecialchars($next_room['name']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <script>
                    const completionText = "<?php echo $hidden_unlocked ? 'Outstanding! You have mastered this chamber and unlocked its secrets!' : 'Excellent work! You have completed all ' . $total_count . ' questions in this chamber!'; ?>";
                </script>
            <?php else: ?>
                <!-- Current Quiz -->
                <div class="py-2">
                    <!-- Question Number -->
                    <div class="text-gold text-sm uppercase tracking-widest mb-2">
                        Question <?php echo $current_index + 1; ?> of <?php echo $total_count; ?>
                    </div>
                    
                    <!-- Question Text (Typewriter) -->
                    <p id="typewriter-text" class="font-serif text-xl md:text-2xl text-gray-200 mb-6">
                        <span class="typewriter-cursor text-gold">|</span>
                    </p>
                    
                    <!-- Answer Options Container - min-height prevents layout jump -->
                    <div class="min-h-[80px]">
                        <div id="answer-options" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="opacity: 0; visibility: hidden; transition: opacity 0.3s ease;">
                            <?php foreach (['a', 'b', 'c'] as $opt): ?>
                                <a href="quiz_answer.php?room_id=<?php echo $room_id; ?>&quiz_id=<?php echo $current_quiz['id']; ?>&answer=<?php echo $opt; ?>" 
                                   class="quiz-option block p-4 border border-gray-700 rounded bg-gray-900/80 text-left text-gray-200 hover:text-white cursor-pointer shadow-lg">
                                    <span class="text-gold font-bold mr-2 uppercase text-xl"><?php echo strtoupper($opt); ?>.</span>
                                    <span class="text-lg"><?php echo htmlspecialchars($current_quiz['option_' . $opt]); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <script>
                    const questionText = <?php echo json_encode($current_quiz['question']); ?>;
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const profTalking = document.getElementById('prof-talking');
    const profIdle = document.getElementById('prof-idle');
    const typewriterEl = document.getElementById('typewriter-text');
    const answerOptions = document.getElementById('answer-options');
    const completionContent = document.getElementById('completion-content');
    
    function setProfessorState(isTalking) {
        if (profTalking && profIdle) {
            if (isTalking) {
                profTalking.classList.remove('hidden');
                profIdle.classList.add('hidden');
            } else {
                profTalking.classList.add('hidden');
                profIdle.classList.remove('hidden');
            }
        }
    }
    
    function typeText(text, callback) {
        if (!typewriterEl) return;
        
        setProfessorState(true);
        typewriterEl.innerHTML = '';
        let i = 0;
        const speed = 25;
        
        const interval = setInterval(() => {
            if (i < text.length) {
                typewriterEl.innerHTML = text.substring(0, i + 1) + '<span class="typewriter-cursor text-gold">|</span>';
                i++;
            } else {
                clearInterval(interval);
                typewriterEl.innerHTML = text;
                setProfessorState(false);
                if (callback) callback();
            }
        }, speed);
    }
    
    // Start animation
    if (typeof questionText !== 'undefined' && questionText) {
        typeText(questionText, () => {
            if (answerOptions) {
                answerOptions.style.opacity = '1';
                answerOptions.style.visibility = 'visible';
            }
        });
    } else if (typeof completionText !== 'undefined' && completionText) {
        typeText(completionText, () => {
            if (completionContent) {
                completionContent.style.opacity = '1';
            }
        });
    }
});
</script>

