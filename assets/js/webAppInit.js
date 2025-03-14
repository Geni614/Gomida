let tg = window.Telegram.WebApp;

// Initialize the Web App
document.addEventListener('DOMContentLoaded', function() {
    tg.ready();
    tg.expand(); // Expand the Web App to full height

    // Check if user is already logged in
    if (!sessionStorage.getItem('user_logged_in')) {
        // If not logged in, show login modal
        if (window.authManager) {
            window.authManager.showModal('loginModal');
        } else {
            // If authManager is not loaded yet, wait and try again
            setTimeout(() => {
                if (window.authManager) {
                    window.authManager.showModal('loginModal');
                }
            }, 500);
        }
    }

    // Handle Telegram's main button (if you want to use it)
    tg.MainButton.setText('PLAY NOW');
    tg.MainButton.onClick(() => {
        if (!sessionStorage.getItem('user_logged_in')) {
            window.authManager.showModal('loginModal');
        } else {
            // User is logged in, start the game
            startGame();
        }
    });
});

function startGame() {
    // Your game initialization code here
    console.log('Starting game...');
    // You might want to hide the login modal and show the game interface
}

// Add this to handle the case when user successfully logs in
window.onLoginSuccess = function() {
    sessionStorage.setItem('user_logged_in', 'true');
    startGame();
};
