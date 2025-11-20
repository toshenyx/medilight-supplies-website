/**
 * signup.js
 * Handles signup form validation and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.querySelector('form[action="signup_handler.php"]');
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const username = document.querySelector('input[name="username"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            // Basic validation
            if (!username || !email || !password || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }
            
            // Username length validation
            if (username.length < 3) {
                alert('Username must be at least 3 characters long');
                return;
            }
            
            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }
            
            // Password length validation
            if (password.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }
            
            // Password match validation
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }
            
            // If validation passes, submit the form
            this.submit();
        });
    }
    
    // Add event listener to the login link
    const loginLink = document.querySelector('a[href="login.html"]');
    
    if (loginLink) {
        loginLink.addEventListener('click', function(e) {
            // Optional: Add any special handling for the login link
        });
    }
});