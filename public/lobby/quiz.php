<?php
session_start();
// Check Auth
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
// Check room_id
if (!isset($_GET['room_id'])) {
    header("Location: index.php");
    exit();
}

// Database and Models
require_once '../../app/Config/database.php';
require_once '../../app/Models/Room.php';
require_once '../../app/Models/Quiz.php';

$room_id = intval($_GET['room_id']);
$user_id = $_SESSION['user_id'];

// Fetch Room Info using Model
$room = Room::findById($conn, $room_id);

if (!$room) {
    echo "Room not found.";
    exit();
}

// Check Access (Security)
if ($_SESSION['level'] < $room['min_level']) {
    header("Location: index.php");
    exit();
}

// Fetch Quizzes with user status using Model
$raw_quizzes = Quiz::getForUser($conn, $room_id, $user_id);

$quizzes = [];
$answered_count = 0;
$total_count = 0;
foreach ($raw_quizzes as $row) {
    $row['is_answered'] = $row['is_answered'] > 0;
    if ($row['is_answered']) $answered_count++;
    $total_count++;
    $quizzes[] = $row;
}

include '../header.php';
include '../navbar.php';
?>

<div class="flex-grow container mx-auto px-4 py-8">
    <!-- Back Button & Title -->
    <div class="flex items-center gap-4 mb-8">
        <a href="room.php?id=<?php echo $room_id; ?>" class="btn-museum bg-gray-800 text-white border-gray-600 hover:bg-gold hover:text-black">
            <i class="fas fa-arrow-left mr-2"></i> Back to Room
        </a>
        <div>
            <h1 class="text-3xl text-gold font-serif"><?php echo $room['name']; ?> Quiz</h1>
            <p class="text-gray-400 text-sm">Test your knowledge and earn XP!</p>
        </div>
    </div>

    <!-- Progress -->
    <div class="mb-8 p-4 bg-gray-900 rounded-lg border border-gray-700">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-400 text-sm">Progress</span>
            <span class="text-gold text-sm"><?php echo $answered_count; ?>/<?php echo $total_count; ?> Completed</span>
        </div>
        <div class="w-full bg-gray-800 rounded-full h-2">
            <div class="bg-gold h-2 rounded-full transition-all duration-500" style="width: <?php echo $total_count > 0 ? ($answered_count / $total_count * 100) : 0; ?>%"></div>
        </div>
    </div>

    <?php if (empty($quizzes)): ?>
        <div class="text-center py-20 bg-gray-900/50 rounded-lg border border-dashed border-gray-700">
            <i class="fas fa-question-circle text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-xl text-gray-400 mb-2">No Quizzes Available</h3>
            <p class="text-gray-600">This room doesn't have any quizzes yet.</p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($quizzes as $index => $quiz): ?>
                <?php $is_answered = $quiz['is_answered']; ?>
                <div class="quiz-card museum-card p-6 rounded-lg <?php echo $is_answered ? 'opacity-60' : ''; ?>" 
                     data-quiz-id="<?php echo $quiz['id']; ?>"
                     data-answered="<?php echo $is_answered ? 'true' : 'false'; ?>">
                    
                    <div class="flex items-start gap-4">
                        <!-- Question Number -->
                        <div class="w-10 h-10 rounded-full <?php echo $is_answered ? 'bg-green-900 border-green-500' : 'bg-gray-800 border-gold'; ?> border flex items-center justify-center flex-shrink-0">
                            <?php if ($is_answered): ?>
                                <i class="fas fa-check text-green-500"></i>
                            <?php else: ?>
                                <span class="text-gold font-bold"><?php echo $index + 1; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Question Content -->
                        <div class="flex-grow">
                            <h3 class="text-lg text-white mb-4"><?php echo htmlspecialchars($quiz['question']); ?></h3>
                            
                            <?php if (!$is_answered): ?>
                                <div class="quiz-options space-y-3">
                                    <label class="quiz-option block cursor-pointer group">
                                        <input type="radio" name="quiz_<?php echo $quiz['id']; ?>" value="a" class="hidden">
                                        <div class="p-3 border border-gray-700 rounded-lg bg-gray-800/50 group-hover:border-gold transition flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center text-sm text-gray-400 group-hover:border-gold group-hover:text-gold transition">A</span>
                                            <span class="text-gray-300"><?php echo htmlspecialchars($quiz['option_a']); ?></span>
                                        </div>
                                    </label>
                                    <label class="quiz-option block cursor-pointer group">
                                        <input type="radio" name="quiz_<?php echo $quiz['id']; ?>" value="b" class="hidden">
                                        <div class="p-3 border border-gray-700 rounded-lg bg-gray-800/50 group-hover:border-gold transition flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center text-sm text-gray-400 group-hover:border-gold group-hover:text-gold transition">B</span>
                                            <span class="text-gray-300"><?php echo htmlspecialchars($quiz['option_b']); ?></span>
                                        </div>
                                    </label>
                                    <label class="quiz-option block cursor-pointer group">
                                        <input type="radio" name="quiz_<?php echo $quiz['id']; ?>" value="c" class="hidden">
                                        <div class="p-3 border border-gray-700 rounded-lg bg-gray-800/50 group-hover:border-gold transition flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center text-sm text-gray-400 group-hover:border-gold group-hover:text-gold transition">C</span>
                                            <span class="text-gray-300"><?php echo htmlspecialchars($quiz['option_c']); ?></span>
                                        </div>
                                    </label>
                                </div>
                                
                                <button class="btn-submit-quiz btn-museum mt-4" data-quiz-id="<?php echo $quiz['id']; ?>">
                                    Submit Answer
                                </button>
                            <?php else: ?>
                                <div class="flex items-center gap-2 text-green-500 text-sm">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Already answered (+<?php echo $quiz['xp_reward']; ?> XP)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- XP Badge -->
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs text-gray-500 uppercase tracking-wider">Reward</span>
                            <div class="text-gold font-bold">+<?php echo $quiz['xp_reward']; ?> XP</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Handle option selection visual feedback
    document.querySelectorAll('.quiz-option input').forEach(input => {
        input.addEventListener('change', function() {
            const card = this.closest('.quiz-card');
            card.querySelectorAll('.quiz-option > div').forEach(opt => {
                opt.classList.remove('border-gold', 'bg-gold/10');
                opt.classList.add('border-gray-700');
            });
            this.nextElementSibling.classList.remove('border-gray-700');
            this.nextElementSibling.classList.add('border-gold', 'bg-gold/10');
        });
    });

    // Handle submit
    document.querySelectorAll('.btn-submit-quiz').forEach(btn => {
        btn.addEventListener('click', function() {
            const quizId = this.dataset.quizId;
            const card = this.closest('.quiz-card');
            const selected = card.querySelector(`input[name="quiz_${quizId}"]:checked`);
            
            if (!selected) {
                alert('Please select an answer!');
                return;
            }

            const formData = new FormData();
            formData.append('quiz_id', quizId);
            formData.append('answer', selected.value);

            fetch('../../app/Handlers/quiz_submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.correct) {
                        alert(`Correct! +${data.xp_reward} XP`);
                        if (data.leveled_up) {
                            alert(`LEVEL UP! You are now Level ${data.new_level}`);
                        }
                    } else {
                        alert('Incorrect answer. Better luck next time!');
                    }
                    
                    // Check for hidden artifact unlock
                    if (data.hidden_artifact_unlocked && data.hidden_artifact) {
                        alert(`HIDDEN ARTIFACT UNLOCKED!\n\n"${data.hidden_artifact.name}"\n\n${data.hidden_artifact.desc}\n\n+${data.hidden_artifact.xp} Bonus XP!`);
                    }
                    
                    location.reload(); // Reload to update UI
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>


