<?php
session_start(); // Start or resume the session

// Log out the admin by removing their login-related session data
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']); // This is optional, in case you saved the username

// Optional: destroy the entire session if it’s only used for admin stuff
// session_destroy(); // Be careful—only use this if you're sure the session isn't used elsewhere

// Send the user back to the login page with a "logged out" message
header('Location: login.php?status=logged_out');
exit;
?>
