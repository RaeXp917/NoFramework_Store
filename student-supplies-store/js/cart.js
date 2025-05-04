/**
 * Student Supplies Store Cart Functionality
 * Uses localStorage to persist cart data
 * Includes basic internationalization using globalLangStrings object provided by PHP.
 */

// --- Core Cart Functions ---

/**
 * Retrieves the cart object from localStorage.
 * @returns {object} The cart object (key: productId, value: {name, price, quantity}) or an empty object.
 */
function getCart() {
    const cartString = localStorage.getItem('shoppingCart');
    try {
        // If cartString is null, undefined, or empty, return {}
        // Otherwise, parse it.
        return cartString ? JSON.parse(cartString) : {};
    } catch (e) {
        console.error("Error parsing cart JSON from localStorage:", e);
        // Clear potentially corrupted cart data on parsing error
        localStorage.removeItem('shoppingCart');
        return {}; // Return empty object on error
    }
}

/**
 * Saves the cart object to localStorage.
 * @param {object} cart - The cart object to save.
 */
function saveCart(cart) {
    try {
        // Basic check: ensure cart is an object before stringifying
        if (typeof cart !== 'object' || cart === null) {
            console.error("saveCart: Attempted to save non-object data:", cart);
            return;
        }
        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartIcon(); // Update icon after successful save
    } catch (e) {
        console.error("Error saving cart to localStorage:", e);
        // Use a generic alert or a translated one if available
        const alertMsg = (typeof globalLangStrings !== 'undefined' && globalLangStrings.errorSavingCart)
                         ? globalLangStrings.errorSavingCart
                         : "Could not save cart. Storage might be full.";
        alert(alertMsg);
    }
}

/**
 * Updates the number displayed on the cart icon in the navbar.
 */
function updateCartIcon() {
    const cart = getCart();
    let totalItems = 0;
    for (const productId in cart) {
        // Ensure it's a direct property and quantity is a valid number
        if (Object.prototype.hasOwnProperty.call(cart, productId) && cart[productId] && typeof cart[productId].quantity === 'number') {
            totalItems += cart[productId].quantity;
        }
    }
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    } else {
        console.warn("Cart count element ('cart-count') not found in the DOM.");
    }
}

/**
 * Adds a product to the shopping cart or increments its quantity if already present.
 * @param {string} productId - The unique ID of the product.
 * @param {string} productName - The name of the product.
 * @param {number|string} productPrice - The price of the product.
 */
function addToCart(productId, productName, productPrice) {
    const cart = getCart();
    const price = parseFloat(productPrice);

    // Input validation
    if (isNaN(price) || price < 0) { // Also check for negative price
        console.error("Invalid product price:", productPrice);
        const alertMsg = (typeof globalLangStrings !== 'undefined' && globalLangStrings.errorInvalidPrice)
                         ? globalLangStrings.errorInvalidPrice
                         : "Cannot add item: Invalid product price.";
        alert(alertMsg);
        return;
    }
     if (!productId || !productName) {
        console.error("Invalid product data:", { productId, productName, productPrice });
         const alertMsg = (typeof globalLangStrings !== 'undefined' && globalLangStrings.errorMissingData)
                         ? globalLangStrings.errorMissingData
                         : "Cannot add item: Missing product data.";
        alert(alertMsg);
        return;
    }

    // Add or update item
    if (cart[productId]) {
        // Ensure quantity is a number before incrementing
        cart[productId].quantity = (typeof cart[productId].quantity === 'number' ? cart[productId].quantity : 0) + 1;
    } else {
        cart[productId] = { name: productName, price: price, quantity: 1 };
    }

    saveCart(cart); // Save the updated cart
    console.log(`Added/Updated ${productName} in cart. Current cart:`, getCart()); // Log action

    // Alert user (using translation if available)
    if (typeof globalLangStrings !== 'undefined' && globalLangStrings.addedToCart) {
        // Use replace for placeholder like '%s' if your translation uses it
        alert(globalLangStrings.addedToCart.replace('%s', productName));
    } else { // Fallback alert
        alert(`"${productName}" has been added to your cart.`);
    }
}

/**
 * Removes an item completely from the cart.
 * @param {string} productId - The ID of the product to remove.
 */
function removeFromCart(productId) {
    const cart = getCart();
    if (cart[productId]) {
        delete cart[productId]; // Remove the item
        saveCart(cart); // Save the change
        // Refresh display ONLY if we are currently on the cart page
        if (document.getElementById('cart-items-container')) {
            displayCartItems();
        }
        console.log(`Removed product ${productId} from cart.`);
    } else {
        console.warn(`Product ${productId} not found in cart for removal.`);
    }
}

/**
 * Updates the quantity of an item in the cart.
 * @param {string} productId - The ID of the product to update.
 * @param {number|string} newQuantity - The new quantity for the item.
 */
function updateCartQuantity(productId, newQuantity) {
    const cart = getCart();
    const quantity = parseInt(newQuantity, 10); // Ensure base 10

    // Validate quantity
    if (isNaN(quantity) || quantity < 0) {
        console.error("Invalid quantity entered:", newQuantity);
        // Use translated alert if available
        const alertMsg = (typeof globalLangStrings !== 'undefined' && globalLangStrings.invalidQuantity)
                         ? globalLangStrings.invalidQuantity
                         : "Invalid quantity entered. Please enter a number greater than or equal to 0.";
        alert(alertMsg);

        // Refresh display to show original value if on cart page
        if (document.getElementById('cart-items-container')) {
            displayCartItems();
        }
        return; // Stop processing
    }

    // Update or remove item based on quantity
    if (cart[productId]) {
        if (quantity === 0) {
            removeFromCart(productId); // Remove item if quantity is 0
        } else {
            cart[productId].quantity = quantity;
            saveCart(cart); // Save the updated quantity
            // Refresh display only if on cart page to update totals etc.
            if (document.getElementById('cart-items-container')) {
                displayCartItems();
            }
            console.log(`Updated quantity for ${productId} to ${quantity}.`);
        }
    } else {
        console.warn(`Product ${productId} not found in cart for quantity update.`);
    }
}


/**
 * Displays the items currently in the cart on the cart page.
 * Uses globalLangStrings for table headers and aria-labels.
 */
function displayCartItems() {
    console.log("Attempting to display cart items..."); // Log start

    // Check if globalLangStrings is defined BEFORE trying to use it
    if (typeof globalLangStrings === 'undefined') {
        console.error("CRITICAL: globalLangStrings object is not defined. Cannot display cart. Check PHP generation in cart.php.");
        const cartContainer = document.getElementById('cart-items-container');
         if (cartContainer) cartContainer.innerHTML = '<div class="alert alert-danger">Error: Cannot load language settings. Cart cannot be displayed.</div>';
        return; // Stop execution
    }

    const cart = getCart(); // Get current cart data

    // Get references to necessary DOM elements
    const cartContainer = document.getElementById('cart-items-container');
    const cartSummary = document.getElementById('cart-summary');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartTotalPriceElement = document.getElementById('cart-total-price');
    const checkoutButton = document.getElementById('checkout-button');

    // Check if all required HTML elements exist on the page
    if (!cartContainer || !cartSummary || !emptyCartMessage || !cartTotalPriceElement || !checkoutButton) {
        console.error("Cart page structure error: One or more required elements (#cart-items-container, #cart-summary, #empty-cart-message, #cart-total-price, #checkout-button) not found.");
        if (cartContainer) cartContainer.innerHTML = '<div class="alert alert-danger">Error displaying cart content. Page structure is incorrect.</div>';
        return; // Stop if page structure is wrong
    }

    cartContainer.innerHTML = ''; // Clear previous contents (like the loading spinner)
    let totalPrice = 0;
    const productIds = Object.keys(cart); // Get array of product IDs in cart

    // --- Get translated strings (with fallbacks) ---
    const headerProduct = globalLangStrings.cartTableProduct || 'Product';
    const headerQuantity = globalLangStrings.cartTableQuantity || 'Quantity';
    const headerUnitPrice = globalLangStrings.cartTableUnitPrice || 'Unit Price';
    const headerTotal = globalLangStrings.cartTableTotal || 'Total';
    const headerRemove = globalLangStrings.cartTableRemove || 'Remove';
    // Labels for accessibility
    const labelQuantity = globalLangStrings.quantityLabel || 'Quantity for';
    const labelRemove = globalLangStrings.removeLabel || 'Remove';


    // --- Handle Empty Cart vs. Cart with Items ---
    if (productIds.length === 0) {
        console.log("Cart is empty. Displaying empty message.");
        cartSummary.classList.add('d-none'); // Hide summary section
        emptyCartMessage.classList.remove('d-none'); // Show empty cart message
        checkoutButton.classList.add('disabled'); // Disable checkout button
        checkoutButton.setAttribute('aria-disabled', 'true'); // Accessibility
    } else {
        console.log("Cart has items. Building table:", cart);
        emptyCartMessage.classList.add('d-none'); // Hide empty cart message
        cartSummary.classList.remove('d-none'); // Show summary section
        checkoutButton.classList.remove('disabled'); // Enable checkout button
        checkoutButton.removeAttribute('aria-disabled'); // Accessibility

        // Create table structure
        const table = document.createElement('table');
        table.className = 'table table-hover align-middle'; // Bootstrap classes
        // Use translated headers in thead
        table.innerHTML = `
            <thead class="table-light">
                <tr>
                    <th scope="col" colspan="2">${headerProduct}</th>
                    <th scope="col" class="text-center">${headerQuantity}</th>
                    <th scope="col" class="text-end">${headerUnitPrice}</th>
                    <th scope="col" class="text-end">${headerTotal}</th>
                    <th scope="col" class="text-center">${headerRemove}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Cart items will be added here -->
            </tbody>
        `;
        const tbody = table.querySelector('tbody'); // Get reference to tbody

        // Loop through each product in the cart
        productIds.forEach(productId => {
            const item = cart[productId];

            // Validate item data structure within the loop
            if (!item || typeof item.name !== 'string' || typeof item.price !== 'number' || typeof item.quantity !== 'number') {
                console.error(`Invalid item data found in cart for Product ID ${productId}:`, item);
                // Optionally remove the bad item here: delete cart[productId]; saveCart(cart);
                return; // Skip rendering this invalid item
            }

            const itemTotalPrice = item.price * item.quantity;
            totalPrice += itemTotalPrice; // Add to grand total
            const escapedName = escapeHTML(item.name); // Escape name once for safe HTML insertion

            // Create table row for the item
            const row = document.createElement('tr');
            // Use translated aria-labels and format prices with Euro symbol
            row.innerHTML = `
                <td colspan="2">${escapedName}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm quantity-input mx-auto" value="${item.quantity}" min="0" data-id="${productId}" onchange="updateCartQuantity('${productId}', this.value)" aria-label="${labelQuantity} ${escapedName}">
                </td>
                <td class="text-end">€${item.price.toFixed(2)}</td>
                <td class="text-end fw-bold">€${itemTotalPrice.toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm remove-item-btn" data-id="${productId}" onclick="removeFromCart('${productId}')" aria-label="${labelRemove} ${escapedName}">
                        × <!-- Use HTML entity for '×' symbol -->
                    </button>
                </td>
            `;
            tbody.appendChild(row); // Add the row to the table body
        }); // End loop through cart items

        cartContainer.appendChild(table); // Add the completed table to the page
        cartTotalPriceElement.textContent = totalPrice.toFixed(2); // Update total price display
        console.log("Cart table built and displayed.");
    } // End else (cart has items)
}

// Helper function to escape HTML entities to prevent XSS
function escapeHTML(str) {
     // Ensure input is a string before attempting to escape
     if (typeof str !== 'string') {
         console.warn("escapeHTML called with non-string value:", str);
         return str || ''; // Return original value or empty string if null/undefined
     }
     // Use textContent assignment for safe escaping
     const div = document.createElement('div');
     div.textContent = str;
     return div.innerHTML;
}


// --- Event Listener Setup ---
// Runs after the basic HTML structure is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded. Setting up cart listeners and initial state.");
    updateCartIcon(); // Update cart icon count on every page load

    // Display cart items only if the specific container exists (i.e., we are on cart.php)
    if (document.getElementById('cart-items-container')) {
        console.log("Cart items container found, calling displayCartItems().");
        displayCartItems(); // Populate the cart table/message
    } else {
         console.log("Not on cart page (no 'cart-items-container' found).");
    }

    // Setup 'Add to Cart' button listeners if any exist on the current page
    // Using event delegation on a common ancestor might be more efficient if many buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    if (addToCartButtons.length > 0) {
        console.log(`Found ${addToCartButtons.length} 'Add to Cart' buttons. Attaching listeners...`);
        addToCartButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const buttonElement = event.currentTarget; // The button that was clicked
                // Retrieve product data from data-* attributes
                const productId = buttonElement.dataset.id;
                const productName = buttonElement.dataset.name;
                const productPrice = buttonElement.dataset.price;

                // Validate that data attributes exist before adding to cart
                if (productId && productName && productPrice !== undefined) {
                     console.log(`'Add to Cart' button clicked: ID=${productId}, Name=${productName}, Price=${productPrice}`);
                     addToCart(productId, productName, productPrice); // Call the function to add
                } else {
                    // Log error and alert user if data is missing
                    console.error("Missing or invalid data attributes on 'Add to Cart' button:", buttonElement.dataset);
                     const alertMsg = (typeof globalLangStrings !== 'undefined' && globalLangStrings.errorButtonData)
                                     ? globalLangStrings.errorButtonData
                                     : "Could not add item: Button data is missing.";
                    alert(alertMsg);
                }
            });
        });
    } else {
         console.log("No 'Add to Cart' buttons (.add-to-cart-btn) found on this page.");
    }
});