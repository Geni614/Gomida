const body = document.body;
const image = body.querySelector('#coin');
const h1 = body.querySelector('#balance');

let coins = localStorage.getItem('coins');
let total = localStorage.getItem('total');
let power = localStorage.getItem('power');
let count = localStorage.getItem('count');

// Initialize coins if not already set
if (coins === null) {
    localStorage.setItem('coins', '0');
    h1.textContent = '0';
} else {
    h1.textContent = Number(coins).toLocaleString();
}

// Check if total and power values exist. If not, initialize them to 500
if (total === null) {
    localStorage.setItem('total', '500');
    body.querySelector('#total').textContent = '/500';
} else {
    body.querySelector('#total').textContent = `/${total}`;
}

if (power === null) {
    localStorage.setItem('power', '500');
    body.querySelector('#power').textContent = '500';
} else {
    body.querySelector('#power').textContent = power;
}

if (count === null) {
    localStorage.setItem('count', '1')
}

image.addEventListener('click', (e)=> {
    let x = e.offsetX;
    let y = e.offsetY;

    navigator.vibrate(5);

    coins = localStorage.getItem('coins');
    power = localStorage.getItem('power');
    
    if(Number(power) > 0){
        localStorage.setItem('coins', `${Number(coins) + 1}`);
        h1.textContent = `${(Number(coins) + 1).toLocaleString()}`;

        localStorage.setItem('power', `${Number(power) - 1}`);
        body.querySelector('#power').textContent = `${Number(power) - 1}`;
    }

    if(x < 150 & y < 150){
        image.style.transform = 'translate(-0.25rem, -0.25rem) skewY(-10deg) skewX(5deg)';
    }
    else if (x < 150 & y > 150){
        image.style.transform = 'translate(-0.25rem, 0.25rem) skewY(-10deg) skewX(5deg)';
    }
    else if (x > 150 & y > 150){
        image.style.transform = 'translate(0.25rem, 0.25rem) skewY(10deg) skewX(-5deg)';
    }
    else if (x > 150 & y < 150){
        image.style.transform = 'translate(0.25rem, -0.25rem) skewY(10deg) skewX(-5deg)';
    }

    setTimeout(()=>{
        image.style.transform = 'translate(0px, 0px)';
    }, 100);

    body.querySelector('.progress').style.width = `${(100 * power) / total}%`;
});

function updateDatabase(coinCount) {
    fetch('update_coins.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coins: coinCount })
    })
    .catch(error => console.error('Error updating database:', error));
}

window.addEventListener('DOMContentLoaded', () => {
    // Force fixed size on the coin
    const coin = document.getElementById('coin');
    if (coin) {
        coin.style.width = '250px';
        
        // Monitor for any changes to the coin's style
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'style') {
                    // If transform contains 'scale', remove it
                    const transform = coin.style.transform;
                    if (transform && transform.includes('scale')) {
                        coin.style.transform = transform.replace(/scale\([^)]*\)/g, '');
                    }
                }
            });
        });
        
        observer.observe(coin, { attributes: true });
    }
});

function spawnSpaceship() {
    // Don't spawn during cooldown
    const cooldownEnd = localStorage.getItem('cooldownEnd');
    if (cooldownEnd && Date.now() < parseInt(cooldownEnd)) {
        return;
    }
    
    // Create spaceship element
    const spaceship = document.createElement('div');
    spaceship.className = 'spaceship';
    
    // Random horizontal position
    const randomX = Math.random() * (window.innerWidth - 60);
    spaceship.style.left = `${randomX}px`;
    
    // Add to document
    document.body.appendChild(spaceship);
    
    // Remove spaceship after animation completes
    setTimeout(() => {
        if (spaceship && spaceship.parentNode) {
            spaceship.parentNode.removeChild(spaceship);
        }
    }, 6000);
    
    // Log for debugging
    console.log('Spaceship spawned at position:', randomX);
}

// Clear any existing intervals
if (window.spaceshipInterval) {
    clearInterval(window.spaceshipInterval);
}

// Spawn spaceships periodically
window.spaceshipInterval = setInterval(spawnSpaceship, 3000);

// Spawn one immediately
spawnSpaceship();

// Sync coins with database periodically
function syncCoinsWithDatabase() {
    const coins = localStorage.getItem('coins') || '0';
    
    fetch('update_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coins: parseInt(coins) })
    });
}

// Sync every 30 seconds
setInterval(syncCoinsWithDatabase, 30000);

// Check for cooldown before processing clicks
const originalClickHandler = image.onclick;
image.onclick = function(e) {
    const cooldownEnd = localStorage.getItem('cooldownEnd');
    if (cooldownEnd && Date.now() < parseInt(cooldownEnd)) {
        // In cooldown, don't allow clicking
        return false;
    }
    
    // Not in cooldown, process click normally
    if (typeof originalClickHandler === 'function') {
        return originalClickHandler.call(this, e);
    }
};

function checkCoinClickability() {
    const coin = document.getElementById('coin');
    if (coin) {
        // Check computed styles
        const styles = window.getComputedStyle(coin);
        console.log('Coin styles:', {
            pointerEvents: styles.pointerEvents,
            opacity: styles.opacity,
            cursor: styles.cursor,
            zIndex: styles.zIndex
        });
        
        // Force clickability
        coin.style.pointerEvents = 'auto';
        coin.style.cursor = 'pointer';
        coin.style.opacity = '1';
        
        // Test click handler
        const oldClick = coin.onclick;
        coin.onclick = function(e) {
            console.log('Coin click detected');
            if (typeof oldClick === 'function') {
                return oldClick.call(this, e);
            }
        };
    } else {
        console.error('Coin element not found');
    }
}

// Run this check when the page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(checkCoinClickability, 1000); // Check after 1 second
});
