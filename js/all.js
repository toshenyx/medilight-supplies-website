// Combined content of all JavaScript files

// aboutjs.js
document.addEventListener("DOMContentLoaded", () => {
    const hero = document.querySelector(".hero-left");
    const mvSections = document.querySelectorAll(".mv");

    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const scrollTop = window.scrollY;

        // reveal hero (only if exists)
        if (hero) {
            const heroPos = hero.getBoundingClientRect().top + scrollTop;
            if (scrollTop + windowHeight > heroPos + 100) {
                hero.classList.add("show");
            }
        }

        // reveal mission/vision sections (if exist)
        mvSections.forEach(sec => {
            const secPos = sec.getBoundingClientRect().top + scrollTop;
            if (scrollTop + windowHeight > secPos + 50) {
                sec.classList.add("show");
            }
        });
    };

    window.addEventListener("scroll", revealOnScroll);
    revealOnScroll(); // initial call

    // Interactive core values (safe check)
    const values = document.querySelectorAll(".val");
    values.forEach(v => {
        v.addEventListener("click", () => {
            const value = v.dataset.value || "Unknown";
            alert(`Core Value: ${value}`);
        });
    });

    // Learn More button animation (safe check)
    const learnBtn = document.getElementById("learnBtn");
    if (learnBtn) {
        learnBtn.addEventListener("click", (e) => {
            e.preventDefault();
            learnBtn.textContent = "Thanks for your interest!";
            learnBtn.style.background = "#00b3b3";
            learnBtn.style.color = "#fff";
            setTimeout(() => {
                learnBtn.textContent = "Learn More";
                learnBtn.style.background = "#fff";
                learnBtn.style.color = "#16374a";
            }, 2000);
        });
    }
});

// js.js
document.addEventListener("DOMContentLoaded", function () {
    const slider = document.getElementById("heroSlider");
    const slides = Array.from(slider.querySelectorAll(".slide"));
    const prevBtn = slider.querySelector(".prev");
    const nextBtn = slider.querySelector(".next");
    const dotsWrap = slider.querySelector(".dots");
    let idx = 0, timer = null, delay = 5000;

    function createDots() {
        slides.forEach((_, i) => {
            const btn = document.createElement("button");
            btn.addEventListener("click", () => goTo(i));
            dotsWrap.appendChild(btn);
        });
        updateDots();
    }
    function updateDots() {
        Array.from(dotsWrap.children).forEach((d, i) =>
            d.classList.toggle("active", i === idx)
        );
    }
    function showSlide(i) {
        slides.forEach((s, j) => s.classList.toggle("active", j === i));
        idx = i; updateDots();
    }
    function next() { showSlide((idx + 1) % slides.length); restart(); }
    function prev() { showSlide((idx - 1 + slides.length) % slides.length); restart(); }
    function start() { stop(); timer = setInterval(next, delay); }
    function stop() { if (timer) clearInterval(timer); timer = null; }
    function restart() { stop(); start(); }

    prevBtn.addEventListener("click", prev);
    nextBtn.addEventListener("click", next);
    slider.addEventListener("mouseenter", stop);
    slider.addEventListener("mouseleave", start);

    createDots();
    showSlide(0);
    start();

    document.getElementById("year").textContent = new Date().getFullYear();

    // Search functionality
    const searchInput = document.querySelector(".search input");
    const allTextBlocks = document.querySelectorAll("h1, h2, h3, h4, p, li");

    searchInput.addEventListener("keypress", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            const term = searchInput.value.toLowerCase().trim();
            if (!term) return;
            document.querySelectorAll(".highlight").forEach(el => el.classList.remove("highlight"));
            let found = false;
            allTextBlocks.forEach(el => {
                if (el.textContent.toLowerCase().includes(term)) {
                    el.classList.add("highlight");
                    if (!found) el.scrollIntoView({ behavior: "smooth", block: "center" });
                    found = true;
                }
            });
            if (!found) alert(`No results found for "${term}".`);
        }
    });
});

// js/cart.js
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

// js/delivery_details.js
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

        // Get order data from localStorage if it exists
        const orderData = JSON.parse(localStorage.getItem('currentOrder') || '{}');
        
        // Prepare form data to send to the server
        const formData = new FormData(deliveryForm);
        
        // Add cart data to form
        formData.append('cart', JSON.stringify(cart));
        formData.append('order_data', JSON.stringify(orderData));

        // Send data to server using fetch
        fetch('process_delivery.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear cart after successful order
                localStorage.removeItem('medilightCart');
                
                // Show success message and redirect
                alert(data.message || 'Order placed successfully!');
                
                // Redirect to thank you page
                window.location.href = data.redirect || 'thank_you.html';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your order. Please try again.');
        });
    };

    // --- Initialization --- 

    // 1. Render the cart summary
    renderSummary();

    // 2. Attach submission handler to the form
    if (deliveryForm) {
        deliveryForm.addEventListener('submit', handleOrderSubmission);
    }
});

// js/order_page.js
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

// js/product_listing.js
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
