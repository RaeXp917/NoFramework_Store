<?php require_once 'config.php'; ?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('CHECKOUT_TITLE') . ' - ' . STORE_BRAND_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
     <style>
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
        <div class="row">
            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <h1 class="mb-4 text-center"><?php echo t('CHECKOUT_TITLE'); ?></h1>

                 <?php
                    // Display validation errors if redirected back
                    if (isset($_SESSION['checkout_errors']) && !empty($_SESSION['checkout_errors'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        echo '<h4 class="alert-heading">'.t('CHECKOUT_VALIDATION_ERROR').'</h4>';
                        echo '<ul>';
                        foreach ($_SESSION['checkout_errors'] as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        unset($_SESSION['checkout_errors']);
                    }
                    $form_data = $_SESSION['checkout_data'] ?? [];
                    unset($_SESSION['checkout_data']);
                ?>


                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo t('CHECKOUT_SUMMARY_TITLE'); ?></h5>
                        <p class="card-text"><?php echo t('CHECKOUT_SUMMARY_TEXT'); ?></p>
                        <p class="fs-4 fw-bold text-end"><?php echo t('CART_SUMMARY_TOTAL'); ?> €<span id="checkout-total-price">0.00</span></p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><?php echo t('CHECKOUT_DETAILS_TITLE'); ?></h5>
                        <form id="checkout-form" action="place_order.php" method="POST">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label"><?php echo t('CHECKOUT_NAME_LABEL'); ?></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($form_data['customer_name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_email" class="form-label"><?php echo t('CHECKOUT_EMAIL_LABEL'); ?></label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($form_data['customer_email'] ?? ''); ?>" required>
                            </div>
                            <input type="hidden" id="cart_data" name="cart_data" value="">
                            <input type="hidden" id="total_price" name="total_price" value="">
                            <div class="d-grid">
                                <button type="submit" id="place-order-button" class="btn btn-success btn-lg"><?php echo t('BTN_PLACE_ORDER'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>

                 <div class="text-center mt-3">
                     <a href="cart.php"><?php echo t('BTN_BACK_TO_CART'); ?></a>
                 </div>
            </div>
        </div>
     </div>

     <!-- Footer -->
     <footer class="text-center mt-5 mb-4"> <p class="text-muted"><?php echo t('FOOTER_COPYRIGHT', date("Y")); ?></p> </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        const checkoutLangStrings = <?php echo json_encode([
            'checkoutCartEmpty' => t('ALERT_CART_EMPTY_CHECKOUT'),
            'placeOrderEmpty' => t('BTN_PLACE_ORDER_EMPTY')
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            updateCartIcon();

            const cart = getCart();
            const productIds = Object.keys(cart);
            let totalPrice = 0;

            productIds.forEach(id => {
                const item = cart[id];
                if (item && typeof item.price === 'number' && typeof item.quantity === 'number') {
                    totalPrice += item.price * item.quantity;
                }
            });

            const totalPriceElement = document.getElementById('checkout-total-price');
            if (totalPriceElement) {
                totalPriceElement.textContent = totalPrice.toFixed(2);
            }

            const cartDataInput = document.getElementById('cart_data');
            const totalPriceInput = document.getElementById('total_price');
            const placeOrderButton = document.getElementById('place-order-button');

            if (productIds.length === 0) {
                if (placeOrderButton) {
                    placeOrderButton.classList.add('disabled');
                    placeOrderButton.textContent = (typeof checkoutLangStrings !== 'undefined' && checkoutLangStrings.placeOrderEmpty) ? checkoutLangStrings.placeOrderEmpty : 'Your Cart is Empty';
                    placeOrderButton.setAttribute('aria-disabled', 'true');
                }
                 const alertMsg = (typeof checkoutLangStrings !== 'undefined' && checkoutLangStrings.checkoutCartEmpty) ? checkoutLangStrings.checkoutCartEmpty : null;
                 if (alertMsg) {
                    // Consider displaying this message more prominently if needed
                    // alert(alertMsg); // Avoid alert if possible
                 }

            } else {
                 if (cartDataInput && totalPriceInput && placeOrderButton) {
                    const cartForSubmit = {};
                    productIds.forEach(id => { cartForSubmit[id] = cart[id].quantity; });
                    cartDataInput.value = JSON.stringify(cartForSubmit);
                    totalPriceInput.value = totalPrice.toFixed(2);
                 } else {
                     console.error("Checkout form hidden fields or button not found!");
                     if(placeOrderButton) placeOrderButton.classList.add('disabled');
                 }
            }
        });
    </script>
</body>
</html>