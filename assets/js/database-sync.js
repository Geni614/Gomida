document.addEventListener('DOMContentLoaded', function() {
    // Function to update coins in database
    function updateCoinsInDatabase(coinCount) {
        fetch('update_coins.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ coins: coinCount })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Database updated:', data);
        })
        .catch(error => console.error('Error updating database:', error));
    }
    
    // Function to get current coins
    function getCurrentCoins() {
        return parseInt(localStorage.getItem('coins') || '0');
    }
    
    // Modify original click handler to update database
    const originalClickHandler = image.onclick;
    const coin = document.getElementById('coin');
    
    if (coin) {
        coin.addEventListener('click', function() {
            // Wait a bit to let the original handler update localStorage
            setTimeout(() => {
                const currentCoins = getCurrentCoins();
                updateCoinsInDatabase(currentCoins);
            }, 100);
        });
    }
    
    // Sync database periodically anyway
    setInterval(() => {
        const currentCoins = getCurrentCoins();
        updateCoinsInDatabase(currentCoins);
    }, 5000);
});
