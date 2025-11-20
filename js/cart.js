/**
 * js/cart_page.js
 * Handles the rendering and dynamic interaction on the cart.html page using database storage.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Get cart from database API
    const getCart = async () => {
        try {
            const response = await fetch('api/cart_api.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            return data.success ? data.items : [];
        } catch (error) {
            console.error('Error fetching cart:', error);
            return [];
        }
    };

    // Save cart to database via API
    const saveCart = async (cart) => {
        renderCart(); // Re-render the cart table after any change
        // We rely on other functions to update the header count
    };

    const cartBody = document.getElementById('cart-items-body');
    const subtotalDisplay = document.getElementById('cart-subtotal');
    const checkoutButton = document.getElementById('checkout-btn');

    // --- Core Functions ---

    /**
     * Calculates the total price of all items in the cart.
     * @param {Array} cart - The cart array.
     * @returns {number} The total subtotal.
     */
    const calculateSubtotal = (cart) => {
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    };

    /**
     * Renders the cart table dynamically.
     */
    const renderCart = async () => {
        const cart = await getCart();
        cartBody.innerHTML = ''; // Clear previous content

        if (cart.length === 0) {
            cartBody.innerHTML = '<tr><td colspan="5" class="text-center">Your cart is empty. Start shopping!</td></tr>';
            subtotalDisplay.textContent = 'Ksh 0.00';
            checkoutButton.disabled = true;
            return;
        }

        cart.forEach(item => {
            const totalItemPrice = (item.price * item.quantity).toFixed(2);
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="product-col">
                    <div class="cart-product-info">
                        <img src="${item.image_path || 'images/placeholder.jpg'}" alt="${item.name}" class="cart-product-image">
                        <span>${item.name}</span>
                    </div>
                </td>
                <td class="price-col">Ksh ${item.price.toLocaleString('en-US')}</td>
                <td class="quantity-col">
                    <div class="quantity-controls">
                        <button class="qty-minus" data-cart-id="${item.cart_id}">-</button>
                        <input type="number" value="${item.quantity}" min="1" data-cart-id="${item.cart_id}" readonly>
                        <button class="qty-plus" data-cart-id="${item.cart_id}">+</button>
                    </div>
                </td>
                <td class="total-col price-highlight">Ksh ${totalItemPrice.toLocaleString('en-US')}</td>
                <td class="remove-col">
                    <button class="remove-item" data-cart-id="${item.cart_id}">&times;</button>
                </td>
            `;
            cartBody.appendChild(row);
        });

        // Update subtotal display
        const subtotal = calculateSubtotal(cart);
        subtotalDisplay.textContent = `Ksh ${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        checkoutButton.disabled = false;
    };

    // --- Event Handlers ---

    /**
     * Handles quantity changes (plus/minus buttons).
     */
    const handleQuantityChange = async (cartId, change) => {
        // Get the current quantity from the input field
        const inputField = document.querySelector(`input[data-cart-id="${cartId}"]`);
        if (!inputField) return;
        
        let newQuantity = parseInt(inputField.value) + change;
        if (newQuantity < 1) newQuantity = 1; // Ensure quantity doesn't go below 1
        
        try {
            const response = await fetch('api/cart_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'update',
                    cart_id: cartId,
                    quantity: newQuantity
                })
            });
            
            const result = await response.json();
            if (result.success) {
                renderCart(); // Re-render to reflect changes
            } else {
                console.error('Error updating cart:', result.message);
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    };

    /**
     * Handles item removal.
     */
    const handleRemoveItem = async (cartId) => {
        try {
            const response = await fetch('api/cart_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'remove',
                    cart_id: cartId
                })
            });
            
            const result = await response.json();
            if (result.success) {
                renderCart(); // Re-render to reflect changes
            } else {
                console.error('Error removing item:', result.message);
            }
        } catch (error) {
            console.error('Error removing item:', error);
        }
    };
    
    // Event delegation for the entire cart table
    if (cartBody) {
        cartBody.addEventListener('click', (event) => {
            const target = event.target;
            const cartId = target.getAttribute('data-cart-id');

            if (!cartId) return; // Ignore clicks without an ID

            if (target.classList.contains('qty-plus')) {
                handleQuantityChange(cartId, 1);
            } else if (target.classList.contains('qty-minus')) {
                handleQuantityChange(cartId, -1);
            } else if (target.classList.contains('remove-item')) {
                handleRemoveItem(cartId);
            }
        });
    }

    // Initialize the cart view
    renderCart();
    
    // Checkout button redirect
    if (checkoutButton) {
        checkoutButton.addEventListener('click', () => {
            window.location.href = 'delivery_details.html';
        });
    }
});