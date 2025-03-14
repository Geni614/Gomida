class AuthManager {
    constructor() {
        this.currentModal = null;
        this.setupEventListeners();
    }

    showModal(modalId) {
        if (this.currentModal) {
            this.currentModal.style.display = 'none';
        }
        this.currentModal = document.getElementById(modalId);
        this.currentModal.style.display = 'block';
    }

    setupEventListeners() {
        // Switch between modals
        document.getElementById('showSignup').addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('signupModal');
        });

        document.getElementById('showLogin').addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('loginModal');
        });

        document.getElementById('forgotPasswordLink').addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('resetModal');
        });

        document.getElementById('backToLogin').addEventListener('click', (e) => {
            e.preventDefault();
            this.showModal('loginModal');
        });

        // Form submissions
        document.getElementById('loginForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin(e.target);
        });

        document.getElementById('signupForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSignup(e.target);
        });

        document.getElementById('resetForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleReset(e.target);
        });

        document.getElementById('signup-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSignup(e.target);
        });
    }

    async handleLogin(form) {
        const formData = new FormData(form);
        try {
            const response = await fetch('process_login.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                // Call the login success handler
                if (window.onLoginSuccess) {
                    window.onLoginSuccess();
                }
                this.currentModal.style.display = 'none';
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred during login');
        }
    }

    async handleSignup(form) {
        const formData = new FormData(form);
        try {
            const response = await fetch('process_signup.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = 'index.html';
            } else {
                document.getElementById('error-message').textContent = data.message;
                document.getElementById('error-message').style.display = 'block';
            }
        } catch (error) {
            console.error('Signup error:', error);
            document.getElementById('error-message').textContent = 'An error occurred during signup';
            document.getElementById('error-message').style.display = 'block';
        }
    }

    async handleReset(form) {
        const formData = new FormData(form);
        try {
            const response = await fetch('process_reset.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                if (data.step === 'question') {
                    this.showSecurityQuestion(data.question);
                } else if (data.step === 'reset') {
                    alert('Password reset successfully!');
                    this.showModal('loginModal');
                }
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Reset error:', error);
            alert('An error occurred during password reset');
        }
    }

    showSecurityQuestion(question) {
        document.getElementById('securityQuestionLabel').textContent = question;
        document.querySelector('.security-question').style.display = 'block';
    }
}

// Initialize auth manager when document loads
document.addEventListener('DOMContentLoaded', () => {
    window.authManager = new AuthManager();
    // Show login modal by default
    window.authManager.showModal('loginModal');
});
