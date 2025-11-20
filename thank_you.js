/**
 * thank_you.js
 * Handles thank you page functionality including clearing the cart after successful order
 */

document.addEventListener('DOMContentLoaded', function() {
    // Clear the cart after successful order completion
    localStorage.removeItem('medilightCart');
    
    // Update cart counter display to show 0 items
    updateCartCounter();
    
    // Function to update cart counter display
    function updateCartCounter() {
        // Update cart counter in the header if it exists
        const cartIcon = document.querySelector('.icon a[href="cart.html"]');
        if (cartIcon) {
            const iconContainer = cartIcon.closest('.icon');
            const countSpan = iconContainer.querySelector('.cart-count');
            
            if (countSpan) {
                countSpan.textContent = '0';
                countSpan.style.display = 'none';
            }
        }
    }
    
    // Add functionality to the "Continue Shopping" button
    const continueShoppingButton = document.querySelector('a[href="index.html"]');
    
    if (continueShoppingButton) {
        continueShoppingButton.addEventListener('click', function(e) {
            // Optional: Add any special handling before redirecting
            // For example, you could log that the user is continuing to shop
        });
    }
    
    // Generate a random order number for display (if needed)
    function generateOrderNumber() {
        const prefix = 'MLT';
        const year = new Date().getFullYear();
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        return `${prefix}-${year}-${randomNum}`;
    }
    
    // Update the order number in the page if it's a placeholder
    const orderNumberElement = document.querySelector('.order-details-summary strong');
    if (orderNumberElement && orderNumberElement.textContent.includes('0427')) {
        // Only update if it's still the placeholder value
        orderNumberElement.textContent = generateOrderNumber();
    }
});