<?php
// Admin Header – shared across all admin pages
// Assumes session and language setup is already done in config.php

// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default page title if not set
if (!isset($page_title)) {
    $page_title = t('NAV_ADMIN_PANEL');
}
?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo STORE_BRAND_NAME; ?></title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">

    <style>
        body { padding-top: 56px; }
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            width: 220px;
            background-color: #f8f9fa;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            z-index: 100;
        }
        .sidebar-sticky {
            height: calc(100vh - 56px);
            padding-top: .5rem;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .sidebar .nav-link {
            color: #333;
            padding: .5rem 1rem;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .navbar .nav-link.lang-link.active {
            font-weight: bold;
        }
        .navbar .nav-link img {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><?php echo t('NAV_ADMIN_PANEL'); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- View Store -->
                <li class="nav-item">
                    <a class="nav-link" href="../index.php" target="_blank" title="<?php echo t('NAV_VIEW_STORE'); ?>">
                        <i class="bi bi-shop"></i> <?php echo t('NAV_VIEW_STORE'); ?>
                    </a>
                </li>

                <!-- Language Switch -->
                <li class="nav-item">
                    <a class="nav-link lang-link <?php echo (CURRENT_LANG == 'en') ? 'active' : ''; ?>"
                       href="?lang=en<?php echo '&' . http_build_query(array_diff_key($_GET, ['lang' => ''])); ?>"
                       title="English">
                        <img src="../images/flag_en.png" alt="EN" height="16" class="me-1">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link lang-link <?php echo (CURRENT_LANG == 'el') ? 'active' : ''; ?>"
                       href="?lang=el<?php echo '&' . http_build_query(array_diff_key($_GET, ['lang' => ''])); ?>"
                       title="Ελληνικά">
                        <img src="../images/flag_el.png" alt="EL" height="16" class="me-1">
                    </a>
                </li>

                <!-- Admin Greeting & Logout -->
                <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            <?php echo t('WELCOME_ADMIN', htmlspecialchars($_SESSION['admin_username'] ?? 'Admin')); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="logout.php"><?php echo t('BTN_LOGOUT'); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Layout Grid -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-door"></i> <?php echo t('ADMIN_DASHBOARD_TITLE'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'product_form.php']) ? 'active' : ''; ?>" href="products.php">
                            <i class="bi bi-box-seam"></i> <?php echo t('ADMIN_MANAGE_PRODUCTS_TITLE'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['orders.php', 'order_details.php']) ? 'active' : ''; ?>" href="orders.php">
                            <i class="bi bi-file-earmark-text"></i> <?php echo t('ADMIN_VIEW_ORDERS_TITLE'); ?>
                        </a>
                    </li>
                    <!-- You can add more links here -->
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <?php
            // Show flash messages if available
            if (function_exists('display_flash_messages')) {
                display_flash_messages();
            }
            ?>
            <!-- The page-specific content comes below -->
