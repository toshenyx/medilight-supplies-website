/**
 * login.js
 * Handles login form validation and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="login_handler.php"]');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            // Basic validation
            if (!email || !password) {
                alert('Please fill in all fields');
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
            
            // If validation passes, submit the form
            this.submit();
        });
    }
    
    // Add event listeners to the links for smooth transitions if needed
    const forgotPasswordLink = document.querySelector('a[href="password.html"]');
    const signupLink = document.querySelector('a[href="signup.html"]');
    
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            // Optional: Add any special handling for the forgot password link
        });
    }
    
    if (signupLink) {
        signupLink.addEventListener('click', function(e) {
            // Optional: Add any special handling for the signup link
        });
    }
});