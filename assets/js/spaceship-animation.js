document.addEventListener('DOMContentLoaded', function() {
    // First, add spaceship style if not already present
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        .spaceship {
            position: fixed;
            width: 60px;
            height: 60px;
            background-image: url('./assets/images/spaceship.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            z-index: 100;
            pointer-events: none;
        }
        
        @keyframes flyUpDiagonalLeft {
            0% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            100% { transform: translate(-200px, -100vh); opacity: 0; }
        }
        
        @keyframes flyUpDiagonalRight {
            0% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            100% { transform: translate(200px, -100vh); opacity: 0; }
        }
        
        @keyframes flyUpStraight {
            0% { transform: translateY(0); opacity: 0; }
            10% { opacity: 1; }
            100% { transform: translateY(-100vh); opacity: 0; }
        }
    `;
    document.head.appendChild(styleElement);
    
    // Function to spawn a spaceship
    function spawnSpaceship() {
        // Don't spawn if in cooldown
        const cooldownEnd = localStorage.getItem('cooldownEnd');
        if (cooldownEnd && Date.now() < parseInt(cooldownEnd)) {
            return;
        }
        
        // Create spaceship
        const spaceship = document.createElement('div');
        spaceship.className = 'spaceship';
        
        // Random horizontal position
        const screenWidth = window.innerWidth;
        const randomX = Math.random() * (screenWidth - 60);
        spaceship.style.left = `${randomX}px`;
        spaceship.style.bottom = '-60px';
        
        // Random flight path
        const flightPaths = ['flyUpStraight', 'flyUpDiagonalLeft', 'flyUpDiagonalRight'];
        const randomPath = flightPaths[Math.floor(Math.random() * flightPaths.length)];
        spaceship.style.animation = `${randomPath} ${5 + Math.random() * 3}s linear forwards`;
        
        // Add to document
        document.body.appendChild(spaceship);
        
        // Log for debugging
        console.log('Spaceship spawned at', randomX, 'with path', randomPath);
        
        // Remove after animation completes
        setTimeout(() => {
            if (spaceship && spaceship.parentNode) {
                spaceship.parentNode.removeChild(spaceship);
            }
        }, 8000);
    }
    
    // Spawn spaceships at random intervals
    function scheduleSpaceship() {
        const delay = 1000 + Math.random() * 2000; // Random delay between 1-3 seconds
        setTimeout(() => {
            spawnSpaceship();
            scheduleSpaceship(); // Schedule next spaceship
        }, delay);
    }
    
    // Start spawning
    scheduleSpaceship();
    
    // Spawn one immediately
    spawnSpaceship();
});
