<?php
session_start(); // Begin the session so we can track if the admin is logged in

// If the admin isn't logged in, send them to the login page with an error
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit;
}

// Load essential config and language files
require_once '../config.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    // If no valid order ID is passed in the URL, go back to the orders list
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

// Set the page title dynamically using the order ID
$page_title = t('ADMIN_ORDER_DETAILS_TITLE', $order_id);

// --- Step 1: Get order info from the database ---
$order_info = null;
$sql_order = "SELECT customer_name, customer_email, total_price, created_at FROM orders WHERE id = ?";
if ($stmt_order = mysqli_prepare($conn, $sql_order)) {
    mysqli_stmt_bind_param($stmt_order, "i", $order_id);
    if (mysqli_stmt_execute($stmt_order)) {
        $result_order = mysqli_stmt_get_result($stmt_order);
        $order_info = mysqli_fetch_assoc($result_order);

        if (!$order_info) {
            // If no order was found, stop and show an error
            if (isset($conn)) mysqli_close($conn);
            die("Error: Order with ID {$order_id} not found.");
        }
    } else {
        // Something went wrong while trying to run the query
        if (isset($conn)) mysqli_close($conn);
        die("Error executing order query: " . mysqli_stmt_error($stmt_order));
    }
    mysqli_stmt_close($stmt_order);
} else {
    // Could not even prepare the SQL statement
    if (isset($conn)) mysqli_close($conn);
    die("Error preparing order query: " . mysqli_error($conn));
}

// --- Step 2: Get the items included in this order ---
$order_items = [];
$item_fetch_error = null;

$sql_items = "SELECT oi.quantity, p.id as product_id, p.name as product_name, p.price as unit_price, p.image as product_image
              FROM order_items oi JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ? ORDER BY p.name ASC";

if ($stmt_items = mysqli_prepare($conn, $sql_items)) {
    mysqli_stmt_bind_param($stmt_items, "i", $order_id);
    if (mysqli_stmt_execute($stmt_items)) {
        $result_items = mysqli_stmt_get_result($stmt_items);
        while ($item = mysqli_fetch_assoc($result_items)) {
            $order_items[] = $item;
        }
    } else {
        $item_fetch_error = t('ADMIN_ORDER_DETAILS_ITEM_ERROR');
        error_log("Error fetching order items: " . mysqli_stmt_error($stmt_items));
    }
    mysqli_stmt_close($stmt_items);
} else {
    $item_fetch_error = t('ADMIN_ORDER_DETAILS_ITEM_ERROR');
    error_log("Error preparing items query: " . mysqli_error($conn));
}

// Load the page header
require_once 'partials/header.php';
?>

<!-- Page Title and Back Button -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $page_title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="orders.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> <?php echo t('ADMIN_BTN_BACK_TO_LIST'); ?>
        </a>
    </div>
</div>

<!-- Show summary of the order (customer info, date, total, etc.) -->
<div class="card mb-4 shadow-sm">
    <div class="card-header"><?php echo t('ADMIN_ORDER_DETAILS_SUMMARY'); ?></div>
    <div class="card-body">
        <p><strong><?php echo t('ADMIN_ORDER_DETAILS_ID'); ?></strong> <?php echo $order_id; ?></p>
        <p><strong><?php echo t('ADMIN_ORDER_DETAILS_CUST_NAME'); ?></strong> <?php echo htmlspecialchars($order_info['customer_name']); ?></p>
        <p><strong><?php echo t('ADMIN_ORDER_DETAILS_CUST_EMAIL'); ?></strong> <?php echo htmlspecialchars($order_info['customer_email']); ?></p>
        <p><strong><?php echo t('ADMIN_ORDER_DETAILS_TOTAL'); ?></strong> €<?php echo number_format($order_info['total_price'], 2); ?></p>
        <p><strong><?php echo t('ADMIN_ORDER_DETAILS_DATE'); ?></strong> <?php echo date("Y-m-d H:i:s", strtotime($order_info['created_at'])); ?></p>
    </div>
</div>

<!-- Display list of products included in this order -->
<h3 class="h3 mb-3"><?php echo t('ADMIN_ORDER_DETAILS_ITEMS_HEADING'); ?></h3>

<?php if ($item_fetch_error): ?>
    <div class="alert alert-danger"><?php echo $item_fetch_error; ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_PROD_ID'); ?></th>
                <th scope="col"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_IMAGE'); ?></th>
                <th scope="col"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_PROD_NAME'); ?></th>
                <th scope="col" class="text-center"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_QUANTITY'); ?></th>
                <th scope="col" class="text-end"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_UNIT_PRICE'); ?></th>
                <th scope="col" class="text-end"><?php echo t('ADMIN_ORDER_DETAILS_TABLE_SUBTOTAL'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($order_items)) {
                foreach ($order_items as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    
                    // If the product has a valid image, use it; otherwise show a placeholder
                    $imagePath = '../images/placeholder.png';
                    $dbImagePath = '../' . $item["product_image"];
                    if (!empty($item["product_image"]) && file_exists($dbImagePath)) {
                        $imagePath = $dbImagePath;
                    }
            ?>
                    <tr>
                        <th scope="row"><?php echo $item['product_id']; ?></th>
                        <td><img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 40px; height: 40px; object-fit: contain;"></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end">€<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="text-end fw-bold">€<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
            <?php 
                }
            } elseif (!$item_fetch_error) { ?>
                <!-- If there was no error but also no items, show a message -->
                <tr><td colspan="6" class="text-center"><?php echo t('ADMIN_ORDER_DETAILS_NO_ITEMS'); ?></td></tr>
            <?php } else { ?>
                <!-- If there was a database error while loading items -->
                <tr><td colspan="6" class="text-center"><?php echo t('ADMIN_ORDER_DETAILS_ITEM_ERROR'); ?></td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
// Load the footer and close the DB connection (if done in footer.php)
require_once 'partials/footer.php';
?>
