/**
 * js/delivery_details.js
 * * Handles the final order summary and order submission logic on delivery_details.html.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Utility functions (must match the others)
    const getCart = () => {
        const cartString = localStorage.getItem('medilightCart');
        return cartString ? JSON.parse(cartString) : [];
    };

    const cart = getCart();
    const summaryContainer = document.getElementById('checkoutSummary');
    const grandTotalDisplay = document.getElementById('grandTotal');
    const deliveryForm = document.getElementById('deliveryForm');

    // --- Functions ---

    /**
     * Renders the final order summary in the sidebar.
     */
    const renderSummary = () => {
        if (cart.length === 0) {
            summaryContainer.innerHTML = '<p style="color:red;">Your cart is empty. Please return to the cart page.</p>';
            grandTotalDisplay.textContent = 'Ksh 0.00';
            return;
        }

        let summaryHTML = '';
        let total = 0;
        const shippingFee = 500; // Fixed shipping fee example

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            summaryHTML += `
                <div class="summary-item">
                    <span>${item.name} (x${item.quantity})</span>
                    <span>Ksh ${itemTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
            `;
        });
        
        // Add shipping fee row
        summaryHTML += `
            <div class="summary-item" style="border-bottom: none;">
                <span style="font-weight: bold;">Shipping Fee:</span>
                <span style="font-weight: bold;">Ksh ${shippingFee.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
            </div>
        `;

        summaryContainer.innerHTML = summaryHTML;
        
        const grandTotal = total + shippingFee;
        grandTotalDisplay.textContent = `Ksh ${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
    };

    /**
     * Clears the cart and redirects on successful order submission.
     */
    const handleOrderSubmission = (event) => {
        event.preventDefault();

        if (cart.length === 0) {
            alert('Cannot place an empty order. Redirecting to Cart.');
            window.location.href = 'cart.html';
            return;
        }

        // --- Core Action: Clear the cart ---
        localStorage.removeItem('medilightCart');
        
        // Simulate Order Processing (In a real application, data would be sent to a server here)
        alert('Order placed successfully! Redirecting to confirmation page.');

        // Redirect to the thank you page
        window.location.href = 'thank_you.html';
    };

    // --- Initialization ---

    // 1. Render the cart summary
    renderSummary();

    // 2. Attach submission handler to the form
    if (deliveryForm) {
        deliveryForm.addEventListener('submit', handleOrderSubmission);
    }
});