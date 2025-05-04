<?php
// lang/en.php
$lang = [
    // General UI
    'NAV_CART' => 'Cart',
    'NAV_ADMIN_PANEL' => 'Admin Panel',
    'NAV_VIEW_STORE' => 'View Store Front',
    'FOOTER_COPYRIGHT' => '© %d Student Supplies Store', // %d will be replaced by year

    // Home Page (index.php)
    'HOME_PRODUCTS_TITLE' => 'Our Products',
    'SEARCH_PLACEHOLDER' => 'Search products by name...',
    'SORT_BY_LABEL' => 'Sort by:',
    'SORT_NAME_ASC' => 'Name (A-Z)',
    'SORT_NAME_DESC' => 'Name (Z-A)',
    'SORT_PRICE_ASC' => 'Price (Low-High)',
    'SORT_PRICE_DESC' => 'Price (High-Low)',
    'SORT_CATEGORY' => 'Category',
    'FILTER_ALL' => 'All',
    'FILTER_CATEGORIES' => 'Categories',
    'NO_CATEGORIES_FOUND' => 'No categories found',
    'PRODUCT_CATEGORY_LABEL' => 'Category:',
    'BTN_VIEW_DETAILS' => 'View Details',
    'BTN_ADD_TO_CART' => 'Add to Cart',
    'NO_PRODUCTS_FOUND' => 'No products found.',

    // Product Page (product.php)
    'PRODUCT_NOT_FOUND_TITLE' => 'Product Not Found',
    'PRODUCT_PRICE_LABEL' => 'Price:',
    'PRODUCT_DESCRIPTION_LABEL' => 'Description',
    'BTN_BACK_TO_PRODUCTS' => '« Back to Products',
    'ALERT_PRODUCT_NOT_FOUND' => 'Product not found or invalid ID provided.',
    'ALERT_RETURN_TO_SHOP' => 'Return to Shop',

    // Cart Page (cart.php)
    'CART_TITLE' => 'Your Shopping Cart',
    'CART_LOADING' => 'Loading your cart...',
    'CART_TABLE_PRODUCT' => 'Product',
    'CART_TABLE_QUANTITY' => 'Quantity',
    'CART_TABLE_UNIT_PRICE' => 'Unit Price',
    'CART_TABLE_TOTAL' => 'Total',
    'CART_TABLE_REMOVE' => 'Remove',
    'CART_SUMMARY_TOTAL' => 'Total:',
    'BTN_CONTINUE_SHOPPING' => 'Continue Shopping',
    'BTN_PROCEED_TO_CHECKOUT' => 'Proceed to Checkout',
    'CART_EMPTY_MESSAGE' => 'Your cart is currently empty.',
    'CART_EMPTY_START_SHOPPING' => 'Start Shopping!',

    // Checkout Page (checkout.php)
    'CHECKOUT_TITLE' => 'Checkout',
    'CHECKOUT_SUMMARY_TITLE' => 'Order Summary',
    'CHECKOUT_SUMMARY_TEXT' => 'Please confirm your details and place the order.',
    'CHECKOUT_DETAILS_TITLE' => 'Your Details',
    'CHECKOUT_NAME_LABEL' => 'Full Name',
    'CHECKOUT_EMAIL_LABEL' => 'Email Address',
    'BTN_PLACE_ORDER' => 'Place Order',
    'BTN_PLACE_ORDER_EMPTY' => 'Your Cart is Empty',
    'BTN_BACK_TO_CART' => '« Back to Cart',
    'BTN_SEARCH' => 'Search',
    'ALERT_ADDED_TO_CART' => '"%s" has been added to your cart.',
    'ALERT_CART_EMPTY_CHECKOUT' => 'Your cart is empty. Please add items before checking out.',
    'CHECKOUT_VALIDATION_ERROR' => 'Please fix the errors below:', // Added

    // Order Success Page (order_success.php)
    'ORDER_SUCCESS_TITLE' => 'Order Successful',
    'ORDER_SUCCESS_HEADING' => 'Thank You!',
    'ORDER_SUCCESS_MESSAGE' => 'Your order has been placed successfully.',
    'ORDER_SUCCESS_ID_LABEL' => 'Your Order ID is:',
    'ORDER_SUCCESS_NOTICE' => 'Thank you for your order! A confirmation email is on its way.',
    'ORDER_SUCCESS_EMAIL_SENT_TO' => 'We’ve sent it to: %s',


    // Flash Msg
    'ORDER_PLACED_SUCCESS' => 'Your order has been placed successfully.',
    'ORDER_PLACED_ERROR' => 'There was an error placing your order.',
    'ADMIN_PRODUCT_ADDED_SUCCESS' => 'Product added successfully.',
    'ADMIN_PRODUCT_ADDED_ERROR' => 'Failed to add the product.',
    'ADMIN_PRODUCT_UPDATED_SUCCESS' => 'Product updated successfully.',
    'ADMIN_PRODUCT_UPDATED_ERROR' => 'Failed to update the product.',
    'ADMIN_PRODUCT_DELETED_SUCCESS' => 'Product deleted successfully.',
    'ADMIN_PRODUCT_DELETED_ERROR' => 'Failed to delete the product.',
    'ADMIN_INVALID_PRODUCT_ID' => 'Invalid product ID.',
    'ADMIN_PRODUCT_NOT_FOUND_DELETE' => 'Product not found. Unable to delete.',
    'ADMIN_FORM_VALIDATION_ERROR' => 'Please correct the errors in the form.',
    'ADMIN_VALIDATE_NAME_REQUIRED' => 'Product name is required.',
    'ADMIN_VALIDATE_PRICE_INVALID' => 'Invalid price value.',
    'ADMIN_VALIDATE_IMAGE_TYPE' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.',
    'ADMIN_VALIDATE_IMAGE_SIZE' => 'Image size exceeds the allowed limit.',
    'ADMIN_VALIDATE_IMAGE_UPLOAD_ERROR' => 'Error uploading the image.',
    'ORDER_SUCCESS_INFO' => 'You will receive a confirmation email shortly.',
    'ORDER_SUCCESS_ORDER_ID' => 'Your order ID is:',


    // Admin General
    'ADMIN_BTN_BACK_TO_LIST' => '« Back to List', // Example generic back button
    'WELCOME_ADMIN' => 'Welcome, %s!', // <-- ADDED
    'BTN_LOGOUT' => 'Logout',         // <-- ADDED

    // Admin Charts
    'ADMIN_CHART_TITLE' => 'Top 5 Most Purchased Products',
    'ADMIN_CHART_LABEL_QUANTITY' => 'Quantity Sold',
    'ADMIN_CHART_NO_DATA' => 'Not enough sales data to display the chart yet.',
    'ADMIN_CHART_ERROR' => 'Could not load chart data.',

    // Admin Dashboard (admin/index.php)
    'ADMIN_PRODUCT_ADD_SUCCESS' => 'Product added successfully!',
    'ADMIN_PRODUCT_UPDATE_SUCCESS' => 'Product updated successfully!',
    'ADMIN_PRODUCT_DELETE_SUCCESS' => 'Product deleted successfully!',
    'ADMIN_GENERAL_ERROR' => 'An error occurred. Please try again.',
    'ADMIN_DASHBOARD_TITLE' => 'Dashboard',
    'ADMIN_DASHBOARD_WELCOME' => 'Welcome to the Admin Panel.',
    'ADMIN_DASHBOARD_INFO' => 'Use the sidebar navigation to manage products or view orders.',
    'ADMIN_MANAGE_PRODUCTS_TITLE' => 'Manage Products',
    'ADMIN_MANAGE_PRODUCTS_TEXT' => 'Add, edit, or delete products available in the store.',
    'ADMIN_MANAGE_PRODUCTS_BTN' => 'Go to Products',
    'ADMIN_VIEW_ORDERS_TITLE' => 'View Orders',
    'ADMIN_VIEW_ORDERS_TEXT' => 'Review customer orders placed through the store.',
    'ADMIN_VIEW_ORDERS_BTN' => 'Go to Orders',

    // Admin Products (admin/products.php)
    // Uses ADMIN_MANAGE_PRODUCTS_TITLE
    'ADMIN_PRODUCTS_ADD_NEW' => 'Add New Product',
    'ADMIN_PRODUCTS_TABLE_ID' => 'ID',
    'ADMIN_PRODUCTS_TABLE_IMAGE' => 'Image',
    'ADMIN_PRODUCTS_TABLE_NAME' => 'Name',
    'ADMIN_PRODUCTS_TABLE_CATEGORY' => 'Category',
    'ADMIN_PRODUCTS_TABLE_PRICE' => 'Price',
    'ADMIN_PRODUCTS_TABLE_ACTIONS' => 'Actions',
    'ADMIN_PRODUCTS_EDIT_TITLE' => 'Edit',
    'ADMIN_PRODUCTS_DELETE_TITLE' => 'Delete',
    'ADMIN_PRODUCTS_DELETE_CONFIRM' => 'Are you sure you want to delete this product: %s?', // %s for product name
    'ADMIN_PRODUCTS_NONE_FOUND' => 'No products found.',
    'ADMIN_PRODUCTS_MATCHING_SEARCH' => 'matching your search', // Added for search context

    // Admin Product Form (admin/product_form.php)
    'ADMIN_PRODUCT_FORM_ADD_TITLE' => 'Add New Product',
    'ADMIN_PRODUCT_FORM_EDIT_TITLE' => 'Edit Product',
    'ADMIN_PRODUCT_FORM_NAME' => 'Product Name',
    'ADMIN_PRODUCT_FORM_DESCRIPTION' => 'Description',
    'ADMIN_PRODUCT_FORM_PRICE' => 'Price',
    'ADMIN_PRODUCT_FORM_CATEGORY' => 'Category',
    'ADMIN_PRODUCT_FORM_IMAGE' => 'Product Image File',
    'ADMIN_PRODUCT_FORM_IMAGE_HELP' => 'Upload a new image (JPG, PNG, GIF, WEBP). If left empty when editing, the existing image will be kept.',
    'ADMIN_PRODUCT_FORM_CURRENT_IMAGE' => 'Current Image:',
    'ADMIN_PRODUCT_FORM_IMAGE_NOT_FOUND' => 'Current image path stored (%s), but file not found at %s.', // %s for paths
    'ADMIN_PRODUCT_FORM_BTN_ADD' => 'Add Product',
    'ADMIN_PRODUCT_FORM_BTN_UPDATE' => 'Update Product',
    'ADMIN_PRODUCT_FORM_BTN_CANCEL' => 'Cancel',
    'ADMIN_PRODUCT_FORM_REQUIRED' => '<span class="text-danger">*</span>', // Required indicator

     // Admin Orders (admin/orders.php)
    'ADMIN_ORDERS_LIST_TITLE' => 'Customer Orders',
    'ADMIN_ORDERS_TABLE_ID' => 'Order ID',
    'ADMIN_ORDERS_TABLE_CUST_NAME' => 'Customer Name',
    'ADMIN_ORDERS_TABLE_CUST_EMAIL' => 'Customer Email',
    'ADMIN_ORDERS_TABLE_TOTAL' => 'Total Price',
    'ADMIN_ORDERS_TABLE_DATE' => 'Order Date',
    'ADMIN_ORDERS_TABLE_ACTIONS' => 'Actions',
    'ADMIN_ORDERS_BTN_VIEW' => 'View',
    'ADMIN_ORDERS_NONE_FOUND' => 'No orders found.',

    // Admin Order Details (admin/order_details.php)
    'ADMIN_ORDER_DETAILS_TITLE' => 'Order Details #%d', // %d for order ID
    'ADMIN_ORDER_DETAILS_SUMMARY' => 'Order Summary',
    'ADMIN_ORDER_DETAILS_ID' => 'Order ID:',
    'ADMIN_ORDER_DETAILS_CUST_NAME' => 'Customer Name:',
    'ADMIN_ORDER_DETAILS_CUST_EMAIL' => 'Customer Email:',
    'ADMIN_ORDER_DETAILS_TOTAL' => 'Total Price:',
    'ADMIN_ORDER_DETAILS_DATE' => 'Order Date:',
    'ADMIN_ORDER_DETAILS_ITEMS_HEADING' => 'Items in this Order',
    'ADMIN_ORDER_DETAILS_TABLE_PROD_ID' => 'Product ID',
    'ADMIN_ORDER_DETAILS_TABLE_IMAGE' => 'Image',
    'ADMIN_ORDER_DETAILS_TABLE_PROD_NAME' => 'Product Name',
    'ADMIN_ORDER_DETAILS_TABLE_QUANTITY' => 'Quantity',
    'ADMIN_ORDER_DETAILS_TABLE_UNIT_PRICE' => 'Unit Price',
    'ADMIN_ORDER_DETAILS_TABLE_SUBTOTAL' => 'Subtotal',
    'ADMIN_ORDER_DETAILS_NO_ITEMS' => 'No items found for this order (this might indicate an issue).',
    'ADMIN_ORDER_DETAILS_ITEM_ERROR' => 'Could not load items due to an error.',

    // Potentially JS Alerts from cart.js (Need mechanism to pass these)
    'JS_ALERT_ADDED_TO_CART' => '"%s" has been added to your cart.', // %s for product name
    'JS_ALERT_INVALID_QUANTITY' => 'Invalid quantity entered. Please enter a number greater than or equal to 0.',
    'JS_ALERT_CHECKOUT_CART_EMPTY' => 'Your cart is empty. Please add items before checking out.',
    'JS_ALERT_ERROR_SAVING_CART' => 'Could not save cart. Storage might be full.', // Added
    'JS_ALERT_ERROR_INVALID_PRICE' => 'Cannot add item: Invalid product price.', // Added
    'JS_ALERT_ERROR_MISSING_DATA' => 'Cannot add item: Missing product data.', // Added
    'JS_ALERT_ERROR_BUTTON_DATA' => 'Could not add item: Button data is missing.', // Added

];
// Note: Removed closing PHP tag ?>