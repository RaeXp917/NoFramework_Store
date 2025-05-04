<?php
session_start(); // Start or resume the session — needed to track login status

// If admin is already logged in, skip login form and go straight to the dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php'); // You can change this to your admin panel file
    exit;
}

// Set up message placeholder
$error_message = '';

// Show message based on query parameters
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid_credentials') {
        $error_message = 'Invalid username or password.';
    } elseif ($_GET['error'] === 'login_required') {
        $error_message = 'Please log in to access the admin area.';
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'logged_out') {
    $error_message = 'You have been logged out successfully.';
}

// --- NOTE: Hardcoded credentials below are for testing only ---
// In real apps, use hashed passwords stored in a database or config file
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'password123'); // ⚠️ Change this even for test use

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values submitted from the form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check credentials
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Successful login — store session info
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username; // Optional: useful for personalization
        header('Location: index.php'); // Go to admin dashboard
        exit;
    } else {
        // Wrong username or password — redirect with error
        header('Location: login.php?error=invalid_credentials');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Student Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet"> <!-- Adjust the path if needed -->

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-form {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2 class="text-center mb-4">Admin Login</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-<?php echo isset($_GET['error']) ? 'danger' : 'info'; ?> mb-3">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    required
                    autofocus
                >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required
                >
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>

        <p class="mt-3 text-center">
            <a href="../index.php">← Back to Store</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
