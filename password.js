/**
 * password.js
 * Handles password reset form validation and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.querySelector('form[action="reset_password.php"]');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const username = document.querySelector('input[name="username"]').value;
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            // Basic validation
            if (!username || !newPassword || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }
            
            // Username length validation
            if (username.length < 3) {
                alert('Username must be at least 3 characters long');
                return;
            }
            
            // Password length validation
            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }
            
            // Password match validation
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            // If validation passes, submit the form
            this.submit();
        });
    }
    
    // Add event listener to the back to login link
    const loginLink = document.querySelector('a[href="login.html"]');
    
    if (loginLink) {
        loginLink.addEventListener('click', function(e) {
            // Optional: Add any special handling for the login link
        });
    }
});