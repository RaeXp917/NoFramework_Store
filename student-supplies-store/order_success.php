<?php
require_once 'config.php';

$order_id = $_SESSION['order_details']['order_id'] ?? 'N/A';
$customer_email = $_SESSION['order_details']['customer_email'] ?? '';

// Optional: Clear the temporary order details from session
// unset($_SESSION['order_details']);
?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('ORDER_SUCCESS_TITLE') . ' - ' . STORE_BRAND_NAME; ?></title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        .lang-switcher a { text-decoration: none; }
        .lang-switcher a.active img { border: 1px solid #555; }
        .lang-switcher img {
            height: 16px;
            margin: 0 3px;
            vertical-align: middle;
            transition: transform 0.1s ease-in-out;
        }
        .lang-switcher a:hover img { transform: scale(1.1); }
        .navbar-brand img.logo {
            height: 40px;
            vertical-align: middle;
        }
    </style>

    <script>
        // Clear cart from localStorage after order
        if (typeof localStorage !== 'undefined') {
            localStorage.removeItem('shoppingCart');
            console.log('Shopping cart cleared from localStorage.');
            document.addEventListener('DOMContentLoaded', () => {
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) cartCountElement.textContent = '0';
            });
        }
    </script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="<?php echo STORE_BRAND_NAME; ?> Logo" class="logo me-2">
                <?php echo STORE_BRAND_NAME; ?>
            </a>

            <!-- Language Switcher + Cart Button -->
            <div class="ms-auto d-flex align-items-center lang-switcher">
                <?php
                    $lang_params_en = ['lang' => 'en'] + array_diff_key($_GET, ['lang'=>'', 'id'=>'', 'page'=>'']);
                    $lang_params_el = ['lang' => 'el'] + array_diff_key($_GET, ['lang'=>'', 'id'=>'', 'page'=>'']);
                ?>
                <a href="?<?php echo http_build_query($lang_params_en); ?>" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'en' ? 'active' : ''); ?>" title="English">
                    <img src="images/flag_en.png" alt="EN Flag">
                </a>
                <a href="?<?php echo http_build_query($lang_params_el); ?>" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'el' ? 'active' : ''); ?>" title="Ελληνικά">
                    <img src="images/flag_el.png" alt="EL Flag">
                </a>

                <a href="cart.php" class="btn btn-outline-success position-relative ms-3">
                    <i class="bi bi-cart"></i>
                    <?php echo t('NAV_CART'); ?>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php
            if (function_exists('display_flash_messages')) {
                display_flash_messages();
            }
        ?>

        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <div class="alert alert-success p-5 shadow-sm" role="alert">
                    <h4 class="alert-heading"><?php echo t('ORDER_SUCCESS_HEADING'); ?></h4>
                    <p><?php echo t('ORDER_SUCCESS_MESSAGE'); ?></p>
                    <p><?php echo t('ORDER_SUCCESS_ID_LABEL'); ?> <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
                    <hr>

                    <!-- Human-friendly order confirmation message -->
                    <p class="mb-0">
                        <?php
                            echo t('ORDER_SUCCESS_NOTICE');
                            if (!empty($customer_email)) {
                                echo ' ' . t('ORDER_SUCCESS_EMAIL_SENT_TO', htmlspecialchars($customer_email));
                            }
                        ?>
                    </p>
                </div>

                <!-- Button to return to shopping -->
                <a href="index.php" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> <?php echo t('BTN_CONTINUE_SHOPPING'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 mb-4">
        <p class="text-muted"><?php echo t('FOOTER_COPYRIGHT', date("Y")); ?></p>
    </footer>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>