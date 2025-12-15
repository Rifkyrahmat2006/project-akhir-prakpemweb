<!-- Collection Modal (Hidden by default) -->
<div id="collect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300">
    <div class="relative px-20 py-12 text-center transform scale-90 transition-transform duration-300 bg-contain bg-center bg-no-repeat min-w-[550px] min-h-[650px] flex flex-col items-center justify-center" id="modal-content" style="background-image: url('<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/assets/img/elements/old-paper.png');">
        
        <!-- Close X Button (Top Right - Inside paper) -->
        <button id="btn-close" class="absolute top-12 right-12 w-8 h-8 flex items-center justify-center rounded-full bg-amber-900/30 hover:bg-amber-900/50 text-amber-900 hover:text-amber-800 text-lg font-bold transition-all z-20">
            <i class="fas fa-times"></i>
        </button>
        
        
        <!-- Content -->
        <div class="relative z-10 max-w-[350px] mx-auto">
            <h3 class="text-lg text-amber-900 font-serif font-bold mb-2 drop-shadow-sm" id="modal-title">Artifact found!</h3>
            
            <!-- Status label -->
            <p class="text-sm text-amber-700 mb-2" id="modal-status"></p>
            
            <!-- Artifact Image with Aura -->
            <div class="mb-3 flex justify-center">
                <div class="relative">
                    <!-- Outer Glow Aura -->
                    <div class="absolute inset-[-15px] rounded-full bg-gradient-to-r from-amber-400/30 via-yellow-300/40 to-amber-400/30 blur-2xl animate-pulse"></div>
                    <!-- Inner Glow -->
                    <div class="absolute inset-[-8px] rounded-full bg-yellow-200/30 blur-xl"></div>
                    <!-- Image -->
                    <img id="modal-image" src="" alt="Artifact" class="relative z-10 w-32 h-32 object-contain drop-shadow-[0_0_30px_rgba(251,191,36,0.6)]">
                </div>
            </div>  
            
            <p class="text-amber-800 mb-3 font-bold text-xs leading-relaxed px-4" id="modal-desc">Description here...</p>
            <button id="btn-collect" class="bg-amber-800 hover:bg-amber-900 text-amber-100 font-bold py-1.5 px-5 text-sm rounded-lg transition shadow-lg">Collect</button>
        </div>
    </div>
</div>

<script>
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

                // Show Modal
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
                    alert(`Collected! XP: ${data.new_xp}`);
                    if (data.leveled_up) {
                        alert(`LEVEL UP! You are now Level ${data.new_level}`);
                        location.reload(); // Reload to update navbar level
                    } else {
                        // Update UI without reload
                        const artEl = document.querySelector(`.artifact-item[data-id="${currentArtifactId}"]`);
                        if(artEl) {
                            artEl.dataset.collected = 'true';
                            
                            // Visual update for room items with glow/icons
                            // Using safe navigation for elements that might not exist in my_collection
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
