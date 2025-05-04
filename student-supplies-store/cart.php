<?php require_once 'config.php'; ?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('CART_TITLE') . ' - ' . STORE_BRAND_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .cart-item-img { width: 50px; height: 50px; object-fit: contain; margin-right: 15px; }
        .quantity-input { width: 70px; }
        .lang-switcher a { text-decoration: none; }
        .lang-switcher a.active img { border: 1px solid #555; }
        .lang-switcher img { height: 16px; width: auto; vertical-align: middle; margin-left: 3px; margin-right: 3px; transition: transform 0.1s ease-in-out; }
        .lang-switcher a:hover img { transform: scale(1.1); }
        .navbar-brand img.logo { height: 40px; width: auto; vertical-align: middle; }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                 <img src="images/logo.png" alt="<?php echo STORE_BRAND_NAME; ?> Logo" class="logo me-2">
                <?php echo STORE_BRAND_NAME; ?>
            </a>
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

     <!-- Main Content Area -->
     <div class="container mt-5">
        <h1 class="mb-4"><?php echo t('CART_TITLE'); ?></h1>

        <div id="cart-items-container">
            <!-- Cart items will be loaded here by cart.js -->
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status"> <span class="visually-hidden"><?php echo t('CART_LOADING'); ?></span> </div>
                <p class="mt-2"><?php echo t('CART_LOADING'); ?></p>
            </div>
        </div>

        <!-- Cart Summary -->
        <div id="cart-summary" class="mt-4 d-none">
            <div class="card shadow-sm"> <div class="card-body text-end">
                <h4><?php echo t('CART_SUMMARY_TOTAL'); ?> €<span id="cart-total-price">0.00</span></h4>
                <a href="index.php" class="btn btn-outline-secondary mt-2"><?php echo t('BTN_CONTINUE_SHOPPING'); ?></a>
                <a href="checkout.php" id="checkout-button" class="btn btn-success mt-2"><?php echo t('BTN_PROCEED_TO_CHECKOUT'); ?></a>
            </div> </div>
        </div>

        <!-- Empty Cart Message -->
        <div id="empty-cart-message" class="alert alert-info mt-4 d-none" role="alert">
             <?php echo t('CART_EMPTY_MESSAGE'); ?> <a href="index.php" class="alert-link"><?php echo t('CART_EMPTY_START_SHOPPING'); ?></a>
        </div>
     </div>

     <!-- Footer -->
     <footer class="text-center mt-5 mb-4"> <p class="text-muted"><?php echo t('FOOTER_COPYRIGHT', date("Y")); ?></p> </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Pass translations to JavaScript -->
    <script>
        const globalLangStrings = <?php echo json_encode([
            'cartTableProduct' => t('CART_TABLE_PRODUCT'),
            'cartTableQuantity' => t('CART_TABLE_QUANTITY'),
            'cartTableUnitPrice' => t('CART_TABLE_UNIT_PRICE'),
            'cartTableTotal' => t('CART_TABLE_TOTAL'),
            'cartTableRemove' => t('CART_TABLE_REMOVE'),
            'quantityLabel' => t('CART_TABLE_QUANTITY'),
            'removeLabel' => t('CART_TABLE_REMOVE'),
            'addedToCart' => t('ALERT_ADDED_TO_CART', '%s'),
            'invalidQuantity' => t('JS_ALERT_INVALID_QUANTITY') ?? 'Invalid quantity entered. Please enter a number greater than or equal to 0.',
            'errorSavingCart' => t('JS_ALERT_ERROR_SAVING_CART') ?? 'Could not save cart. Storage might be full.',
            'errorInvalidPrice' => t('JS_ALERT_ERROR_INVALID_PRICE') ?? 'Cannot add item: Invalid product price.',
            'errorMissingData' => t('JS_ALERT_ERROR_MISSING_DATA') ?? 'Cannot add item: Missing product data.',
            'errorButtonData' => t('JS_ALERT_ERROR_BUTTON_DATA') ?? 'Could not add item: Button data is missing.'
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <script src="js/cart.js"></script>
</body>
</html>