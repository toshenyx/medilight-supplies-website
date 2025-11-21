// signup.js
document.addEventListener('DOMContentLoaded', function () {
    // Get form and elements
    const form = document.querySelector('form');
    const usernameInput = document.querySelector('input[name="username"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    
    // Optional: Add a visually hidden helper for screen readers
    let feedbackDiv = document.getElementById('form-feedback');
    if (!feedbackDiv) {
        feedbackDiv = document.createElement('div');
        feedbackDiv.id = 'form-feedback';
        feedbackDiv.setAttribute('aria-live', 'polite');
        feedbackDiv.style.cssText = `
            position: fixed; top: 10px; right: 10px; z-index: 1000;
            padding: 12px 20px; border-radius: 6px; font-weight: bold;
            color: white; max-width: 300px; text-align: center;
            opacity: 0; transition: opacity 0.3s, transform 0.3s;
            transform: translateY(-20px);
        `;
        document.body.appendChild(feedbackDiv);
    }

    // Show feedback message
    function showFeedback(message, isError = true) {
        feedbackDiv.textContent = message;
        feedbackDiv.style.backgroundColor = isError ? '#e53935' : '#43a047';
        feedbackDiv.style.opacity = '1';
        feedbackDiv.style.transform = 'translateY(0)';
        
        setTimeout(() => {
            feedbackDiv.style.opacity = '0';
            feedbackDiv.style.transform = 'translateY(-20px)';
        }, 3000);
    }

    // Validate username
    function validateUsername() {
        const value = usernameInput.value.trim();
        if (value.length < 3) {
            usernameInput.setCustomValidity('Username must be at least 3 characters.');
            return false;
        }
        if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            usernameInput.setCustomValidity('Username can only contain letters, numbers, and underscores.');
            return false;
        }
        usernameInput.setCustomValidity('');
        return true;
    }

    // Validate email
    function validateEmail() {
        const value = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            emailInput.setCustomValidity('Please enter a valid email address (e.g., abc@gmail.com).');
            return false;
        }
        emailInput.setCustomValidity('');
        return true;
    }

    // Validate password
    function validatePassword() {
        const value = passwordInput.value;
        if (value.length < 6) {
            passwordInput.setCustomValidity('Password must be at least 6 characters.');
            return false;
        }
        // Optional: Add strength feedback
        const strength = getPassStrength(value);
        let msg = '';
        if (strength < 2) msg = 'Weak password';
        else if (strength === 2) msg = 'Medium strength';
        else msg = 'Strong password ✅';
        
        // You can show this inline later if you want
        passwordInput.setCustomValidity('');
        return true;
    }

    // Confirm password match
    function validateConfirmPassword() {
        const pass = passwordInput.value;
        const confirm = confirmPasswordInput.value;
        if (pass !== confirm) {
            confirmPasswordInput.setCustomValidity('Passwords do not match.');
            return false;
        }
        confirmPasswordInput.setCustomValidity('');
        return true;
    }

    // Simple password strength checker (0–3)
    function getPassStrength(pass) {
        let strength = 0;
        if (pass.length >= 6) strength++;
        if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) strength++;
        if (/\d/.test(pass)) strength++;
        if (/[^A-Za-z0-9]/.test(pass)) strength++;
        return Math.min(strength, 3);
    }

    // Attach real-time validation
    usernameInput.addEventListener('input', validateUsername);
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', () => {
        validatePassword();
        if (confirmPasswordInput.value) validateConfirmPassword(); // re-check confirm if needed
    });
    confirmPasswordInput.addEventListener('input', validateConfirmPassword);

    // Form submission handler
    form.addEventListener('submit', function (e) {
        // Trigger all validations
        const isUserValid = validateUsername();
        const isEmailValid = validateEmail();
        const isPassValid = validatePassword();
        const isConfirmValid = validateConfirmPassword();

        if (!isUserValid || !isEmailValid || !isPassValid || !isConfirmValid) {
            e.preventDefault();
            showFeedback('Please fix the errors above.', true);
            // Scroll to first invalid field
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) firstInvalid.focus();
            return false;
        }

        // Optional: Disable button during submission
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing up...';

        // Optional: Add spinner (if you want later)
        // submitBtn.innerHTML = 'Signing up... <span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;"></span>';

        // Allow form to submit (to signup_handler.php)
        // PHP will do final server-side validation
    });
});