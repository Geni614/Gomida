class Game {
    constructor() {
        this.balance = 0;
        this.timer = 0;
        this.isInCooldown = false;
        this.timerInterval = null;
        this.coinElement = document.getElementById('main-coin');
        this.balanceElement = document.getElementById('balance');
        this.timerElement = document.getElementById('timer');
        this.initialized = false;
    }

    async init() {
        if (this.initialized) return;
        
        try {
            // Verify session first
            const sessionResponse = await fetch('check_session.php');
            const sessionData = await sessionResponse.json();
            
            if (!sessionData.success) {
                console.error('Session error:', sessionData.error);
                this.showError('Not logged in. Please refresh the page.');
                return;
            }
            
            // Load initial game state
            await Promise.all([
                this.loadCoinCount(),
                this.loadTimer()
            ]);

            // Set up coin click handler
            this.coinElement.addEventListener('click', () => this.handleCoinClick());

            // Start the timer update interval
            this.startTimerInterval();
            
            this.initialized = true;
        } catch (error) {
            console.error('Game initialization error:', error);
            this.showError('Failed to initialize game. Please refresh the page.');
        }
    }

    async loadCoinCount() {
        try {
            const response = await fetch('get_balance.php');
            const text = await response.text();
            
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    this.balance = data.coins;
                    this.updateBalanceDisplay();
                } else {
                    throw new Error(data.error || 'Failed to load coin count');
                }
            } catch (jsonError) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server');
            }
        } catch (error) {
            console.error('Error loading coin count:', error);
            this.showError('Failed to load your coin balance.');
            throw error;
        }
    }

    async loadTimer() {
        try {
            const response = await fetch('get_timer.php');
            const text = await response.text();
            
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    // Calculate remaining time in seconds
                    const now = Math.floor(Date.now() / 1000);
                    this.timer = Math.max(0, data.timer_end - now);
                    this.isInCooldown = data.is_in_cooldown === 1;
                    this.updateTimerDisplay();
                    this.updateCoinState();
                } else {
                    throw new Error(data.error || 'Failed to load timer');
                }
            } catch (jsonError) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server');
            }
        } catch (error) {
            console.error('Error loading timer:', error);
            this.showError('Failed to load game timer.');
            throw error;
        }
    }

    startTimerInterval() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }

        this.timerInterval = setInterval(() => {
            if (this.timer > 0) {
                this.timer--;
                this.updateTimerDisplay();
                this.updateCoinState();
            } else if (this.isInCooldown) {
                // If in cooldown, periodically check timer status
                this.loadTimer();
            } else {
                // If timer is at 0 and not in cooldown, refresh timer from server
                this.loadTimer();
            }
        }, 1000);
    }

    updateBalanceDisplay() {
        if (this.balanceElement) {
            this.balanceElement.textContent = this.balance;
        }
    }

    updateTimerDisplay() {
        if (this.timerElement) {
            if (this.isInCooldown) {
                this.timerElement.textContent = "Cooldown";
                this.timerElement.classList.add('cooldown');
            } else {
                // Format timer as MM:SS
                const minutes = Math.floor(this.timer / 60);
                const seconds = this.timer % 60;
                this.timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                this.timerElement.classList.remove('cooldown');
            }
        }
    }

    updateCoinState() {
        // Update coin clickability based on timer and cooldown
        if (this.timer <= 0 || this.isInCooldown) {
            this.coinElement.classList.add('disabled');
        } else {
            this.coinElement.classList.remove('disabled');
        }
    }

    async handleCoinClick() {
        if (this.timer <= 0 || this.isInCooldown) {
            this.showError('You cannot collect coins right now.');
            return;
        }

        try {
            const response = await fetch('collect_coin.php', {
                method: 'POST'
            });
            const text = await response.text();
            
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    this.balance = data.new_balance;
                    this.updateBalanceDisplay();
                    
                    // Add coin collection animation
                    this.animateCoinCollection();
                    
                    // Update timer if it was modified by the server
                    if (data.timer_end) {
                        const now = Math.floor(Date.now() / 1000);
                        this.timer = Math.max(0, data.timer_end - now);
                        this.isInCooldown = data.is_in_cooldown === 1;
                        this.updateTimerDisplay();
                        this.updateCoinState();
                    }
                } else {
                    this.showError(data.error || 'Failed to collect coin');
                    
                    // Refresh timer state in case it changed
                    this.loadTimer();
                }
            } catch (jsonError) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server');
            }
        } catch (error) {
            console.error('Error collecting coin:', error);
            this.showError('Failed to collect coin. Please try again.');
        }
    }

    animateCoinCollection() {
        // Add a temporary class for animation
        this.coinElement.classList.add('collected');
        setTimeout(() => {
            this.coinElement.classList.remove('collected');
        }, 300);
    }
    
    showError(message) {
        // Create or update error message element
        let errorElement = document.getElementById('game-error');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = 'game-error';
            errorElement.className = 'error-message';
            document.body.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        // Hide after 3 seconds
        setTimeout(() => {
            errorElement.style.display = 'none';
        }, 3000);
    }
}

// Initialize game when DOM is loaded
window.game = new Game();
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if user is logged in (game container is visible)
    if (document.getElementById('game-container').style.display === 'block') {
        window.game.init();
    }
});