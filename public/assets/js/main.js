// Main JavaScript file for Classic Old Europe Museum
// ==================================================

document.addEventListener("DOMContentLoaded", () => {
  console.log("Museum script loaded");

  // Initialize page fade-in animation
  initPageTransition();

  // Initialize mobile menu
  initMobileMenu();
});

// ==================================================
// PAGE TRANSITIONS
// ==================================================
function initPageTransition() {
  const mainContent =
    document.querySelector("main") || document.querySelector(".flex-grow");
  if (mainContent) {
    mainContent.classList.add("page-fade-in");
  }
}

// ==================================================
// MOBILE MENU
// ==================================================
function initMobileMenu() {
  const menuBtn = document.querySelector("[data-menu-toggle]");
  const mobileMenu = document.querySelector(".mobile-menu");

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("open");
    });

    // Close on click outside
    document.addEventListener("click", (e) => {
      if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
        mobileMenu.classList.remove("open");
      }
    });
  }
}

// ==================================================
// TOAST NOTIFICATIONS
// ==================================================
function showToast(message, type = "default", duration = 3000) {
  // Remove existing toast
  const existingToast = document.querySelector(".toast");
  if (existingToast) {
    existingToast.remove();
  }

  // Create toast element
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.innerHTML = `
        <i class="fas ${
          type === "success"
            ? "fa-check-circle"
            : type === "error"
            ? "fa-exclamation-circle"
            : "fa-info-circle"
        } mr-2"></i>
        ${message}
    `;

  document.body.appendChild(toast);

  // Auto remove
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateX(100%)";
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ==================================================
// XP BAR ANIMATION
// ==================================================
function animateXPBar(currentXP, maxXP) {
  const xpBar = document.querySelector(".xp-bar-fill");
  if (xpBar) {
    const percentage = (currentXP / maxXP) * 100;
    xpBar.style.width = percentage + "%";
  }
}

// ==================================================
// LEVEL UP EFFECT
// ==================================================
function showLevelUpEffect(newLevel) {
  const overlay = document.createElement("div");
  overlay.className =
    "fixed inset-0 z-50 flex items-center justify-center bg-black/90";
  overlay.innerHTML = `
        <div class="text-center animate-pulse">
            <i class="fas fa-crown text-gold text-6xl mb-4"></i>
            <h2 class="text-4xl text-gold font-serif mb-2">LEVEL UP!</h2>
            <p class="text-2xl text-white">You are now Level ${newLevel}</p>
            <p class="text-gray-400 mt-4">Click anywhere to continue</p>
        </div>
    `;

  document.body.appendChild(overlay);

  overlay.addEventListener("click", () => {
    overlay.style.opacity = "0";
    setTimeout(() => {
      overlay.remove();
      location.reload();
    }, 300);
  });
}

// ==================================================
// COLLECT ARTIFACT SUCCESS EFFECT
// ==================================================
function showCollectSuccess(xpGained) {
  // Floating +XP text
  const floater = document.createElement("div");
  floater.className =
    "fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 pointer-events-none";
  floater.innerHTML = `
        <span class="text-4xl text-gold font-bold animate-bounce">+${xpGained} XP</span>
    `;

  document.body.appendChild(floater);

  setTimeout(() => {
    floater.style.opacity = "0";
    floater.style.transform = "translate(-50%, -100px)";
    setTimeout(() => floater.remove(), 500);
  }, 1000);
}

// ==================================================
// SOUND EFFECTS (Optional - requires audio files)
// ==================================================
const sounds = {
  collect: null,
  levelUp: null,
  click: null,
};

function initSounds() {
  // Uncomment and add audio files to enable sounds
  // sounds.collect = new Audio('assets/audio/collect.mp3');
  // sounds.levelUp = new Audio('assets/audio/levelup.mp3');
  // sounds.click = new Audio('assets/audio/click.mp3');
}

function playSound(soundName) {
  if (sounds[soundName]) {
    sounds[soundName].currentTime = 0;
    sounds[soundName].play().catch(() => {});
  }
}

// ==================================================
// UTILITY: Format number with commas
// ==================================================
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// ==================================================
// UTILITY: Debounce function
// ==================================================
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
