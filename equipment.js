/**
 * equipment.js
 * Handles equipment page functionality including Add to Cart and Wishlist buttons
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle Add to Cart buttons
    const addToCartButtons = document.querySelectorAll('.equipment-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const equipmentItem = this.closest('.equipment-item');
            const equipmentName = equipmentItem.querySelector('.equipment-name').textContent;
            const equipmentPriceText = equipmentItem.querySelector('.equipment-price').textContent;
            const equipmentImage = equipmentItem.querySelector('.equipment-img').src;
            
            // Extract price by removing 'Ksh ' and commas, then converting to number
            const price = parseFloat(equipmentPriceText.replace(/[^\d.-]/g, ''));
            
            // Create a unique ID for the equipment (using name as a simple approach)
            const equipmentId = equipmentName.toLowerCase().replace(/\s+/g, '-');
            
            // Create equipment object
            const equipment = {
                id: equipmentId,
                name: equipmentName,
                price: price,
                image: equipmentImage,
                quantity: 1
            };
            
            // Add to cart functionality
            addToCart(equipment);
        });
    });
    
    // Handle Wishlist buttons
    const wishlistButtons = document.querySelectorAll('.equipment-wishlist');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const equipmentItem = this.closest('.equipment-item');
            const equipmentName = equipmentItem.querySelector('.equipment-name').textContent;
            const equipmentPriceText = equipmentItem.querySelector('.equipment-price').textContent;
            const equipmentImage = equipmentItem.querySelector('.equipment-img').src;
            
            // Extract price by removing 'Ksh ' and commas, then converting to number
            const price = parseFloat(equipmentPriceText.replace(/[^\d.-]/g, ''));
            
            // Create a unique ID for the equipment
            const equipmentId = equipmentName.toLowerCase().replace(/\s+/g, '-');
            
            // Create equipment object for wishlist
            const equipment = {
                id: equipmentId,
                name: equipmentName,
                price: price,
                image: equipmentImage
            };
            
            // Add to wishlist functionality
            addToWishlist(equipment);
        });
    });
    
    // Function to add item to cart
    function addToCart(item) {
        // Get existing cart from localStorage or create empty array
        let cart = JSON.parse(localStorage.getItem('medilightCart')) || [];
        
        // Check if item already exists in cart
        const existingItemIndex = cart.findIndex(cartItem => cartItem.id === item.id);
        
        if (existingItemIndex > -1) {
            // If exists, increment quantity
            cart[existingItemIndex].quantity += 1;
        } else {
            // If not exists, add new item
            cart.push(item);
        }
        
        // Save updated cart to localStorage
        localStorage.setItem('medilightCart', JSON.stringify(cart));
        
        // Provide visual feedback
        const originalText = '🛒 Add to Cart';
        this.textContent = 'Added! 👍';
        this.disabled = true;
        
        setTimeout(() => {
            this.textContent = originalText;
            this.disabled = false;
        }, 1500);
        
        // Update cart count display (if there's a cart counter element)
        updateCartCounter();
    }
    
    // Function to add item to wishlist
    function addToWishlist(item) {
        // Get existing wishlist from localStorage or create empty array
        let wishlist = JSON.parse(localStorage.getItem('medilightWishlist')) || [];
        
        // Check if item already exists in wishlist
        const existingItemIndex = wishlist.findIndex(wishlistItem => wishlistItem.id === item.id);
        
        if (existingItemIndex > -1) {
            // If exists, inform user
            alert(`${item.name} is already in your wishlist!`);
        } else {
            // If not exists, add new item
            wishlist.push(item);
            
            // Save updated wishlist to localStorage
            localStorage.setItem('medilightWishlist', JSON.stringify(wishlist));
            
            // Provide visual feedback
            const originalText = '❤️ Wishlist';
            this.textContent = 'Saved! ❤️';
            this.disabled = true;
            
            setTimeout(() => {
                this.textContent = originalText;
                this.disabled = false;
            }, 1500);
        }
    }
    
    // Function to update cart counter display
    function updateCartCounter() {
        const cart = JSON.parse(localStorage.getItem('medilightCart')) || [];
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        // Update cart counter in the header if it exists
        const cartIcon = document.querySelector('.icon a[href="cart.html"]');
        if (cartIcon) {
            const iconContainer = cartIcon.closest('.icon');
            let countSpan = iconContainer.querySelector('.cart-count');
            
            if (!countSpan) {
                countSpan = document.createElement('span');
                countSpan.classList.add('cart-count');
                iconContainer.appendChild(countSpan);
            }
            
            if (totalItems > 0) {
                countSpan.textContent = totalItems;
                countSpan.style.display = 'block';
            } else {
                countSpan.style.display = 'none';
            }
        }
    }
    
    // Initialize cart counter on page load
    updateCartCounter();
});