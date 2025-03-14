document.addEventListener('DOMContentLoaded', function() {
    const coin = document.getElementById('coin');
    const balanceElement = document.getElementById('balance');
    
    // Function to check timer state
    function isTimerRunning() {
        const cooldownEnd = localStorage.getItem('cooldownEnd');
        return !(cooldownEnd && Date.now() < parseInt(cooldownEnd));
    }
    
    // Directly add click handler to the coin
    coin.addEventListener('click', function(e) {
        // Only process click if timer is running
        if (!isTimerRunning()) return;
        
        // Get current values
        const coins = localStorage.getItem('coins') || '0';
        const power = localStorage.getItem('power') || '500';
        
        if (Number(power) > 0) {
            // Increment coins
            const newCoins = Number(coins) + 1;
            localStorage.setItem('coins', newCoins.toString());
            
            // Update display
            if (balanceElement) {
                balanceElement.textContent = newCoins.toLocaleString();
            }
            
            // Decrement power
            const newPower = Number(power) - 1;
            localStorage.setItem('power', newPower.toString());
            
            // Update power display
            const powerElement = document.getElementById('power');
            if (powerElement) {
                powerElement.textContent = newPower.toString();
            }
            
            // Animation based on click position
            const x = e.offsetX;
            if (x < 125) {
                // Clicked on left side
                coin.style.transform = 'translateX(-10px)';
            } else {
                // Clicked on right side
                coin.style.transform = 'translateX(10px)';
            }
            
            // Reset position
            setTimeout(() => {
                coin.style.transform = 'translateX(0)';
            }, 100);
            
            // Update progress bar
            const total = localStorage.getItem('total') || '500';
            const progressBar = document.querySelector('.progress');
            if (progressBar) {
                progressBar.style.width = `${(100 * newPower) / Number(total)}%`;
            }
            
            // Sync with database (if you have that functionality)
            if (typeof updateDatabase === 'function') {
                updateDatabase(newCoins);
            }
        }
    });
    
    // Function to disable/enable coin based on timer
    function updateCoinState() {
        if (isTimerRunning()) {
            coin.style.opacity = '1';
            coin.style.cursor = 'pointer';
        } else {
            coin.style.opacity = '0.5';
            coin.style.cursor = 'default';
        }
    }
    
    // Check and update coin state periodically
    updateCoinState();
    setInterval(updateCoinState, 1000);
});
