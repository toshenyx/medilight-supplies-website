/**
 * js/cart_page.js
 * * Handles the rendering and dynamic interaction on the cart.html page.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Shared utility function (must match the one in product_listing.js)
    const getCart = () => {
        const cartString = localStorage.getItem('medilightCart');
        return cartString ? JSON.parse(cartString) : [];
    };

    const saveCart = (cart) => {
        localStorage.setItem('medilightCart', JSON.stringify(cart));
        renderCart(); // Re-render the cart table after any change
        // We rely on product_listing.js (which is also loaded) to update the header count
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
    const renderCart = () => {
        const cart = getCart();
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
                        <img src="${item.image}" alt="${item.name}" class="cart-product-image">
                        <span>${item.name}</span>
                    </div>
                </td>
                <td class="price-col">Ksh ${item.price.toLocaleString('en-US')}</td>
                <td class="quantity-col">
                    <div class="quantity-controls">
                        <button class="qty-minus" data-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" min="1" data-id="${item.id}" readonly>
                        <button class="qty-plus" data-id="${item.id}">+</button>
                    </div>
                </td>
                <td class="total-col price-highlight">Ksh ${totalItemPrice.toLocaleString('en-US')}</td>
                <td class="remove-col">
                    <button class="remove-item" data-id="${item.id}">&times;</button>
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
    const handleQuantityChange = (itemId, change) => {
        let cart = getCart();
        const itemIndex = cart.findIndex(item => item.id === itemId);

        if (itemIndex > -1) {
            cart[itemIndex].quantity += change;

            // Ensure quantity doesn't drop below 1
            if (cart[itemIndex].quantity < 1) {
                // If it hits zero or less, remove the item
                cart.splice(itemIndex, 1); 
            }
            saveCart(cart);
        }
    };

    /**
     * Handles item removal.
     */
    const handleRemoveItem = (itemId) => {
        let cart = getCart();
        const newCart = cart.filter(item => item.id !== itemId);
        saveCart(newCart);
    };
    
    // Event delegation for the entire cart table
    if (cartBody) {
        cartBody.addEventListener('click', (event) => {
            const target = event.target;
            const itemId = target.getAttribute('data-id');

            if (!itemId) return; // Ignore clicks without an ID

            if (target.classList.contains('qty-plus')) {
                handleQuantityChange(itemId, 1);
            } else if (target.classList.contains('qty-minus')) {
                handleQuantityChange(itemId, -1);
            } else if (target.classList.contains('remove-item')) {
                handleRemoveItem(itemId);
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