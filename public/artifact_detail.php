<!-- Collection Modal (Hidden by default) -->
<div id="collect-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300">
    <div class="relative px-20 py-12 text-center transform scale-90 transition-transform duration-300 bg-contain bg-center bg-no-repeat min-w-[550px] min-h-[650px] flex flex-col items-center justify-center" id="modal-content" style="background-image: url('/project-akhir/public/assets/img/elements/old-paper.png');">
        
        <!-- Close X Button (Top Right - Inside paper) -->
        <button id="btn-close" class="absolute top-12 right-12 w-8 h-8 flex items-center justify-center rounded-full bg-amber-900/30 hover:bg-amber-900/50 text-amber-900 hover:text-amber-800 text-lg font-bold transition-all z-20">
            <i class="fas fa-times"></i>
        </button>
        
        
        <!-- Content -->
        <div class="relative z-10 max-w-[350px] mx-auto">
            <h3 class="text-lg text-amber-900 font-serif font-bold mb-2 drop-shadow-sm" id="modal-title">Artifact found!</h3>
            
            <!-- Status label -->
            <p class="text-sm text-amber-700 mb-3" id="modal-status"></p>
            
            <!-- Artifact Image with Aura -->
            <div class="mb-4 flex justify-center">
                <div class="relative">
                    <!-- Outer Glow Aura -->
                    <div class="absolute inset-[-20px] rounded-full bg-gradient-to-r from-amber-400/30 via-yellow-300/40 to-amber-400/30 blur-2xl animate-pulse"></div>
                    <!-- Inner Glow -->
                    <div class="absolute inset-[-12px] rounded-full bg-yellow-200/30 blur-xl"></div>
                    <!-- Image - Much Bigger -->
                    <img id="modal-image" src="" alt="Artifact" class="relative z-10 w-56 h-56 object-contain drop-shadow-[0_0_30px_rgba(251,191,36,0.6)]">
                </div>
            </div>  
            
            <p class="text-amber-800 mb-5 text-sm leading-relaxed px-4" id="modal-desc" style="font-family: 'Garamond', 'Georgia', 'Times New Roman', serif;">Description here...</p>
            <div class="flex justify-center">
                <button id="btn-collect" class="bg-amber-800 hover:bg-amber-900 text-amber-100 font-bold py-2 px-6 text-sm rounded-lg transition shadow-lg">Collect</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to update XP bar dynamically
    function updateXpBar(newXp, xpProgress, newLevel, rankName) {
        // Update desktop XP bar
        const xpBarDesktop = document.getElementById('xp-bar-fill-desktop');
        const xpTextDesktop = document.getElementById('xp-text-desktop');
        const levelTextDesktop = document.getElementById('level-text-desktop');
        const rankTextDesktop = document.getElementById('rank-text-desktop');
        
        if (xpBarDesktop) {
            xpBarDesktop.style.width = xpProgress + '%';
            xpBarDesktop.dataset.currentXp = newXp;
            xpBarDesktop.dataset.level = newLevel;
        }
        if (xpTextDesktop) xpTextDesktop.textContent = newXp.toLocaleString() + ' XP';
        if (levelTextDesktop) levelTextDesktop.textContent = 'LV.' + newLevel;
        if (rankTextDesktop) rankTextDesktop.textContent = rankName;
        
        // Update mobile XP bar
        const xpBarMobile = document.getElementById('xp-bar-fill-mobile');
        const xpTextMobile = document.getElementById('xp-text-mobile');
        const levelTextMobile = document.getElementById('level-text-mobile');
        const rankTextMobile = document.getElementById('rank-text-mobile');
        
        if (xpBarMobile) xpBarMobile.style.width = xpProgress + '%';
        if (xpTextMobile) xpTextMobile.textContent = newXp.toLocaleString() + ' XP';
        if (levelTextMobile) levelTextMobile.textContent = newLevel;
        if (rankTextMobile) rankTextMobile.textContent = rankName;
        
        // Add animation effect to XP bar
        if (xpBarDesktop) {
            xpBarDesktop.style.transition = 'width 0.5s ease-out';
        }
        if (xpBarMobile) {
            xpBarMobile.style.transition = 'width 0.5s ease-out';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Use a more generic class selector if needed, or stick to .artifact-item
        // We will ensure both room.php items and my_collection.php items use this class
        const artifacts = document.querySelectorAll('.artifact-item');
        const modal = document.getElementById('collect-modal');
        const modalContent = document.getElementById('modal-content');
        const modalTitle = document.getElementById('modal-title');
        const modalImage = document.getElementById('modal-image');
        const modalDesc = document.getElementById('modal-desc');
        const btnCollect = document.getElementById('btn-collect');
        const btnClose = document.getElementById('btn-close');

        let currentArtifactId = null;

        artifacts.forEach(art => {
            art.addEventListener('click', () => {
                const id = art.dataset.id;
                const name = art.dataset.name;
                const desc = art.dataset.desc;
                const image = art.dataset.image; // Get Data Image
                const collected = art.dataset.collected === 'true';

                currentArtifactId = id;
                modalTitle.innerText = name;
                modalDesc.innerText = desc;
                
                // Set Image
                if (image) {
                    modalImage.src = image;
                    modalImage.classList.remove('hidden');
                } else {
                    modalImage.classList.add('hidden');
                }

                if (collected) {
                    btnCollect.style.display = 'none';
                    modalTitle.innerHTML = name + ' <span class="text-xs text-green-500 block mt-1">(Collected)</span>';
                } else {
                    btnCollect.style.display = 'block';
                    btnCollect.innerText = "Collect";
                }

                // Show Modal & Hide Room UI
                if (window.toggleRoomUI) window.toggleRoomUI(false);
                modal.classList.remove('hidden');
                // Small delay to allow display:block to apply before opacity transition
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-90');
                    modalContent.classList.add('scale-100');
                }, 10);
            });
        });

        const closeModal = () => {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-90');
            setTimeout(() => {
                modal.classList.add('hidden');
                // Restore Room UI
                if (window.toggleRoomUI) window.toggleRoomUI(true);
            }, 300);
        };

        if(btnClose) btnClose.addEventListener('click', closeModal);

        if(btnCollect) btnCollect.addEventListener('click', () => {
            if (!currentArtifactId) return;

            // AJAX Request
            const formData = new FormData();
            formData.append('artifact_id', currentArtifactId);

            // Path adjustment: Since this file is included in files inside /lobby/, 
            // the relative path ../app/Handlers/collect.php is correct.
            fetch('../../app/Handlers/collect.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update XP bar dynamically
                    if (data.new_xp !== undefined && data.xp_progress !== undefined) {
                        updateXpBar(data.new_xp, data.xp_progress, data.new_level, data.rank_name || 'Visitor');
                    }
                    
                    // Update UI
                    const artEl = document.querySelector(`.artifact-item[data-id="${currentArtifactId}"]`);
                    if(artEl) {
                        artEl.dataset.collected = 'true';
                        
                        // Visual update for room items with glow/icons
                        const glow = artEl.querySelector('.status-glow');
                        const icon = artEl.querySelector('.status-icon');
                        const iconI = icon ? icon.querySelector('i') : null;

                        if (glow) {
                            glow.classList.remove('bg-gold');
                            glow.classList.add('bg-green-500');
                        }
                        if (icon) {
                            icon.classList.remove('bg-black/60', 'border-gold', 'text-gold');
                            icon.classList.add('bg-green-900/60', 'border-green-500', 'text-green-500');
                        }
                        if (iconI) {
                            iconI.classList.remove('fa-gem');
                            iconI.classList.add('fa-check');
                        }
                    }
                    
                    closeModal();
                    
                    // Update artifact counter
                    const counterEl = document.getElementById('artifact-counter');
                    if (counterEl && data.collected_count !== undefined && data.total_artifacts !== undefined) {
                        counterEl.textContent = data.collected_count + '/' + data.total_artifacts;
                    }
                    
                    // Check if all artifacts are now collected
                    if (data.all_collected) {
                        // Store hidden artifact data for the chest
                        if (data.hidden_artifact) {
                            window.hiddenArtifactData = data.hidden_artifact;
                        }
                        
                        // Trigger the professor congratulations modal using global function
                        setTimeout(() => {
                            if (typeof window.showCongratsModal === 'function') {
                                window.showCongratsModal();
                            } else {
                                // Fallback if function not found
                                alert('Congratulations! You collected all artifacts! Look for the hidden chest!');
                                if (typeof window.showHiddenChest === 'function') {
                                    window.showHiddenChest();
                                }
                            }
                        }, 500);
                    } else {
                        // Show XP notification for non-final artifact
                        if (data.new_xp !== undefined) {
                            // Small toast notification instead of alert
                            const xpGained = data.xp_reward || 'some';
                            console.log(`Collected! +${xpGained} XP`);
                        }
                    }
                    
                    // Handle level up
                    if (data.leveled_up) {
                        setTimeout(() => {
                            alert(`LEVEL UP! You are now Level ${data.new_level}!`);
                        }, 100);
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err));
        });
        
        // Close on click outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>
