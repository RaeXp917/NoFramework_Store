<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/language.php';

define('DB_SERVER', 'db');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'student_store');
define('STORE_BRAND_NAME', 'Pen & Panic');

function set_flash_message($message, $type = 'success') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['message' => $message, 'type' => $type];
}

function display_flash_messages() {
    if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {
        echo '<div class="flash-messages-container">';
        foreach ($_SESSION['flash_messages'] as $flash) {
            $alert_type = htmlspecialchars($flash['type']);
            switch ($alert_type) {
                case 'error': $alert_class = 'danger'; break;
                case 'warning': $alert_class = 'warning'; break;
                case 'info': $alert_class = 'info'; break;
                case 'success': default: $alert_class = 'success'; break;
            }
            echo '<div class="alert alert-' . $alert_class . ' alert-dismissible fade show m-3" role="alert">';
            echo htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        echo '</div>';
        unset($_SESSION['flash_messages']);
    }
}

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    error_log("FATAL: Database Connection Error in config.php - " . mysqli_connect_error());
    die("Sorry, we couldn't connect to the database. Please check configuration or try again later. Error: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>