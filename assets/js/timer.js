// Very simple timer.js
document.addEventListener('DOMContentLoaded', function() {
    // Create timer element if needed
    let timerElement = document.getElementById('timer');
    if (!timerElement) {
        timerElement = document.createElement('div');
        timerElement.id = 'timer';
        timerElement.style.position = 'fixed';
        timerElement.style.top = '10px';
        timerElement.style.right = '10px';
        timerElement.style.background = 'rgba(0, 0, 0, 0.7)';
        timerElement.style.padding = '10px 15px';
        timerElement.style.borderRadius = '15px';
        timerElement.style.fontSize = '1.5rem';
        timerElement.style.color = '#00ff9d';
        timerElement.style.zIndex = '1000';
        document.body.appendChild(timerElement);
    }
    
    // Simple 60-second timer
    let timeLeft = 60;
    timerElement.textContent = timeLeft;
    
    // Start countdown
    const timerInterval = setInterval(function() {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerElement.style.color = '#ff4d4d';
            timerElement.textContent = '10:00'; // Show 10 minutes
        }
    }, 1000);
});
