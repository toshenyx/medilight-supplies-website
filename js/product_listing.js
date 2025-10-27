/**
 * js/product_listing.js
 * * CORE SCRIPT: Handles 'Add to Cart' functionality across all product listing pages 
 * and maintains the cart counter in the header using LocalStorage.
 */

document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const cartIconLink = document.querySelector('a[href="cart.html"]');
    
    // --- 1. Cart Utility Functions ---

    function getCart() {
        // FIX: Use the consistent LocalStorage key 'medilightCart'
        const cartData = localStorage.getItem('medilightCart'); 
        return cartData ? JSON.parse(cartData) : [];
    }

    function saveCart(cart) {
        // FIX: Use the consistent LocalStorage key 'medilightCart'
        localStorage.setItem('medilightCart', JSON.stringify(cart));
        updateCartCountDisplay(cart);
    }

    function updateCartCountDisplay(cart) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        // Find the parent icon container
        const cartIcon = cartIconLink ? cartIconLink.closest('.icon') : null;
        if (!cartIcon) return;

        let countSpan = cartIcon.querySelector('.cart-count');

        // Create the count element if it doesn't exist
        if (!countSpan) {
            countSpan = document.createElement('span');
            countSpan.classList.add('cart-count');
            // Append to the parent container (.icon) which holds the link and div
            cartIcon.appendChild(countSpan); 
        }

        // Update display based on count
        if (totalItems > 0) {
            countSpan.textContent = totalItems;
            countSpan.style.display = 'flex'; // Use display:flex or block for visibility
        } else {
            countSpan.style.display = 'none'; // Hide if cart is empty
        }
    }

    // --- 2. Core Add to Cart Logic ---

    function addToCart(event) {
        // FIX: Changed selector to the modular '.product-card' 
        const itemElement = event.target.closest('.product-card'); 
        if (!itemElement) return;
        
        // FIX: Extracting Data using reliable data-attributes 
        const itemId = itemElement.getAttribute('data-product-id');
        const itemName = itemElement.getAttribute('data-product-name');
        const itemPrice = parseFloat(itemElement.getAttribute('data-product-price'));
        const imageSrc = itemElement.querySelector('img').getAttribute('src');

        if (!itemId || !itemName || isNaN(itemPrice)) {
             console.error('Missing required data attributes (id, name, or price) on the product card.');
             return; 
        }

        const newItem = {
            id: itemId,
            name: itemName,
            price: itemPrice,
            image: imageSrc,
            quantity: 1
        };

        let cart = getCart();
        const existingItemIndex = cart.findIndex(item => item.id === newItem.id);

        if (existingItemIndex > -1) {
            // Item exists: increase quantity
            cart[existingItemIndex].quantity += 1;
        } else {
            // Item is new: add to cart
            cart.push(newItem);
        }

        saveCart(cart);

        // Visual Feedback (Retained from your original code)
        const originalText = event.target.classList.contains('equipment-cart') ? '🛒' : event.target.textContent;
        event.target.textContent = 'Added! 👍';
        event.target.disabled = true;
        
        setTimeout(() => {
            event.target.textContent = originalText;
            event.target.disabled = false;
        }, 800);
    }

    // --- 3. Initialization ---
    
    // Attach event listener to the body for efficiency (event delegation)
    body.addEventListener('click', function(event) {
        // Ensure you capture clicks on the intended buttons
        if (event.target.classList.contains('add-to-cart') || event.target.classList.contains('add-btn') || event.target.classList.contains('equipment-cart')) {
            addToCart(event);
        }
    });

    // Initialize cart count on page load
    updateCartCountDisplay(getCart());
});