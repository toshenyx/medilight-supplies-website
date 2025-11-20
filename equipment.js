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
            const productId = equipmentItem.getAttribute('data-product-id');
            const equipmentName = equipmentItem.querySelector('.equipment-name').textContent;
            const equipmentPriceText = equipmentItem.querySelector('.equipment-price').textContent;
            
            // Extract price by removing 'Ksh ' and commas, then converting to number
            const price = parseFloat(equipmentPriceText.replace(/[^\d.-]/g, ''));
            
            // Add to cart functionality using the product ID
            addToCart({productId, name: equipmentName, price: price});
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
    async function addToCart(item) {
        try {
            const productId = item.productId;
            
            if (productId) {
                // Use the API to add to cart
                const response = await fetch('api/cart_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: parseInt(productId),
                        quantity: 1
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    // Provide visual feedback
                    const clickedButton = document.activeElement && document.activeElement.classList.contains('equipment-cart') 
                        ? document.activeElement 
                        : null;
                        
                    if (clickedButton) {
                        const originalText = '🛒 Add to Cart';
                        clickedButton.textContent = 'Added! 👍';
                        clickedButton.disabled = true;
                        
                        setTimeout(() => {
                            clickedButton.textContent = originalText;
                            clickedButton.disabled = false;
                        }, 1500);
                    }
                    
                    // Update cart count display
                    updateCartCounter();
                } else {
                    console.error('Error adding to cart:', result.message);
                }
            } else {
                // Fallback: try to add using name as ID (for demo purposes)
                // This approach may not work well without a proper product ID
                // We'll need to modify the HTML to include proper product IDs
                console.warn('Product ID not found, unable to add to cart via database');
                
                // For now, show a message to the user
                alert('Please refresh the page to ensure product IDs are loaded before adding to cart.');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    }
    
    // Function to add item to wishlist
    async function addToWishlist(item) {
        try {
            const equipmentItem = this.closest('.equipment-item');
            const productId = equipmentItem ? equipmentItem.getAttribute('data-product-id') : null;
            
            if (productId) {
                // Use the API to add to wishlist
                // For now, we'll just show a message since we don't have a wishlist API
                alert(`Item ${item.name} added to wishlist! (Wishlist functionality will be implemented with database storage)`);
            } else {
                alert('Product ID not found. Please ensure the product has proper ID attributes.');
            }
        } catch (error) {
            console.error('Error adding to wishlist:', error);
        }
    }
    
    // Function to update cart counter display
    async function updateCartCounter() {
        try {
            const response = await fetch('api/cart_api.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                const totalItems = data.count || 0;
                
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
        } catch (error) {
            console.error('Error updating cart counter:', error);
        }
    }
    
    // Initialize cart counter on page load
    updateCartCounter();
});