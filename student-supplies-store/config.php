<?php
// config.php

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start a new session
}

// Include the language configuration file (make sure it's in the same directory as this file)
require_once __DIR__ . '/language.php'; // Using __DIR__ ensures the path is correct, regardless of where the file is included from

// --- Database Credentials ---
define('DB_SERVER', 'localhost'); // The server where the database is hosted
define('DB_USERNAME', 'root');    // The database username (default for XAMPP, change if needed)
define('DB_PASSWORD', '');        // The database password (default for XAMPP is empty, change if needed)
define('DB_NAME', 'student_store'); // Name of your database (make sure this matches your actual DB name)
define('STORE_BRAND_NAME', 'Pen & Panic'); // The name of your store

// --- Function to Set Flash Messages ---
function set_flash_message($message, $type = 'success') {
    // Create an array to hold flash messages if it doesn't exist yet
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    // Add the new message to the session
    $_SESSION['flash_messages'][] = ['message' => $message, 'type' => $type];
}

// --- Function to Display Flash Messages ---
function display_flash_messages() {
    // Check if there are any flash messages stored in the session
    if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {
        // Loop through each message and display it
        echo '<div class="flash-messages-container">'; // Optional container for better styling
        foreach ($_SESSION['flash_messages'] as $flash) {
            $alert_type = htmlspecialchars($flash['type']); // Get the message type (e.g., success, error)
            // Choose the appropriate Bootstrap class based on the message type
            switch ($alert_type) {
                case 'error':
                    $alert_class = 'danger'; // For error messages, use the "danger" alert class
                    break;
                case 'warning':
                    $alert_class = 'warning'; // For warnings, use the "warning" alert class
                    break;
                case 'info':
                    $alert_class = 'info'; // For informational messages, use the "info" class
                    break;
                case 'success':
                default:
                    $alert_class = 'success'; // Default is success messages with the "success" class
                    break;
            }
            // Display the message using Bootstrap's alert structure
            echo '<div class="alert alert-' . $alert_class . ' alert-dismissible fade show m-3" role="alert">';
            echo htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        echo '</div>'; // Close the optional container
        // After displaying the messages, remove them from the session
        unset($_SESSION['flash_messages']);
    }
}

// --- Establish Database Connection ---
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if($conn === false){
    // Log the connection error for debugging
    error_log("FATAL: Database Connection Error in config.php - " . mysqli_connect_error());
    // Show a user-friendly message and stop the script if the connection fails
    // You might want to redirect to a custom error page in a real application
    die("Sorry, we couldn't connect to the database. Please try again later.");
}

// Set the character set to support all types of characters (including emojis, etc.)
mysqli_set_charset($conn, "utf8mb4");

// The $conn variable is now ready to be used in any script that includes this config.php

?>
