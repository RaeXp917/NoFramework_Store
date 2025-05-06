<?php
// === Product Page ===
// Cleaned & commented by AI assistant on 2025-05-04
// Some comments are marked as AI-added, others are left casually to mimic human dev notes

require_once 'config.php';

// Validate product ID from query string
$product_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];
}

$product = null;
if ($product_id) {
    $sql = "SELECT id, name, description, price, image, category FROM products WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) === 1) {
                $product = mysqli_fetch_assoc($result);
            }
        } else {
            error_log("[AI] Error executing product statement: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("[AI] Error preparing product statement: " . mysqli_error($conn));
    }
}

$page_title = $product ? htmlspecialchars($product['name']) : t('PRODUCT_NOT_FOUND_TITLE');
?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title . ' - ' . STORE_BRAND_NAME; ?></title>
    
    <!-- CDN Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* === Inline page-specific styles === */
        .product-image {
            max-width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            background-color: #f8f9fa;
            border-radius: .375rem;
            border: 1px solid #dee2e6;
        }
        .lang-switcher a { text-decoration: none; }
        .lang-switcher a.active img { border: 1px solid #555; }
        .lang-switcher img {
            height: 16px;
            vertical-align: middle;
            margin: 0 3px;
            transition: transform 0.1s ease-in-out;
        }
        .lang-switcher a:hover img { transform: scale(1.1); }
        .navbar-brand img.logo {
            height: 40px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- === Navigation Bar === -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="<?php echo STORE_BRAND_NAME; ?> Logo" class="logo me-2">
                <?php echo STORE_BRAND_NAME; ?>
            </a>

            <div class="ms-auto d-flex align-items-center lang-switcher">
                <?php
                    // Language switcher links (preserve current product ID)
                    $lang_params_en = ['lang' => 'en'] + ($product_id ? ['id' => $product_id] : []);
                    $lang_params_el = ['lang' => 'el'] + ($product_id ? ['id' => $product_id] : []);
                ?>
                <a href="?<?php echo http_build_query($lang_params_en); ?>" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'en' ? 'active' : ''); ?>" title="English">
                    <img src="images/flag_en.png" alt="EN Flag">
                </a>
                <a href="?<?php echo http_build_query($lang_params_el); ?>" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'el' ? 'active' : ''); ?>" title="Ελληνικά">
                    <img src="images/flag_el.png" alt="EL Flag">
                </a>

                <!-- Cart Icon -->
                <a href="cart.php" class="btn btn-outline-success position-relative ms-3">
                    <i class="bi bi-cart"></i>
                    <?php echo t('NAV_CART'); ?>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- === Main Content === -->
    <div class="container mt-5">
        <?php if ($product): ?>
            <div class="row">
                <div class="col-md-6 mb-4 text-center">
                    <?php
                        // Image fallback & checks
                        $imagePath = 'images/placeholder.png';
                        if (!empty($product['image'])) {
                            $potentialPath = ltrim($product['image'], '/');
                            if (file_exists($potentialPath)) {
                                $imagePath = $potentialPath;
                            } elseif (file_exists('images/' . basename($potentialPath))) {
                                $imagePath = 'images/' . basename($potentialPath);
                            }
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-muted"><?php echo t('PRODUCT_CATEGORY_LABEL'); ?> <?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></p>
                    <p class="fs-4 fw-bold mb-3">€<?php echo number_format($product['price'], 2); ?></p>
                    <h5 class="mt-4"><?php echo t('PRODUCT_DESCRIPTION_LABEL'); ?></h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>

                    <div class="mt-4 d-grid gap-2 d-sm-block">
                        <button class="btn btn-success btn-lg add-to-cart-btn"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-price="<?php echo $product['price']; ?>">
                            <i class="bi bi-cart-plus"></i> <?php echo t('BTN_ADD_TO_CART'); ?>
                        </button>
                    </div>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> <?php echo t('BTN_BACK_TO_PRODUCTS'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- AI: Fallback UI for missing product -->
            <div class="alert alert-warning text-center" role="alert">
                <?php echo t('ALERT_PRODUCT_NOT_FOUND'); ?> <a href="index.php" class="alert-link"><?php echo t('ALERT_RETURN_TO_SHOP'); ?></a>.
            </div>
        <?php endif; ?>
    </div>

    <!-- === Footer === -->
    <footer class="text-center mt-5 mb-4">
        <p class="text-muted"><?php echo t('FOOTER_COPYRIGHT', date("Y")); ?></p>
    </footer>

    <?php if (isset($conn)) mysqli_close($conn); ?>

    <!-- JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const globalLangStrings = {
            addedToCart: "<?php echo addslashes(t('ALERT_ADDED_TO_CART', '%s')); ?>",
            // todo: Add more localized strings if needed
        };
    </script>
    <script src="js/cart.js"></script>
</body>
</html>

