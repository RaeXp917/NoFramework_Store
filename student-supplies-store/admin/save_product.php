<?php
session_start(); // Start the session to track user login status

// Check if the admin is logged in, if not, redirect to the login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit; // Exit to prevent further execution if not logged in
}

// --- Admin page code continues here ---
require_once '../config.php'; // Include configuration file to access database and other settings
// Other necessary includes can go here...
?>
<!-- HTML or other PHP code for the admin page -->

<?php
// Include the config file for database and helper functions
require_once '../config.php';

// Function to handle image uploads
function handleImageUpload($fileInputName, $uploadDir = '../images/') {
   // Check if the file is uploaded without errors
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fileInputName]['tmp_name']; // Temporary file location
        $fileName = basename($_FILES[$fileInputName]['name']); // Get the file name and sanitize it
        $fileSize = $_FILES[$fileInputName]['size']; // Get the file size
        $fileType = $_FILES[$fileInputName]['type']; // Get the file type
        $fileNameCmps = explode(".", $fileName); // Split the file name by extension
        $fileExtension = strtolower(end($fileNameCmps)); // Get the file extension in lowercase

        // Sanitize the file name to remove unwanted characters and spaces
        $newFileName = preg_replace('/[^A-Za-z0-9.\-_]/', '', str_replace(' ', '_', $fileNameCmps[0]));
        $newFileName = $newFileName . '_' . time() . '.' . $fileExtension; // Make the name unique by adding a timestamp

        // Define allowed file extensions
        $allowedfileExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp'];
        if (in_array($fileExtension, $allowedfileExtensions)) {

            // Check if the file size is within the limit (5MB max)
            $maxFileSize = 5 * 1024 * 1024;
            if ($fileSize > $maxFileSize) {
                return ['error' => t('ADMIN_VALIDATE_IMAGE_SIZE')]; // Translate error message if file is too large
            }

            // Check the file's MIME type (more reliable than just checking the extension)
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileMimeType = mime_content_type($fileTmpPath);
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                 return ['error' => t('ADMIN_VALIDATE_IMAGE_TYPE')]; // Translate error message if the MIME type is invalid
             }

            // Path where the image will be saved
            $dest_path = $uploadDir . $newFileName;

            // Ensure the upload directory exists, create it if it doesn't
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                     return ['error' => t('ADMIN_VALIDATE_IMAGE_DIR_ERROR')]; // Error message if directory creation fails
                }
            }

            // Move the uploaded file from temporary location to the destination
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Return the relative path for saving to the database (without the '../')
                return ['success' => str_replace('../', '', $dest_path)];
            } else {
                return ['error' => t('ADMIN_VALIDATE_IMAGE_MOVE_ERROR')]; // Error message if moving the file fails
            }
        } else {
            return ['error' => t('ADMIN_VALIDATE_IMAGE_EXT_ERROR', implode(', ', $allowedfileExtensions))]; // Error if the file extension is not allowed
        }
    } elseif (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle any other upload errors
        return ['error' => t('ADMIN_VALIDATE_IMAGE_UPLOAD_ERROR_CODE', $_FILES[$fileInputName]['error'])]; // Translate error code
    }
    // If no file was uploaded or an error occurred before this point
    return ['no_upload' => true];
}
// --- End of image upload function ---


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Gather and sanitize form data ---
    $product_id = isset($_POST['product_id']) ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : null;
    $product_name = trim($_POST['product_name'] ?? '');
    $product_description = trim($_POST['product_description'] ?? '');
    $product_price = isset($_POST['product_price']) ? filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT) : null;
    $product_category = trim($_POST['product_category'] ?? '');
    $existing_image_path = $_POST['existing_image_path'] ?? '';
    $is_edit = !empty($product_id); // Check if we're editing an existing product

    // --- Form validation ---
    $errors = [];
    if (empty($product_name)) {
        $errors[] = t('ADMIN_VALIDATE_NAME_REQUIRED'); // Translate error if the product name is missing
    }
    if ($product_price === null || $product_price < 0) {
        $errors[] = t('ADMIN_VALIDATE_PRICE_INVALID'); // Translate error if the price is invalid
    }
    // Add other validation checks here...

    // --- Handle image upload ---
    $image_result = handleImageUpload('product_image_file'); // Process the uploaded image
    $image_path_to_save = null;
    $old_image_to_delete = null; // Keep track of old image if it's being replaced

    if (isset($image_result['success'])) {
        $image_path_to_save = $image_result['success'];
        if ($is_edit && !empty($existing_image_path)) {
            $old_image_to_delete = '../' . $existing_image_path; // Set the old image for deletion after the update
        }
    } elseif (isset($image_result['error'])) {
        $errors[] = $image_result['error']; // Add the error message if there's an issue with the image upload
    } elseif ($is_edit && !empty($existing_image_path)) {
        $image_path_to_save = $existing_image_path; // Keep the existing image if no new image is uploaded
    } else {
        $image_path_to_save = ''; // No image uploaded or set to empty if not editing
    }

    // --- Perform database operation ---
    if (empty($errors)) {
        if ($is_edit) {
            // --- UPDATE product ---
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdssi", $product_name, $product_description, $product_price, $product_category, $image_path_to_save, $product_id);
            $success_message = t('ADMIN_PRODUCT_UPDATED_SUCCESS'); // Success message after updating
            $error_message = t('ADMIN_PRODUCT_UPDATED_ERROR'); // Error message if update fails
        } else {
            // --- INSERT new product ---
            $sql = "INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdss", $product_name, $product_description, $product_price, $product_category, $image_path_to_save);
            $success_message = t('ADMIN_PRODUCT_ADDED_SUCCESS'); // Success message after adding
            $error_message = t('ADMIN_PRODUCT_ADDED_ERROR'); // Error message if adding fails
        }

        // If query executes successfully, display success and redirect
        if ($stmt && mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash_message($success_message, 'success'); // Set flash message for success

            // Delete old image after successful DB update
            if ($old_image_to_delete && file_exists($old_image_to_delete)) {
                 if (!unlink($old_image_to_delete)) {
                     error_log("Failed to delete old image file: " . $old_image_to_delete);
                     set_flash_message(t('ADMIN_IMAGE_DELETE_ERROR', $old_image_to_delete), 'warning'); // Warning if image deletion fails
                 }
            }

            header("Location: products.php"); // Redirect to the products page
            exit;
        } else {
            // If there was a database error, log it and show an error message
            $db_error = $stmt ? mysqli_stmt_error($stmt) : mysqli_error($conn);
            error_log("Database error saving product: " . $db_error);
            $errors[] = $error_message; // Add error message for user
            if ($stmt) mysqli_stmt_close($stmt);
        }
    }

    // --- Handle validation or DB errors ---
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors; // Store validation errors in the session
        $_SESSION['form_data'] = $_POST;    // Store form data to repopulate the form
        set_flash_message(t('ADMIN_FORM_VALIDATION_ERROR'), 'error'); // Set general error flash message

        // Redirect back to the form with errors
        $redirect_url = 'product_form.php' . ($is_edit ? '?id=' . $product_id : '');
        header('Location: ' . $redirect_url);
        exit;
    }

    // Close the database connection if it's open
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }

} else {
    // If the form wasn't submitted with POST, redirect to products page
    header("Location: products.php");
    exit;
}
?>
