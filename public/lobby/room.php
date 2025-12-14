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

// Database and Models
require_once '../../app/Config/database.php';
require_once '../../app/Models/Room.php';

$room_id = intval($_GET['id']);
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

// Always show professor guide on room entry
$first_visit = true; // Always show the guide modal

// Fetch Artifacts & Collection Status using Model
$raw_artifacts = Room::getArtifacts($conn, $room_id, $user_id);

// Default positions for fallback
$fallback_positions = [
    ['top' => '60%', 'left' => '20%'],
    ['top' => '70%', 'left' => '50%'],
    ['top' => '55%', 'left' => '80%'],
    ['top' => '40%', 'left' => '30%'],
    ['top' => '50%', 'left' => '60%'],
];

$artifacts = [];
$collected_count = 0;
$i = 0;
foreach ($raw_artifacts as $row) {
    // specific positioning from DB or fallback
    if ($row['position_top'] && $row['position_left']) {
        $row['top'] = $row['position_top'];
        $row['left'] = $row['position_left'];
    } else {
        $pos = $fallback_positions[$i % count($fallback_positions)];
        $row['top'] = $pos['top'];
        $row['left'] = $pos['left'];
    }
    
    if ($row['is_collected'] > 0) $collected_count++;
    $artifacts[] = $row;
    $i++;
}

$total_artifacts = count($artifacts);
$all_collected = ($collected_count > 0 && $collected_count >= $total_artifacts);

// Check hidden artifact status using Model
$hidden_artifact_unlocked = Room::isHiddenArtifactUnlocked($conn, $room_id, $user_id);
$hidden_artifact = Room::getHiddenArtifact($conn, $room_id);
if ($hidden_artifact) {
    $hidden_artifact['unlocked'] = $hidden_artifact_unlocked;
}

// Clean description for JS usage
$desc = addslashes($room['description']);
$room_name = addslashes($room['name']);

include '../header.php';
include '../navbar.php';

// Determine room music based on room name
$room_music_map = [
    'Medieval Hall' => 'medieval.mp3',
    'Renaissance Gallery' => 'Renaissance.mp3',
    'Baroque Palace' => 'baroque.mp3',
    'Royal Archives' => 'archive.mp3'
];
$room_music = $room_music_map[$room['name']] ?? 'lobby.mp3';
?>

<!-- Room-Specific Background Music -->
<audio id="room-music" loop preload="auto" style="display: none;">
    <source src="/project-akhir/public/assets/music/<?php echo $room_music; ?>" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<script>
// Room Music Control
document.addEventListener('DOMContentLoaded', function() {
    const lobbyMusic = document.getElementById('bg-music');
    const roomMusic = document.getElementById('room-music');
    
    // Stop lobby music if it's playing
    if (lobbyMusic) {
        lobbyMusic.pause();
        lobbyMusic.currentTime = 0;
    }
    
    // Play room music
    if (roomMusic) {
        roomMusic.volume = 0.3; // 30% volume
        
        const playPromise = roomMusic.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('Room music started: <?php echo $room_music; ?>');
            }).catch(error => {
                console.log('Room music autoplay prevented:', error);
                
                // Fallback: play on first user interaction
                document.body.addEventListener('click', function startRoomMusic() {
                    roomMusic.play().then(() => {
                        console.log('Room music started after interaction');
                    }).catch(e => console.log('Room music play failed:', e));
                    document.body.removeEventListener('click', startRoomMusic);
                }, { once: true });
            });
        }
    }
});
</script>

<!-- Room Intro Guide Modal - Improved Storytelling Style -->
<div id="guide-modal" class="fixed inset-0 z-[100] flex flex-col justify-end bg-black/80 backdrop-blur-md <?php echo $first_visit ? '' : 'hidden'; ?>">
    
    <!-- Professor Aldric - Direct child for proper z-index -->
    <div id="professor-container" class="absolute bottom-0 left-0 md:left-[-2rem] h-[125vh] w-[400px] z-30 pointer-events-none transition-all duration-700 ease-out flex items-end">
        <img id="prof-gif" src="/project-akhir/public/assets/img/professor.gif" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)] hidden">
        <img id="prof-idle" src="/project-akhir/public/assets/img/professor-diam.png" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)]">
    </div>
    
    <!-- Dialogue Area - Bottom -->
    <div class="relative w-full bg-black/95 border-t-2 border-gold z-20 p-6 pb-8 md:pl-[350px]">
        <!-- Guide Name Tag -->
        <div class="absolute -top-8 left-6 md:left-[350px] bg-gold text-black px-4 py-1 font-serif font-bold text-lg rounded-t shadow-[0_0_15px_rgba(197,160,89,0.5)]">
            Professor Aldric
        </div>
        
        <!-- Typewriter Text Container -->
        <div class="min-h-[80px] font-serif text-lg md:text-xl text-gray-200 leading-relaxed drop-shadow-md relative">
            <span id="typewriter-text"></span><span class="animate-pulse text-gold">|</span>
        </div>
        
        <!-- Controls -->
        <div class="flex justify-between items-center mt-6 border-t border-gray-800 pt-4">
            <div class="flex gap-2" id="dialog-dots">
                <!-- Dots generated by JS -->
            </div>
            
            <button id="btn-next" class="btn-museum bg-gold/10 hover:bg-gold text-gold hover:text-black">
                Next <i class="fas fa-chevron-right ml-2"></i>
            </button>
        </div>
        
        <!-- Skip Button -->
        <button id="btn-skip" class="absolute top-4 right-4 text-gray-500 hover:text-white text-sm uppercase tracking-wider">
            Skip Intro <i class="fas fa-forward ml-1"></i>
        </button>
    </div>
</div>

<!-- Info Button to reopen guide (fixed position) -->
<button id="btn-info" class="fixed top-20 right-4 z-[90] w-12 h-12 rounded-full bg-black/70 border-2 border-gold text-gold hover:bg-gold hover:text-black transition flex items-center justify-center shadow-lg">
    <i class="fas fa-book-open"></i>
</button>

<div class="relative w-full h-[calc(100vh-64px)] overflow-hidden bg-black">
    <!-- Navigation Buttons -->
    <div class="absolute top-4 left-4 z-30 flex gap-3">
        <a href="index.php" class="btn-museum bg-black/50 text-white border-white/30 hover:bg-gold hover:text-black">
            <i class="fas fa-arrow-left mr-2"></i> Back to Lobby
        </a>
    </div>
    
    <!-- Room Progress -->
    <div class="absolute top-4 right-20 z-30 flex items-center gap-2 bg-black/50 border border-gray-700 rounded-full px-4 py-2">
        <i class="fas fa-gem text-gold"></i>
        <span class="text-white text-sm"><?php echo $collected_count; ?>/<?php echo $total_artifacts; ?></span>
    </div>

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
            <!-- Artifact Item - Fixed position, pulsing glow only -->
            <div class="absolute cursor-pointer transform group artifact-item"
                 data-id="<?php echo $artifact['id']; ?>"
                 data-name="<?php echo htmlspecialchars($artifact['name']); ?>"
                 data-desc="<?php echo htmlspecialchars($artifact['description']); ?>"
                 data-image="<?php echo htmlspecialchars($artifact['image_url']); ?>"
                 data-collected="<?php echo $is_collected ? 'true' : 'false'; ?>"
                 style="top: <?php echo $artifact['top']; ?>; left: <?php echo $artifact['left']; ?>;">
                    
                <!-- Artifact Container -->
                <div class="relative w-12 h-12 flex items-center justify-center transition duration-500 ease-out">
                    
                    <!-- 1. Outer Diffuse Glow - Large pulsing aurora -->
                    <div class="absolute inset-[-5px] rounded-full bg-gold/20 blur-xl animate-pulse group-hover:opacity-100 group-hover:bg-gold/60 group-hover:blur-2xl transition duration-500"></div>
                    
                    <!-- 2. Inner Focused Glow - Stronger light -->
                    <div class="absolute inset-1 rounded-full bg-gold/40 blur-md group-hover:bg-gold/80 group-hover:bg-opacity-100 transition duration-300"></div>
                    
                    <!-- 3. Core Light - The bright source -->
                    <div class="absolute w-1 h-1 bg-white rounded-full shadow-[0_0_10px_rgba(255,255,255,0.8)] group-hover:w-2 group-hover:h-2 group-hover:shadow-[0_0_30px_rgba(255,255,255,1)] transition-all duration-300"></div>

                </div>
                <!-- Tooltip removed for mystery -->
            </div>
        <?php endforeach; ?>
    </div>

    <?php include '../artifact_detail.php'; ?>
    
    <!-- Hidden Chest (Shows after all artifacts collected) - Always rendered for dynamic trigger -->
    <div id="hidden-chest" class="absolute cursor-pointer z-20 hidden transform hover:scale-110 transition-all duration-300"
         style="top: 65%; left: 48%;"
         data-artifact-name="<?php echo $hidden_artifact ? htmlspecialchars($hidden_artifact['name']) : 'Hidden Artifact'; ?>"
         data-artifact-desc="<?php echo $hidden_artifact ? htmlspecialchars($hidden_artifact['description']) : 'A mysterious artifact awaits...'; ?>"
         data-artifact-image="<?php echo $hidden_artifact ? htmlspecialchars($hidden_artifact['image']) : ''; ?>"
         data-unlocked="<?php echo $hidden_artifact_unlocked ? 'true' : 'false'; ?>">
        <!-- Chest Glow -->
        <div class="absolute inset-[-15px] rounded-lg bg-amber-400/40 blur-xl animate-pulse"></div>
        <div class="absolute inset-[-8px] rounded-lg bg-yellow-300/50 blur-md"></div>
        <!-- Chest Image -->
        <img src="/project-akhir/public/assets/img/artifacts/chest.png" alt="Hidden Chest" 
             class="relative z-10 w-20 h-20 object-contain drop-shadow-[0_0_20px_rgba(251,191,36,0.8)]">
    </div>
    
    <!-- Mystery Artifact Modal (Shows when chest is clicked) -->
    <div id="mystery-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300">
        <div class="relative px-20 py-12 text-center transform scale-90 transition-transform duration-300 bg-contain bg-center bg-no-repeat min-w-[550px] min-h-[650px] flex flex-col items-center justify-center" id="mystery-content" style="background-image: url('/project-akhir/public/assets/img/elements/old-paper.png');">
            
            <!-- Close X Button -->
            <button id="mystery-close" class="absolute top-12 right-12 w-8 h-8 flex items-center justify-center rounded-full bg-amber-900/30 hover:bg-amber-900/50 text-amber-900 hover:text-amber-800 text-lg font-bold transition-all z-20">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Content -->
            <div class="relative z-10 max-w-[350px] mx-auto">
                <h3 class="text-lg text-amber-900 font-serif font-bold mb-3 drop-shadow-sm" id="mystery-title">??? Unknown Artifact ???</h3>
                
                <!-- Mystery Image -->
                <div class="mb-4 flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-[-20px] rounded-full bg-gradient-to-r from-amber-400/30 via-yellow-300/40 to-amber-400/30 blur-2xl animate-pulse"></div>
                        <div class="absolute inset-[-12px] rounded-full bg-yellow-200/30 blur-xl"></div>
                        <div id="mystery-image-container" class="relative z-10 w-40 h-40 flex items-center justify-center text-7xl text-amber-700">
                            <i class="fas fa-question"></i>
                        </div>
                    </div>
                </div>
                
                <p class="text-amber-800 mb-5 text-sm leading-relaxed px-4" id="mystery-desc" style="font-family: 'Garamond', 'Georgia', 'Times New Roman', serif;">
                    This ancient artifact remains shrouded in mystery. Only those who prove their knowledge can unlock its secrets...
                </p>
                
                <div class="flex justify-center">
                    <button id="mystery-collect" class="bg-amber-800 hover:bg-amber-900 text-amber-100 font-bold py-2 px-6 text-sm rounded-lg transition shadow-lg">
                        <i class="fas fa-key mr-2"></i> Collect?
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quiz Dialogue Modal (Professor asks questions) -->
    <div id="quiz-dialogue-modal" class="fixed inset-0 z-[100] flex flex-col justify-end bg-black/90 hidden">
        <!-- Professor Container -->
        <div id="quiz-professor-container" class="absolute bottom-0 left-0 md:left-[-2rem] h-[125vh] w-[400px] z-30 pointer-events-none transition-all duration-700 ease-out flex items-end">
            <img id="quiz-prof-gif" src="/project-akhir/public/assets/img/professor.gif" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)] hidden">
            <img id="quiz-prof-idle" src="/project-akhir/public/assets/img/professor-diam.png" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)]">
        </div>
        
        <!-- Dialogue Area -->
        <div class="relative w-full bg-black/95 border-t-2 border-gold z-20 p-6 pb-8 md:pl-[350px]">
            <div class="absolute -top-8 left-6 md:left-[350px] bg-gold text-black px-4 py-1 font-serif font-bold text-lg rounded-t shadow-[0_0_15px_rgba(197,160,89,0.5)]">
                Professor Aldric
            </div>
            
            <!-- Question Text -->
            <div class="min-h-[60px] font-serif text-lg md:text-xl text-gray-200 leading-relaxed drop-shadow-md relative mb-4">
                <span id="quiz-question-text"></span><span class="animate-pulse text-gold">|</span>
            </div>
            
            <!-- Answer Options Container -->
            <div id="quiz-options-container" class="flex flex-col gap-3 mt-4 hidden">
                <button class="quiz-option-btn" data-option="a"></button>
                <button class="quiz-option-btn" data-option="b"></button>
                <button class="quiz-option-btn" data-option="c"></button>
            </div>
            
            <!-- Progress & Score -->
            <div class="flex justify-between items-center mt-6 border-t border-gray-800 pt-4">
                <div class="text-gray-400 text-sm">
                    Question <span id="quiz-current-num">1</span>/<span id="quiz-total-num">3</span>
                </div>
                <div class="text-gold text-sm">
                    Score: <span id="quiz-score">0</span>/<span id="quiz-total-questions">3</span>
                </div>
                <button id="quiz-skip" class="text-gray-500 hover:text-white text-sm uppercase tracking-wider">
                    Abort Quiz <i class="fas fa-times ml-1"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Congratulation Dialog (Shows when all artifacts collected) - Always rendered for dynamic trigger -->
    <div id="congrats-modal" class="fixed inset-0 z-[100] hidden flex flex-col justify-end bg-black/90 opacity-0 transition-opacity duration-300">
        <!-- Professor Container - Same as intro -->
        <div id="congrats-professor-container" class="absolute bottom-0 left-0 md:left-[-2rem] h-[125vh] w-[400px] z-30 pointer-events-none transition-all duration-700 ease-out flex items-end">
            <img id="congrats-prof-gif" src="/project-akhir/public/assets/img/professor.gif" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)] hidden">
            <img id="congrats-prof-idle" src="/project-akhir/public/assets/img/professor-diam.png" alt="Professor Aldric" class="h-auto max-h-full w-auto object-contain object-bottom drop-shadow-[0_0_50px_rgba(197,160,89,0.3)]">
        </div>
        
        <!-- Dialogue Area - Bottom (Same as intro) -->
        <div class="relative w-full bg-black/95 border-t-2 border-gold z-20 p-6 pb-8 md:pl-[350px]">
            <!-- Guide Name Tag -->
            <div class="absolute -top-8 left-6 md:left-[350px] bg-gold text-black px-4 py-1 font-serif font-bold text-lg rounded-t shadow-[0_0_15px_rgba(197,160,89,0.5)]">
                Professor Aldric
            </div>
            
            <!-- Typewriter Text Container -->
            <div class="min-h-[80px] font-serif text-lg md:text-xl text-gray-200 leading-relaxed drop-shadow-md relative">
                <span id="congrats-typewriter"></span><span class="animate-pulse text-gold">|</span>
            </div>
            
            <!-- Controls -->
            <div class="flex justify-between items-center mt-6 border-t border-gray-800 pt-4">
                <div class="flex gap-2" id="congrats-dots">
                    <!-- Dots generated by JS -->
                </div>
                
                <!-- Next Button -->
                <button id="congrats-next" class="btn-museum bg-gold/10 hover:bg-gold text-gold hover:text-black">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </button>
                
                <!-- Action Buttons (hidden initially, shown at last message) -->
                <div id="congrats-actions" class="hidden gap-4">
                    <button id="congrats-explore" class="btn-museum bg-gold hover:bg-gold-hover text-black">
                        <i class="fas fa-search mr-2"></i> Find The Chest!
                    </button>
                </div>
            </div>
            
            <!-- Skip Button -->
            <button id="congrats-skip" class="absolute top-4 right-4 text-gray-500 hover:text-white text-sm uppercase tracking-wider">
                Skip <i class="fas fa-forward ml-1"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const guideModal = document.getElementById('guide-modal');
    const professorContainer = document.getElementById('professor-container');
    const profGif = document.getElementById('prof-gif');
    const profIdle = document.getElementById('prof-idle');
    const typewriterText = document.getElementById('typewriter-text');
    const btnNext = document.getElementById('btn-next');
    const btnSkip = document.getElementById('btn-skip');
    const btnInfo = document.getElementById('btn-info');
    const dialogDotsContainer = document.getElementById('dialog-dots');
    
    function restartProfessorAnimation() {
        // Simple fade animation
        professorContainer.style.opacity = '0';
        professorContainer.style.transform = 'translateX(-50px)';
        
        setTimeout(() => {
            professorContainer.style.opacity = '1';
            professorContainer.style.transform = 'translateX(0)';
        }, 100);
    }

    function setProfessorState(isTalking) {
        if (isTalking) {
            profGif.classList.remove('hidden');
            profIdle.classList.add('hidden');
        } else {
            profGif.classList.add('hidden');
            profIdle.classList.remove('hidden');
        }
    }
    
    // Dialog Data - Use database dialogs if available, otherwise fallback to defaults
    <?php 
    $db_dialogs = json_decode($room['professor_dialogs'] ?? '[]', true);
    $default_dialogs = [
        "Welcome, young explorer! I am Professor Aldric.",
        "You have entered the " . addslashes($room['name']) . ". A magnificent place, isn't it?",
        addslashes($room['description']),
        "There are " . $total_artifacts . " artifacts hidden here. Look for the glowing markers.",
        "Collect them all to gain knowledge and experience. Good luck!"
    ];
    $final_dialogs = !empty($db_dialogs) ? $db_dialogs : $default_dialogs;
    ?>
    const messages = <?php echo json_encode($final_dialogs); ?>;
    
    let currentStep = 0;
    let isTyping = false;
    let typeInterval;
    
    // Create dots
    messages.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = `w-3 h-3 rounded-full ${index === 0 ? 'bg-gold' : 'bg-gray-600'} transition-colors duration-300`;
        dialogDotsContainer.appendChild(dot);
    });
    
    const dots = dialogDotsContainer.querySelectorAll('div');
    
    function updateDots(index) {
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-gold', i === index);
            dot.classList.toggle('bg-gray-600', i !== index);
        });
    }

    function typeText(text, callback) {
        isTyping = true;
        setProfessorState(true); // Talking
        typewriterText.innerHTML = "";
        let i = 0;
        clearInterval(typeInterval);
        
        typeInterval = setInterval(() => {
            if (i < text.length) {
                typewriterText.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
                isTyping = false;
                setProfessorState(false); // Idle
                if (callback) callback();
            }
        }, 30); // Speed: 30ms per char
    }
    
    function showMessage(index) {
        if (index >= messages.length) {
            closeGuide();
            return;
        }

        // Restart Professor Animation on every step
        restartProfessorAnimation();
        
        updateDots(index);
        
        if (index === messages.length - 1) {
            btnNext.innerHTML = 'Start Exploring <i class="fas fa-play ml-2"></i>';
        } else {
            btnNext.innerHTML = 'Next <i class="fas fa-chevron-right ml-2"></i>';
        }
        
        typeText(messages[index]);
    }
    
    function instantFinish() {
        clearInterval(typeInterval);
        typewriterText.textContent = messages[currentStep];
        isTyping = false;
        setProfessorState(false); // Idle
    }
    
    function nextStep() {
        if (isTyping) {
            instantFinish();
        } else {
            currentStep++;
            showMessage(currentStep);
        }
    }
    
    function closeGuide() {
        guideModal.classList.add('opacity-0');
        setTimeout(() => guideModal.classList.add('hidden'), 500);
    }
    
    function openGuide() {
        currentStep = 0;
        guideModal.classList.remove('hidden');
        // Small delay to allow fade-in transition if added later
        setTimeout(() => {
            guideModal.classList.remove('opacity-0');
            showMessage(0);
        }, 10);
    }
    
    // Event Listeners
    btnNext.addEventListener('click', nextStep);
    
    // Allow clicking on text area to speed up
    const textArea = guideModal.querySelector('.min-h-\\[80px\\]');
    if (textArea) {
        textArea.addEventListener('click', () => {
            if (isTyping) instantFinish();
        });
    }

    btnSkip.addEventListener('click', closeGuide);
    btnInfo.addEventListener('click', openGuide);
    
    // Enter key to advance dialog
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            if (guideModal && !guideModal.classList.contains('hidden')) {
                e.preventDefault();
                nextStep();
            } else if (congratsModal && !congratsModal.classList.contains('hidden')) {
                // Only if next button is visible (not on Yes/No step)
                if (congratsNext && !congratsNext.classList.contains('hidden')) {
                    e.preventDefault();
                    nextCongratsStep();
                }
            }
        }
    });
    
    // Start if first visit or manually opened
    if (!guideModal.classList.contains('hidden')) {
        showMessage(0);
    }
    
    // Add animations styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    `;
    document.head.appendChild(style);
    
    // Congrats Dialog Logic (Typewriter style)
    const congratsModal = document.getElementById('congrats-modal');
    const congratsTypewriter = document.getElementById('congrats-typewriter');
    const congratsDots = document.getElementById('congrats-dots');
    const congratsNext = document.getElementById('congrats-next');
    const congratsActions = document.getElementById('congrats-actions');
    const congratsNo = document.getElementById('congrats-no');
    
    // Check if should show congrats
    const allCollected = <?php echo $all_collected ? 'true' : 'false'; ?>;
    const congratsShown = sessionStorage.getItem('congrats_shown_room_<?php echo $room_id; ?>');
    
    // Congrats messages
    const congratsMessages = [
        "🎉 Incredible work, young explorer!",
        "You have successfully collected all <?php echo $total_artifacts; ?> artifacts in the <?php echo addslashes($room['name']); ?>!",
        "But wait... I sense something else hidden in this chamber...",
        "An ancient chest containing a mysterious artifact! Its name remains unknown...",
        "Look around the room carefully. The chest will reveal itself to those who seek it."
    ];
    
    let congratsStep = 0;
    let congratsTyping = false;
    let congratsTypeInterval;
    
    // Create dots
    if (congratsDots) {
        congratsMessages.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.className = `w-2 h-2 rounded-full ${index === 0 ? 'bg-gold' : 'bg-gray-600'} transition-colors duration-300`;
            congratsDots.appendChild(dot);
        });
    }
    
    const congratsDotElements = congratsDots ? congratsDots.querySelectorAll('div') : [];
    
    function updateCongratsDots(index) {
        congratsDotElements.forEach((dot, i) => {
            dot.className = `w-2 h-2 rounded-full ${i <= index ? 'bg-gold' : 'bg-gray-600'} transition-colors duration-300`;
        });
    }
    
    const congratsProfGif = document.getElementById('congrats-prof-gif');
    const congratsProfIdle = document.getElementById('congrats-prof-idle');

    function setCongratsProfessorState(isTalking) {
        if (congratsProfGif && congratsProfIdle) {
            if (isTalking) {
                congratsProfGif.classList.remove('hidden');
                congratsProfIdle.classList.add('hidden');
            } else {
                congratsProfGif.classList.add('hidden');
                congratsProfIdle.classList.remove('hidden');
            }
        }
    }

    function typeCongratsMessage(text, callback) {
        congratsTyping = true;
        setCongratsProfessorState(true); // Start Talking
        let i = 0;
        congratsTypewriter.innerHTML = '';
        
        congratsTypeInterval = setInterval(() => {
            if (i < text.length) {
                congratsTypewriter.innerHTML += text.charAt(i);
                i++;
            } else {
                clearInterval(congratsTypeInterval);
                congratsTyping = false;
                setCongratsProfessorState(false); // Stop Talking (Idle)
                if (callback) callback();
            }
        }, 30);
    }
    
    // Make showCongratsMessage global so it can be called from artifact_detail.php
    window.showCongratsMessage = function(index) {
        if (index >= congratsMessages.length) {
            // Show Yes/No buttons on last message
            congratsNext.classList.add('hidden');
            congratsActions.classList.remove('hidden');
            setCongratsProfessorState(false); // Ensure idle
            return;
        }
        
        congratsStep = index;
        updateCongratsDots(index);
        typeCongratsMessage(congratsMessages[index]);
    };
    
    function nextCongratsStep() {
        if (congratsTyping) {
            clearInterval(congratsTypeInterval);
            congratsTypewriter.innerHTML = congratsMessages[congratsStep];
            congratsTyping = false;
            setCongratsProfessorState(false); // Stop Talking (Idle)
        } else {
            showCongratsMessage(congratsStep + 1);
        }
    }
    
    // Make showCongratsModal global so it can be triggered from artifact_detail.php
    window.showCongratsModal = function() {
        if (congratsModal) {
            congratsModal.classList.remove('hidden');
            setTimeout(() => {
                congratsModal.classList.remove('opacity-0');
                window.showCongratsMessage(0);
            }, 100);
            sessionStorage.setItem('congrats_shown_room_<?php echo $room_id; ?>', 'true');
        }
    };
    
    // Make hideCongratsModal global
    window.hideCongratsModal = function() {
        if (congratsModal) {
            congratsModal.classList.add('hidden');
            congratsModal.classList.add('opacity-0');
        }
    };
    
    if (congratsNext) {
        congratsNext.addEventListener('click', nextCongratsStep);
    }
    
    if (congratsNo) {
        congratsNo.addEventListener('click', () => {
            hideCongratsModal();
            // Still show the chest even if user clicks "Maybe later"
            // The chest should appear once all artifacts are collected, regardless of button choice
            window.showHiddenChest();
        });
    }
    
    // Skip button
    const congratsSkip = document.getElementById('congrats-skip');
    if (congratsSkip) {
        congratsSkip.addEventListener('click', hideCongratsModal);
    }
    // Show congrats modal after guide is closed (if all collected)
    console.log('DEBUG: allCollected=', allCollected, 'congratsShown=', congratsShown, 'modal=', congratsModal);
    
    if (allCollected && !congratsShown) {
        // If guide is shown, wait for it to close
        if (guideModal && !guideModal.classList.contains('hidden')) {
            // Modify closeGuide to also trigger congrats
            const originalCloseGuide = closeGuide;
            closeGuide = function() {
                originalCloseGuide();
                setTimeout(showCongratsModal, 500);
            };
        } else {
            // Guide already closed, show congrats after short delay
            setTimeout(showCongratsModal, 1000);
        }
    }
    
    // ========== HIDDEN CHEST & QUIZ DIALOGUE SYSTEM ==========
    const hiddenChest = document.getElementById('hidden-chest');
    const mysteryModal = document.getElementById('mystery-modal');
    const mysteryContent = document.getElementById('mystery-content');
    const mysteryClose = document.getElementById('mystery-close');
    const mysteryCollect = document.getElementById('mystery-collect');
    const mysteryTitle = document.getElementById('mystery-title');
    const mysteryDesc = document.getElementById('mystery-desc');
    const mysteryImageContainer = document.getElementById('mystery-image-container');
    
    const quizDialogueModal = document.getElementById('quiz-dialogue-modal');
    const quizQuestionText = document.getElementById('quiz-question-text');
    const quizOptionsContainer = document.getElementById('quiz-options-container');
    const quizCurrentNum = document.getElementById('quiz-current-num');
    const quizTotalNum = document.getElementById('quiz-total-num');
    const quizScore = document.getElementById('quiz-score');
    const quizTotalQuestions = document.getElementById('quiz-total-questions');
    const quizSkip = document.getElementById('quiz-skip');
    const quizProfGif = document.getElementById('quiz-prof-gif');
    const quizProfIdle = document.getElementById('quiz-prof-idle');
    
    const congratsExplore = document.getElementById('congrats-explore');
    
    // Quiz data - will be fetched from database
    let quizQuestions = [];
    let currentQuizIndex = 0;
    let correctAnswers = 0;
    let quizTyping = false;
    let quizTypeInterval;
    
    // Show chest after congrats modal closes - make it global
    window.showHiddenChest = function() {
        let chest = document.getElementById('hidden-chest');
        
        // If chest doesn't exist, create it dynamically
        if (!chest && window.hiddenArtifactData) {
            const artifactContainer = document.getElementById('artifact-container');
            if (artifactContainer) {
                chest = document.createElement('div');
                chest.id = 'hidden-chest';
                chest.className = 'absolute cursor-pointer z-20 transform hover:scale-110 transition-all duration-300';
                chest.style.cssText = 'top: 65%; left: 48%;';
                chest.dataset.artifactName = window.hiddenArtifactData.name || 'Hidden Artifact';
                chest.dataset.artifactDesc = window.hiddenArtifactData.description || 'A mysterious artifact.';
                chest.dataset.artifactImage = window.hiddenArtifactData.image || '';
                chest.dataset.unlocked = window.hiddenArtifactData.unlocked ? 'true' : 'false';
                
                chest.innerHTML = `
                    <div class="absolute inset-[-15px] rounded-lg bg-amber-400/40 blur-xl animate-pulse"></div>
                    <div class="absolute inset-[-8px] rounded-lg bg-yellow-300/50 blur-md"></div>
                    <img src="/project-akhir/public/assets/img/artifacts/chest.png" alt="Hidden Chest" 
                         class="relative z-10 w-20 h-20 object-contain drop-shadow-[0_0_20px_rgba(251,191,36,0.8)]">
                `;
                
                artifactContainer.parentElement.appendChild(chest);
                
                // Add click handler for the new chest
                chest.addEventListener('click', handleChestClick);
            }
        }
        
        if (chest) {
            chest.classList.remove('hidden');
            chest.style.animation = 'fadeInUp 0.8s ease-out forwards';
        }
    };
    
    // Handle chest click - extracted to separate function
    function handleChestClick() {
        const chest = document.getElementById('hidden-chest');
        if (!chest) return;
        
        const unlocked = chest.dataset.unlocked === 'true';
        
        if (unlocked) {
            // Already unlocked - show actual artifact
            mysteryTitle.textContent = chest.dataset.artifactName;
            mysteryDesc.textContent = chest.dataset.artifactDesc;
            if (chest.dataset.artifactImage) {
                mysteryImageContainer.innerHTML = `<img src="${chest.dataset.artifactImage}" alt="Hidden Artifact" class="w-full h-full object-contain">`;
            }
            mysteryCollect.innerHTML = '<i class="fas fa-check mr-2"></i> Already Collected!';
            mysteryCollect.disabled = true;
            mysteryCollect.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            // Not unlocked - show mystery
            mysteryTitle.textContent = '??? Unknown Artifact ???';
            mysteryDesc.textContent = 'This ancient artifact remains shrouded in mystery. Only those who prove their knowledge can unlock its secrets...';
            mysteryImageContainer.innerHTML = '<i class="fas fa-question"></i>';
            mysteryCollect.innerHTML = '<i class="fas fa-key mr-2"></i> Collect?';
            mysteryCollect.disabled = false;
            mysteryCollect.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        mysteryModal.classList.remove('hidden');
        setTimeout(() => {
            mysteryModal.classList.remove('opacity-0');
            mysteryContent.classList.remove('scale-90');
            mysteryContent.classList.add('scale-100');
        }, 10);
    }
    
    // "Find The Chest!" button handler
    if (congratsExplore) {
        congratsExplore.addEventListener('click', () => {
            hideCongratsModal();
            window.showHiddenChest();
        });
    }
    
    // Chest click - show mystery modal
    if (hiddenChest) {
        hiddenChest.addEventListener('click', handleChestClick);
    }
    
    // Close mystery modal
    if (mysteryClose) {
        mysteryClose.addEventListener('click', () => {
            mysteryModal.classList.add('opacity-0');
            mysteryContent.classList.remove('scale-100');
            mysteryContent.classList.add('scale-90');
            setTimeout(() => mysteryModal.classList.add('hidden'), 300);
        });
    }
    
    // Click outside mystery modal to close
    if (mysteryModal) {
        mysteryModal.addEventListener('click', (e) => {
            if (e.target === mysteryModal) {
                mysteryClose.click();
            }
        });
    }
    
    // "Collect?" button - start quiz dialogue
    if (mysteryCollect) {
        mysteryCollect.addEventListener('click', () => {
            if (mysteryCollect.disabled) return;
            
            // Close mystery modal
            mysteryModal.classList.add('hidden');
            
            // Fetch quiz questions and start dialogue
            fetchAndStartQuiz();
        });
    }
    
    // Fetch quiz questions from server
    async function fetchAndStartQuiz() {
        try {
            const response = await fetch(`../../app/Handlers/get_quiz.php?room_id=<?php echo $room_id; ?>`);
            const data = await response.json();
            
            if (data.success && data.questions.length > 0) {
                quizQuestions = data.questions;
                currentQuizIndex = 0;
                correctAnswers = 0;
                startQuizDialogue();
            } else {
                alert('No quiz questions available for this room.');
            }
        } catch (error) {
            console.error('Failed to fetch quiz:', error);
            alert('Failed to load quiz. Please try again.');
        }
    }
    
    // Start quiz dialogue
    function startQuizDialogue() {
        quizDialogueModal.classList.remove('hidden');
        quizTotalNum.textContent = quizQuestions.length;
        quizTotalQuestions.textContent = quizQuestions.length;
        quizScore.textContent = '0';
        
        showQuizQuestion(0);
    }
    
    // Quiz professor animation
    function setQuizProfState(isTalking) {
        if (isTalking) {
            quizProfGif.classList.remove('hidden');
            quizProfIdle.classList.add('hidden');
        } else {
            quizProfGif.classList.add('hidden');
            quizProfIdle.classList.remove('hidden');
        }
    }
    
    // Type quiz question text
    function typeQuizText(text, callback) {
        quizTyping = true;
        setQuizProfState(true);
        quizOptionsContainer.classList.add('hidden');
        
        let i = 0;
        quizQuestionText.innerHTML = '';
        
        quizTypeInterval = setInterval(() => {
            if (i < text.length) {
                quizQuestionText.innerHTML += text.charAt(i);
                i++;
            } else {
                clearInterval(quizTypeInterval);
                quizTyping = false;
                setQuizProfState(false);
                if (callback) callback();
            }
        }, 30);
    }
    
    // Show quiz question
    function showQuizQuestion(index) {
        if (index >= quizQuestions.length) {
            showQuizResult();
            return;
        }
        
        const q = quizQuestions[index];
        quizCurrentNum.textContent = index + 1;
        
        // Type the question
        typeQuizText(`Question ${index + 1}: ${q.question}`, () => {
            // Show options after typing
            const optionBtns = quizOptionsContainer.querySelectorAll('.quiz-option-btn');
            optionBtns[0].textContent = `A: ${q.option_a}`;
            optionBtns[0].dataset.option = 'a';
            optionBtns[1].textContent = `B: ${q.option_b}`;
            optionBtns[1].dataset.option = 'b';
            optionBtns[2].textContent = `C: ${q.option_c}`;
            optionBtns[2].dataset.option = 'c';
            
            // Style options
            optionBtns.forEach(btn => {
                btn.className = 'quiz-option-btn p-3 border border-gray-600 rounded-lg bg-gray-800/50 hover:border-gold hover:bg-gold/10 text-gray-300 text-left transition cursor-pointer';
                btn.disabled = false;
            });
            
            quizOptionsContainer.classList.remove('hidden');
        });
    }
    
    // Option button styling
    const style2 = document.createElement('style');
    style2.textContent = `
        .quiz-option-btn:hover {
            border-color: #c5a059;
            background: rgba(197, 160, 89, 0.1);
        }
        .quiz-option-btn.correct {
            border-color: #22c55e !important;
            background: rgba(34, 197, 94, 0.2) !important;
            color: #22c55e !important;
        }
        .quiz-option-btn.incorrect {
            border-color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
        }
    `;
    document.head.appendChild(style2);
    
    // Handle quiz answer click
    quizOptionsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('.quiz-option-btn');
        if (!btn || btn.disabled) return;
        
        const selectedOption = btn.dataset.option;
        const correctOption = quizQuestions[currentQuizIndex].correct_option;
        const isCorrect = selectedOption === correctOption;
        
        // Disable all options
        const allBtns = quizOptionsContainer.querySelectorAll('.quiz-option-btn');
        allBtns.forEach(b => b.disabled = true);
        
        // Show correct/incorrect
        if (isCorrect) {
            btn.classList.add('correct');
            correctAnswers++;
            quizScore.textContent = correctAnswers;
        } else {
            btn.classList.add('incorrect');
            // Show correct answer
            allBtns.forEach(b => {
                if (b.dataset.option === correctOption) {
                    b.classList.add('correct');
                }
            });
        }
        
        // Move to next question after delay
        setTimeout(() => {
            currentQuizIndex++;
            showQuizQuestion(currentQuizIndex);
        }, 1500);
    });
    
    // Show quiz result
    function showQuizResult() {
        const passRate = correctAnswers / quizQuestions.length;
        const passed = passRate >= 0.5;
        
        quizOptionsContainer.classList.add('hidden');
        
        if (passed) {
            typeQuizText(`🎉 Excellent! You got ${correctAnswers}/${quizQuestions.length} correct! The hidden artifact is now unlocked! Click the chest again to collect it.`, () => {
                // Unlock the artifact via API
                unlockHiddenArtifact();
            });
        } else {
            typeQuizText(`😔 You got ${correctAnswers}/${quizQuestions.length} correct. You need at least 50% to unlock the artifact. Don't give up! Try again!`, () => {
                setTimeout(() => {
                    quizDialogueModal.classList.add('hidden');
                }, 2000);
            });
        }
    }
    
    // Unlock hidden artifact API call
    async function unlockHiddenArtifact() {
        try {
            const response = await fetch('../../app/Handlers/unlock_hidden.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `room_id=<?php echo $room_id; ?>`
            });
            const data = await response.json();
            
            if (data.success) {
                // Update XP bar dynamically
                if (typeof updateXpBar === 'function' && data.new_xp !== undefined && data.xp_progress !== undefined) {
                    updateXpBar(data.new_xp, data.xp_progress, data.new_level, data.rank_name || 'Visitor');
                }
                
                // Update chest to unlocked state
                if (hiddenChest) {
                    hiddenChest.dataset.unlocked = 'true';
                }
                
                setTimeout(() => {
                    quizDialogueModal.classList.add('hidden');
                    // Show chest click prompt
                    alert('Hidden artifact unlocked! Click the chest to collect it.');
                }, 2000);
            }
        } catch (error) {
            console.error('Failed to unlock artifact:', error);
        }
    }
    
    // Quiz skip/abort button
    if (quizSkip) {
        quizSkip.addEventListener('click', () => {
            clearInterval(quizTypeInterval);
            quizDialogueModal.classList.add('hidden');
        });
    }
    
    // Show chest on page load if all artifacts are already collected
    if (allCollected && hiddenChest) {
        // Don't show congrats again, just show the chest directly
        hiddenChest.classList.remove('hidden');
    }
});
</script>

