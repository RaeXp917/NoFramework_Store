<?php
// language.php - Handles language selection and loading

// Start session if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Supported languages and default
$available_langs = ['en', 'el'];
$default_lang = 'en';
$current_lang = $default_lang;

// 1. Check if 'lang' parameter is in the URL
if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $current_lang = $_GET['lang'];
    $_SESSION['lang'] = $current_lang;

    // Optional: Persist language with cookie
    // setcookie('lang', $current_lang, time() + (30 * 24 * 3600), "/"); // 30 days
}
// 2. Fallback to session if no URL param
elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available_langs)) {
    $current_lang = $_SESSION['lang'];
}
// 3. Fallback to cookie if no session (optional - uncomment if needed)
// elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $available_langs)) {
//     $current_lang = $_COOKIE['lang'];
//     $_SESSION['lang'] = $current_lang; // Sync with session
// }

// Define constant for use across your app
define('CURRENT_LANG', $current_lang);

// Load the appropriate language file
$lang_file = __DIR__ . '/lang/' . CURRENT_LANG . '.php';

// If it exists, load it; else fallback to default
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    $fallback_file = __DIR__ . '/lang/' . $default_lang . '.php';
    if (file_exists($fallback_file)) {
        require_once $fallback_file;
    } else {
        die("Error: No language files found.");
    }
}

// Translation helper
function t(string $key, ...$args): string {
    global $lang;

    if (isset($lang[$key])) {
        return vsprintf($lang[$key], $args);
    } else {
        error_log("Missing translation key: $key in " . CURRENT_LANG);
        return $key; // or return "[$key]"
    }
}
