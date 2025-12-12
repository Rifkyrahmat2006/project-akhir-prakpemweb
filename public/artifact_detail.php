<!-- Collection Modal (Hidden by default) -->
<div id="collect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-dark-bg border border-gold p-8 max-w-sm text-center rounded-lg transform scale-90 transition-transform duration-300" id="modal-content">
        <h3 class="text-2xl text-gold font-serif mb-4" id="modal-title">Artifact found!</h3>
        <p class="text-gray-300 mb-6" id="modal-desc">Description here...</p>
        <button id="btn-collect" class="btn-museum w-full mb-4">Collect & Gain XP</button>
        <button id="btn-close" class="text-gray-500 text-sm underline hover:text-white">Close</button>
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
        const modalDesc = document.getElementById('modal-desc');
        const btnCollect = document.getElementById('btn-collect');
        const btnClose = document.getElementById('btn-close');

        let currentArtifactId = null;

        artifacts.forEach(art => {
            art.addEventListener('click', () => {
                const id = art.dataset.id;
                const name = art.dataset.name;
                const desc = art.dataset.desc;
                const collected = art.dataset.collected === 'true';

                currentArtifactId = id;
                modalTitle.innerText = name;
                modalDesc.innerText = desc;

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
