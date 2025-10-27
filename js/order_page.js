document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.product-card');
    const hiddenInput = document.getElementById('productCategoryHidden');
    const orderForm = document.getElementById('orderForm');

    // 1. Product Card Selection Logic
    cards.forEach(card => {
        card.addEventListener('click', function () {
            // Deselect all other cards
            cards.forEach(c => c.classList.remove('selected'));
            // Select the clicked card
            this.classList.add('selected');
            // Update the hidden input field with the selected category
            const category = this.getAttribute('data-product-category');
            hiddenInput.value = category;
        });
    });

    // 2. Form Submission Logic
    if (orderForm) {
        orderForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default browser form submission

            const productCategory = hiddenInput.value;
            
            // Validation: Must select a product category
            if (!productCategory) {
                alert("Please select a product category by clicking one of the cards before proceeding.");
                return;
            }

            // Collect and structure all form data
            const orderData = {
                clientName: document.getElementById('clientName').value,
                contactEmail: document.getElementById('contactEmail').value,
                productCategory: productCategory,
                quantity: document.getElementById('quantity').value,
                specificNeeds: document.getElementById('specificNeeds').value,
                orderDate: new Date().toLocaleDateString('en-US')
            };

            // Save data temporarily to the browser's storage
            localStorage.setItem('currentOrder', JSON.stringify(orderData));
            
            // Redirect to the next step
            window.location.href = 'delivery_details.html';
        });
    }
});