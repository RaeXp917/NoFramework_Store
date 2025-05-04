<?php
session_start(); // Start a session so we can track if the admin is logged in

// If the admin isn't logged in, send them to the login page with an error
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit;
}

// Include the main config file (for DB connection, settings, etc.)
require_once '../config.php';
// You can include other necessary files below as needed
?>
<!-- HTML or other PHP layout code for the admin panel -->

<?php
require_once '../config.php'; // Load config again to make sure everything is set up (e.g. translations)
$page_title = t('ADMIN_VIEW_ORDERS_TITLE');
require_once 'partials/header.php'; // Load the common header for admin pages

// Fetch all orders from the database, newest ones first
$sql = "SELECT id, customer_name, customer_email, total_price, created_at FROM orders ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// If the query fails, log the error and stop the script
if (!$result) {
    error_log("Database query failed in admin/orders.php: " . mysqli_error($conn));
    die("Error fetching orders."); // You can use t() for translations here if you want
}
?>

<!-- Page header with title -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo t('ADMIN_ORDERS_LIST_TITLE'); ?></h1>
</div>

<!-- Orders table -->
<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col"><?php echo t('ADMIN_ORDERS_TABLE_ID'); ?></th>
                <th scope="col"><?php echo t('ADMIN_ORDERS_TABLE_CUST_NAME'); ?></th>
                <th scope="col"><?php echo t('ADMIN_ORDERS_TABLE_CUST_EMAIL'); ?></th>
                <th scope="col" class="text-end"><?php echo t('ADMIN_ORDERS_TABLE_TOTAL'); ?></th>
                <th scope="col"><?php echo t('ADMIN_ORDERS_TABLE_DATE'); ?></th>
                <th scope="col" class="text-center"><?php echo t('ADMIN_ORDERS_TABLE_ACTIONS'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            // If there are any orders, loop through and show them in the table
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $order_date = date("Y-m-d H:i:s", strtotime($row['created_at']));
            ?>
                    <tr>
                        <th scope="row"><?php echo $row['id']; ?></th>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                        <td class="text-end">€<?php echo number_format($row['total_price'], 2); ?></td> <!-- Show price in Euros -->
                        <td><?php echo $order_date; ?></td>
                        <td class="text-center">
                            <!-- Link to view more details about this order -->
                            <a href="order_details.php?order_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm" title="<?php echo t('ADMIN_ORDERS_BTN_VIEW'); ?>">
                                <i class="bi bi-eye-fill"></i> <?php echo t('ADMIN_ORDERS_BTN_VIEW'); ?>
                            </a>
                        </td>
                    </tr>
            <?php 
                } 
            } else { ?>
                <!-- If there are no orders, show a message -->
                <tr><td colspan="6" class="text-center"><?php echo t('ADMIN_ORDERS_NONE_FOUND'); ?></td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
require_once 'partials/footer.php'; // Load the common footer
// DB connection is closed inside footer.php
?>
