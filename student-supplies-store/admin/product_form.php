<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required'); // Redirect if not logged in
    exit;
}

// --- REST OF YOUR ADMIN PAGE CODE GOES BELOW ---
require_once '../config.php'; // Including configuration file
// ... any other necessary includes ...
?>
<!-- HTML or other PHP code for the admin page -->

<?php
require_once '../config.php'; // Includes language.php for translations

// Initialize variables for product details
$product_id = null; $product_name = ''; $product_description = ''; $product_price = ''; $product_category = ''; $product_image = '';
$form_action = 'save_product.php'; // The form submits to this script
$is_editing = false; // Flag to check if we're editing an existing product

// Check if we are editing an existing product
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id']; // Get product ID from URL
    $is_editing = true; // Set flag to true since we're editing

    // Fetch existing product details from the database
    $sql = "SELECT name, description, price, category, image FROM products WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $product_id); // Bind the product ID to the SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt); // Get the result
            if ($product = mysqli_fetch_assoc($result)) { // If product found, assign values
                $product_name = $product['name']; $product_description = $product['description']; $product_price = $product['price']; $product_category = $product['category']; $product_image = $product['image'];
            } else {
                // Handle case if product not found
                die("Error: Product with ID $product_id not found.");
            }
        } else { 
            die("Error executing statement: " . mysqli_stmt_error($stmt)); 
        }
        mysqli_stmt_close($stmt); // Close the statement after execution
    } else { 
        die("Error preparing statement: " . mysqli_error($conn)); 
    }
}

// Set page title and button label depending on whether we're editing or adding a product
$page_title = $is_editing ? t('ADMIN_PRODUCT_FORM_EDIT_TITLE') : t('ADMIN_PRODUCT_FORM_ADD_TITLE');
$button_label = $is_editing ? t('ADMIN_PRODUCT_FORM_BTN_UPDATE') : t('ADMIN_PRODUCT_FORM_BTN_ADD');

// Include the page header after setting the title
require_once 'partials/header.php'; 
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $page_title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="products.php" class="btn btn-secondary"> <i class="bi bi-arrow-left"></i> <?php echo t('ADMIN_BTN_BACK_TO_LIST'); ?> </a>
    </div>
</div>

<?php /* Placeholder for session error display if needed */
    // You can add logic here to display any errors or success messages
?>

<!-- Product form starts here -->
<form action="<?php echo $form_action; ?>" method="POST" enctype="multipart/form-data">
    <?php if ($product_id): ?> <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"> <?php endif; ?>

    <!-- Product Name -->
    <div class="mb-3">
        <label for="product_name" class="form-label"><?php echo t('ADMIN_PRODUCT_FORM_NAME'); ?> <?php echo t('ADMIN_PRODUCT_FORM_REQUIRED'); ?></label>
        <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
    </div>
    
    <!-- Product Description -->
    <div class="mb-3">
        <label for="product_description" class="form-label"><?php echo t('ADMIN_PRODUCT_FORM_DESCRIPTION'); ?></label>
        <textarea class="form-control" id="product_description" name="product_description" rows="4"><?php echo htmlspecialchars($product_description); ?></textarea>
    </div>
    
    <!-- Product Price -->
    <div class="mb-3">
        <label for="product_price" class="form-label"><?php echo t('ADMIN_PRODUCT_FORM_PRICE'); ?> <?php echo t('ADMIN_PRODUCT_FORM_REQUIRED'); ?></label>
        <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" step="0.01" min="0" required>
    </div>
    
    <!-- Product Category -->
    <div class="mb-3">
        <label for="product_category" class="form-label"><?php echo t('ADMIN_PRODUCT_FORM_CATEGORY'); ?></label>
        <input type="text" class="form-control" id="product_category" name="product_category" value="<?php echo htmlspecialchars($product_category); ?>">
    </div>
    
    <!-- Product Image -->
    <div class="mb-3">
        <label for="product_image_file" class="form-label"><?php echo t('ADMIN_PRODUCT_FORM_IMAGE'); ?></label>
        <input class="form-control" type="file" id="product_image_file" name="product_image_file" accept="image/jpeg, image/png, image/gif, image/webp">
        <small class="form-text text-muted"><?php echo t('ADMIN_PRODUCT_FORM_IMAGE_HELP'); ?></small>
        
        <!-- Display existing image if editing -->
        <?php if ($is_editing && !empty($product_image)):
            $imageDisplayPath = '../' . $product_image;
            if (file_exists($imageDisplayPath)) : ?>
            <div class="mt-2">
                <p><?php echo t('ADMIN_PRODUCT_FORM_CURRENT_IMAGE'); ?></p>
                <img src="<?php echo htmlspecialchars($imageDisplayPath); ?>" alt="Current Image" style="max-width: 150px; max-height: 150px; object-fit: contain; border: 1px solid #ccc;">
                <input type="hidden" name="existing_image_path" value="<?php echo htmlspecialchars($product_image); ?>">
            </div>
        <?php else: ?>
             <p class="text-warning mt-2"><?php echo t('ADMIN_PRODUCT_FORM_IMAGE_NOT_FOUND', htmlspecialchars($product_image), htmlspecialchars($imageDisplayPath)); ?></p>
             <input type="hidden" name="existing_image_path" value="">
        <?php endif; endif; ?>
    </div>

    <!-- Submit and Cancel Buttons -->
    <button type="submit" class="btn btn-primary"><?php echo $button_label; ?></button>
    <a href="products.php" class="btn btn-secondary"><?php echo t('ADMIN_PRODUCT_FORM_BTN_CANCEL'); ?></a>
</form>

<?php
// Include the footer which also closes the database connection
require_once 'partials/footer.php';
?>
