class Spaceship {
    constructor() {
        this.element = document.createElement('img');
        this.element.src = './assets/images/spaceship.png';
        this.element.classList.add('spaceship');
    }

    spawn() {
        let container = document.querySelector('.spaceship-container');
        if (!container) {
            container = document.createElement('div');
            container.classList.add('spaceship-container');
            document.body.appendChild(container);
        }

        // Adjust spawn position to be more centered
        const centerBias = Math.random() * 0.6 + 0.2; // 0.2 to 0.8 for more centered distribution
        const randomX = window.innerWidth * centerBias;
        const randomOffsetX = (Math.random() - 0.5) * 150; // Increased spread
        
        this.element.style.left = `${randomX + randomOffsetX}px`;
        container.appendChild(this.element);

        this.element.addEventListener('animationend', () => {
            this.element.remove();
        });
    }
}

function spawnSpaceship() {
    // Create spaceship element
    const spaceship = document.createElement('div');
    spaceship.className = 'spaceship';
    
    // Random horizontal position
    const randomX = Math.random() * (window.innerWidth - 60);
    spaceship.style.left = `${randomX}px`;
    
    // Add to document
    document.body.appendChild(spaceship);
    
    // Log for debugging
    console.log('Spaceship spawned');
    
    // Remove spaceship after animation completes
    setTimeout(() => {
        if (spaceship && spaceship.parentNode) {
            spaceship.parentNode.removeChild(spaceship);
        }
    }, 6000);
}

// Spawn spaceships periodically
setInterval(spawnSpaceship, 3000);

// Spawn one immediately
spawnSpaceship();
