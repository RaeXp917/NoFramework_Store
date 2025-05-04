<?php
session_start(); // Start user session

// Make sure the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit;
}

require_once '../config.php'; // DB connection and translations

// --- Validate Product ID ---
$product_id = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];
} else {
    set_flash_message(t('ADMIN_INVALID_PRODUCT_ID'), 'error');
    header('Location: products.php');
    exit();
}

// --- Fetch Image Path to Delete ---
$image_path_to_delete = null;

$sql_get_image = "SELECT image FROM products WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql_get_image)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['image'])) {
            $image_path_to_delete = '../' . $row['image'];
        }
    }

    mysqli_stmt_close($stmt);
}

// --- Delete Product Record from DB ---
$sql_delete = "DELETE FROM products WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql_delete)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            set_flash_message(t('ADMIN_PRODUCT_DELETED_SUCCESS'), 'success');

            // --- Delete the image file if it exists ---
            if ($image_path_to_delete && file_exists($image_path_to_delete)) {
                if (!unlink($image_path_to_delete)) {
                    error_log("Failed to delete image: $image_path_to_delete");
                    set_flash_message(t('ADMIN_IMAGE_DELETE_ERROR', $image_path_to_delete), 'warning');
                }
            }
        } else {
            set_flash_message(t('ADMIN_PRODUCT_NOT_FOUND_DELETE'), 'warning');
        }
    } else {
        error_log("Failed to delete product #$product_id: " . mysqli_stmt_error($stmt));
        set_flash_message(t('ADMIN_PRODUCT_DELETED_ERROR'), 'error');
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("Delete statement failed for product #$product_id: " . mysqli_error($conn));
    set_flash_message(t('ADMIN_PRODUCT_DELETED_ERROR'), 'error');
}

// Close the database connection
mysqli_close($conn);

// Redirect to product list
header("Location: products.php");
exit;
?>
