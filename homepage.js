/**
 * homepage.js
 * Handles homepage-specific functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart counter on homepage
    updateCartCounter();
    
    // Function to update cart counter display
    function updateCartCounter() {
        // Get cart from localStorage
        const cart = JSON.parse(localStorage.getItem('medilightCart')) || [];
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        // Update cart counter in the header if it exists
        const cartIcon = document.querySelector('.top-links a[href="cart.html"]');
        if (cartIcon) {
            const iconContainer = cartIcon.parentElement;
            let countSpan = iconContainer.querySelector('.cart-count');
            
            if (!countSpan) {
                countSpan = document.createElement('span');
                countSpan.classList.add('cart-count');
                iconContainer.appendChild(countSpan);
            }
            
            if (totalItems > 0) {
                countSpan.textContent = totalItems;
                countSpan.style.display = 'inline-block';
            } else {
                countSpan.style.display = 'none';
            }
        }
    }
    
    // Handle search functionality
    const searchInput = document.querySelector('.search input[type="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    // In a real implementation, you might redirect to a search results page
                    alert(`Searching for: ${searchTerm}`);
                }
            }
        });
    }
    
    // Handle navigation buttons
    const shopButtons = document.querySelectorAll('.btn.shop');
    shopButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            // Smooth scroll to the target section or redirect to shop page
            const target = this.getAttribute('href');
            if (target && target.startsWith('#')) {
                const targetElement = document.querySelector(target);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            } else {
                // Redirect to shop page
                window.location.href = 'equipment.html';
            }
        });
    });
});