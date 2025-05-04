<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $cart_data_json = $_POST['cart_data'] ?? '';
    $total_price_posted = filter_var($_POST['total_price'] ?? 0, FILTER_VALIDATE_FLOAT);

    $errors = [];

    // --- Validation ---
    if (empty($customer_name)) {
        $errors[] = t('CHECKOUT_VALIDATE_NAME_REQUIRED');
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('CHECKOUT_VALIDATE_EMAIL_INVALID');
    }
    if (empty($cart_data_json)) {
        $errors[] = t('CHECKOUT_VALIDATE_CART_MISSING');
    }
    if ($total_price_posted === false || $total_price_posted <= 0) {
        $errors[] = t('CHECKOUT_VALIDATE_PRICE_INVALID');
    }

    $cart_items = json_decode($cart_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart_items) || empty($cart_items)) {
        $errors[] = t('CHECKOUT_VALIDATE_CART_INVALID');
    }

    if (!empty($errors)) {
        $_SESSION['checkout_errors'] = $errors;
        $_SESSION['checkout_data'] = $_POST;
        set_flash_message(t('CHECKOUT_VALIDATION_ERROR'), 'warning');
        header("Location: checkout.php");
        exit;
    }

    if (!$conn) {
        error_log("DB connection failed.");
        set_flash_message(t('DB_CONNECTION_ERROR'), 'error');
        header("Location: checkout.php");
        exit;
    }

    mysqli_begin_transaction($conn);
    try {
        // --- Insert Order ---
        $stmt_order = mysqli_prepare($conn, "INSERT INTO orders (customer_name, customer_email, total_price, created_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt_order) {
            throw new Exception("Prepare failed for order insert: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt_order, "ssd", $customer_name, $customer_email, $total_price_posted);
        if (!mysqli_stmt_execute($stmt_order)) {
            throw new Exception("Order insert failed: " . mysqli_stmt_error($stmt_order));
        }
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_order);

        // --- Insert Items ---
        $stmt_items = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        if (!$stmt_items) {
            throw new Exception("Prepare failed for items insert: " . mysqli_error($conn));
        }

        foreach ($cart_items as $product_id => $quantity) {
            $product_id_int = filter_var($product_id, FILTER_VALIDATE_INT);
            $quantity_int = filter_var($quantity, FILTER_VALIDATE_INT);

            if ($product_id_int === false || $quantity_int === false || $quantity_int <= 0) {
                error_log("Invalid cart item: ID=" . htmlspecialchars($product_id) . ", Qty=" . htmlspecialchars($quantity));
                throw new Exception("Invalid product or quantity in cart.");
            }

            mysqli_stmt_bind_param($stmt_items, "iii", $order_id, $product_id_int, $quantity_int);
            if (!mysqli_stmt_execute($stmt_items)) {
                throw new Exception("Failed to insert item ID $product_id_int: " . mysqli_stmt_error($stmt_items));
            }
        }
        mysqli_stmt_close($stmt_items);

        mysqli_commit($conn);

        set_flash_message(t('ORDER_PLACED_SUCCESS'), 'success');
        $_SESSION['order_details'] = [
            'order_id' => $order_id,
            'customer_email' => $customer_email
        ];

        header("Location: order_success.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Order failed: " . $e->getMessage());
        set_flash_message(t('ORDER_PLACED_ERROR'), 'error');
        $_SESSION['checkout_errors'] = [$e->getMessage()];
        $_SESSION['checkout_data'] = $_POST;
        header("Location: checkout.php");
        exit;
    } finally {
        mysqli_close($conn);
    }

} else {
    header("Location: index.php");
    exit;
}
