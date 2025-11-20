/**
 * wishlist.js
 * Handles wishlist page functionality including loading, displaying, and managing wishlist items
 */

document.addEventListener('DOMContentLoaded', function() {
    const wishlistBody = document.getElementById('wishlist-body');
    
    // Function to load wishlist from localStorage
    function loadWishlist() {
        const wishlist = JSON.parse(localStorage.getItem('medilightWishlist')) || [];
        
        if (wishlist.length === 0) {
            wishlistBody.innerHTML = '<tr><td colspan="4" class="text-center">Your wishlist is empty. Start adding items!</td></tr>';
            return;
        }
        
        // Clear the current wishlist table body
        wishlistBody.innerHTML = '';
        
        // Add each wishlist item to the table
        wishlist.forEach(item => {
            const row = document.createElement('tr');
            
            // Format the price with commas
            const formattedPrice = item.price.toLocaleString('en-US');
            
            row.innerHTML = `
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="${item.image}" alt="${item.name}" class="product-image" style="width: 60px; height: 60px; object-fit: cover;">
                        <span>${item.name}</span>
                    </div>
                </td>
                <td class="price">Ksh ${formattedPrice}</td>
                <td class="quantity">1</td>
                <td>
                    <div class="action-container">
                        <button class="add-to-cart-btn" data-id="${item.id}" data-name="${item.name}" data-price="${item.price}" data-image="${item.image}">Add to Cart</button>
                        <button class="remove-from-wishlist" data-id="${item.id}">Remove</button>
                    </div>
                </td>
            `;
            
            wishlistBody.appendChild(row);
        });
        
        // Add event listeners to the new buttons
        attachEventListeners();
    }
    
    // Function to attach event listeners to buttons
    function attachEventListeners() {
        // Add to cart buttons
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = parseFloat(this.getAttribute('data-price'));
                const image = this.getAttribute('data-image');
                
                // Create item object
                const item = {
                    id: id,
                    name: name,
                    price: price,
                    image: image,
                    quantity: 1
                };
                
                // Add to cart functionality
                addToCart(item);
            });
        });
        
        // Remove from wishlist buttons
        const removeFromWishlistButtons = document.querySelectorAll('.remove-from-wishlist');
        removeFromWishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                removeFromWishlist(id);
            });
        });
    }
    
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
        
        // Show confirmation
        alert(`${item.name} added to cart!`);
        
        // Update cart counter display
        updateCartCounter();
    }
    
    // Function to remove item from wishlist
    function removeFromWishlist(id) {
        let wishlist = JSON.parse(localStorage.getItem('medilightWishlist')) || [];
        
        // Filter out the item with the given ID
        const updatedWishlist = wishlist.filter(item => item.id !== id);
        
        // Save updated wishlist to localStorage
        localStorage.setItem('medilightWishlist', JSON.stringify(updatedWishlist));
        
        // Reload the wishlist display
        loadWishlist();
        
        // Show confirmation
        alert('Item removed from wishlist!');
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
    
    // Initialize the wishlist on page load
    loadWishlist();
    
    // Initialize cart counter on page load
    updateCartCounter();
});