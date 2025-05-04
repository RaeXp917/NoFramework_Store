<?php
// 1. Include config FIRST
require_once 'config.php';

// *** PARAMETER HANDLING ***
$current_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc'; // Default sort

// *** PAGINATION CONFIGURATION ***
$products_per_page = 8; // Show 8 products per page

// *** GET CURRENT PAGE ***
$current_page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;

// *** BUILD DYNAMIC SQL WHERE CLAUSE & PARAMETERS ***
$where_clauses = [];
$params_where = []; // Parameters for WHERE clause
$param_types_where = ""; // Parameter types for WHERE clause

// Category Filter
if ($current_category !== 'all') {
    $where_clauses[] = "category = ?";
    $params_where[] = $current_category;
    $param_types_where .= "s";
}

// Search Filter
if (!empty($search_term)) {
    $where_clauses[] = "name LIKE ?";
    $params_where[] = "%" . $search_term . "%";
    $param_types_where .= "s";
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}

// *** BUILD DYNAMIC SQL ORDER BY CLAUSE ***
$order_by_sql = " ORDER BY ";
switch ($sort_by) {
    case 'price_asc': $order_by_sql .= "price ASC"; break;
    case 'price_desc': $order_by_sql .= "price DESC"; break;
    case 'name_desc': $order_by_sql .= "name DESC"; break;
    case 'category': $order_by_sql .= "category ASC, name ASC"; break;
    case 'name_asc': default: $order_by_sql .= "name ASC"; break;
}

// *** CALCULATE TOTAL PRODUCTS (WITH FILTERS) ***
$count_sql = "SELECT COUNT(*) as total FROM products" . $where_sql;
$stmt_count = mysqli_prepare($conn, $count_sql);
if ($stmt_count === false) { die("Prepare failed (count): " . htmlspecialchars(mysqli_error($conn))); } // Check prepare error
if (!empty($params_where)) {
    mysqli_stmt_bind_param($stmt_count, $param_types_where, ...$params_where);
}
mysqli_stmt_execute($stmt_count);
$count_result = mysqli_stmt_get_result($stmt_count);
$total_products = ($count_result) ? (int)mysqli_fetch_assoc($count_result)['total'] : 0;
mysqli_stmt_close($stmt_count);

// *** CALCULATE TOTAL PAGES and OFFSET ***
$total_pages = ($products_per_page > 0) ? ceil($total_products / $products_per_page) : 0;
$offset = ($current_page - 1) * $products_per_page;

// Bounds check for current page
if ($current_page > $total_pages && $total_pages > 0) { $current_page = $total_pages; }
if ($current_page < 1) { $current_page = 1; }
$offset = ($current_page - 1) * $products_per_page; // Recalculate offset after bounds check

// *** Combine parameters for the final query (WHERE + LIMIT/OFFSET) ***
$params_all = $params_where;
$param_types_all = $param_types_where;

$params_all[] = $products_per_page;
$param_types_all .= "i";
$params_all[] = $offset;
$param_types_all .= "i";

// 2. Fetch Products *** WITH FILTERS, SORTING, PAGINATION ***
$sql = "SELECT id, name, price, image, category FROM products" . $where_sql . $order_by_sql . " LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) { die("Prepare failed (main): " . htmlspecialchars(mysqli_error($conn))); } // Check prepare error
// Bind all parameters
mysqli_stmt_bind_param($stmt, $param_types_all, ...$params_all);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    error_log("Database query failed: " . mysqli_error($conn));
    die(t("ERROR_FETCHING_PRODUCTS"));
}

// 3. Fetch Categories (No changes needed)
$categories = [];
$cat_sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
$cat_result = mysqli_query($conn, $cat_sql);
if ($cat_result && mysqli_num_rows($cat_result) > 0) {
    while ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $categories[] = $cat_row['category'];
    }
}

// Helper function to build URLs preserving relevant parameters
function build_url($page_num = null) {
    global $current_category, $search_term, $sort_by, $current_page;

    $params = $_GET; // Start with existing params like 'lang'

    // Set/update core parameters
    $params['category'] = $current_category;
    if (!empty($search_term)) $params['search'] = $search_term; else unset($params['search']);
    $params['sort'] = $sort_by;

    // Set/update page number
    if ($page_num !== null) { $params['page'] = $page_num; }
    else { $params['page'] = $current_page; } // Use current page if page_num not provided

    // Clean up default parameters for shorter URLs
    if ($params['category'] === 'all') unset($params['category']);
    if ($params['sort'] === 'name_asc') unset($params['sort']);
    if ($params['page'] <= 1) unset($params['page']);

    return 'index.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}

?>
<!doctype html>
<html lang="<?php echo CURRENT_LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo STORE_BRAND_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar (No changes) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                 <img src="images/logo.png" alt="<?php echo STORE_BRAND_NAME; ?> Logo" class="logo me-2">
                <?php echo STORE_BRAND_NAME; ?>
            </a>
            <div class="ms-auto d-flex align-items-center lang-switcher">
                 <a href="?lang=en" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'en' ? 'active' : ''); ?>" title="English">
                     <img src="images/flag_en.png" alt="EN Flag">
                 </a>
                 <a href="?lang=el" class="nav-link px-2 link-secondary <?php echo (CURRENT_LANG == 'el' ? 'active' : ''); ?>" title="Ελληνικά">
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
     <div class="container mt-4">
        <div class="row">

            <!-- *** Sidebar (Category links generated by PHP) *** -->
            <div class="col-lg-3 col-md-4 mb-4">
                <ul class="list-group" id="category-filter-list">
                    <?php
                        // Build URL for 'All' category link
                        $all_cat_params = $_GET;
                        unset($all_cat_params['category']);
                        unset($all_cat_params['page']);
                        $all_cat_url = 'index.php' . (!empty($all_cat_params) ? '?' . http_build_query($all_cat_params) : '');
                    ?>
                    <a href="<?php echo $all_cat_url; ?>"
                       class="list-group-item list-group-item-action category-filter <?php echo ($current_category === 'all') ? 'active' : ''; ?>"
                       data-category="all" <?php echo ($current_category === 'all') ? 'aria-current="true"' : ''; ?>>
                        <?php echo t('FILTER_ALL'); ?>
                    </a>
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $safeCategory = htmlspecialchars($category);
                            // Build URL for this category link
                            $cat_params = $_GET;
                            $cat_params['category'] = $category;
                            unset($cat_params['page']); // Reset page
                            $cat_url = 'index.php?' . http_build_query($cat_params);
                            $is_active = ($current_category === $category);
                            echo '<a href="' . $cat_url . '" class="list-group-item list-group-item-action category-filter ' . ($is_active ? 'active' : '') . '" data-category="' . $safeCategory . '" ' . ($is_active ? 'aria-current="true"' : '') . '>' . $safeCategory . '</a>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <!-- *** End Sidebar *** -->

            <!-- *** Main Content *** -->
            <div class="col-lg-9 col-md-8">
                <h1 class="mb-4 text-center"><?php echo t('HOME_PRODUCTS_TITLE'); ?></h1>

                <!-- Search Bar (Inside a form) -->
                <div class="row mb-4">
                    <div class="col-md-8 offset-md-2">
                        <form method="GET" action="index.php" class="input-group">
                            <!-- Hidden inputs to preserve other parameters -->
                            <?php foreach ($_GET as $key => $value): ?>
                                <?php if (!in_array($key, ['search', 'page'])): // Preserve all except search and page ?>
                                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <span class="input-group-text" id="search-addon"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" placeholder="<?php echo t('SEARCH_PLACEHOLDER'); ?>" id="product-search-input" name="search" value="<?php echo htmlspecialchars($search_term); ?>" aria-label="Search" aria-describedby="search-addon">
                            <button class="btn btn-outline-secondary" type="submit"><?php echo t('BTN_SEARCH'); // Add translation ?></button>
                        </form>
                    </div>
                </div>
                <!-- End Search Bar -->

                <!-- Sorting Controls (Selected option set by PHP) -->
                <div class="d-flex justify-content-end mb-3">
                    <div class="d-flex align-items-center">
                        <label for="sort-select" class="form-label me-2 mb-0 fw-bold"><?php echo t('SORT_BY_LABEL'); ?></label>
                        <select class="form-select form-select-sm w-auto" id="sort-select" aria-label="Sort products">
                            <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>><?php echo t('SORT_NAME_ASC'); ?></option>
                            <option value="name_desc" <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>><?php echo t('SORT_NAME_DESC'); ?></option>
                            <option value="price_asc" <?php echo ($sort_by === 'price_asc') ? 'selected' : ''; ?>><?php echo t('SORT_PRICE_ASC'); ?></option>
                            <option value="price_desc" <?php echo ($sort_by === 'price_desc') ? 'selected' : ''; ?>><?php echo t('SORT_PRICE_DESC'); ?></option>
                            <option value="category" <?php echo ($sort_by === 'category') ? 'selected' : ''; ?>><?php echo t('SORT_CATEGORY'); ?></option>
                        </select>
                    </div>
                </div>
                <!-- End Sorting Controls -->

                <!-- Product Grid (Displays filtered/sorted/paginated results) -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 g-4" id="product-list">
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            // Product card rendering (no changes needed)
                            $category = !empty($row["category"]) ? htmlspecialchars($row["category"]) : 'uncategorized';
                            echo '<div class="col product-card" data-category="' . $category . '" data-price="' . $row["price"] . '" data-name="' . htmlspecialchars($row["name"]) . '">';
                            echo '  <div class="card h-100 shadow-sm">';
                            $imagePath = 'images/placeholder.png';
                            $potentialImagePath = 'images/' . basename($row["image"]);
                            if (!empty($row["image"]) && file_exists($potentialImagePath)) { $imagePath = $potentialImagePath; }
                            elseif (!empty($row["image"]) && file_exists($row["image"])) { $imagePath = $row["image"]; }
                            echo '    <a href="product.php?id=' . $row["id"] . '"><img src="' . htmlspecialchars($imagePath) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '"></a>';
                            echo '    <div class="card-body d-flex flex-column">';
                            echo '      <div>';
                            echo '          <h5 class="card-title"><a href="product.php?id=' . $row["id"] . '" class="text-decoration-none text-dark">' . htmlspecialchars($row["name"]) . '</a></h5>';
                            echo '          <p class="card-text text-muted small mb-2">' . t('PRODUCT_CATEGORY_LABEL') . ' ' . $category . '</p>';
                            echo '          <p class="card-text fw-bold fs-5 mb-3">€' . number_format($row["price"], 2) . '</p>';
                            echo '      </div>';
                            echo '      <div class="mt-auto d-grid gap-2 d-sm-flex justify-content-sm-center">';
                            echo '          <a href="product.php?id=' . $row["id"] . '" class="btn btn-outline-secondary btn-sm">' . t('BTN_VIEW_DETAILS') . '</a>';
                            echo '          <button class="btn btn-success btn-sm add-to-cart-btn" data-id="' . $row["id"] . '" data-name="' . htmlspecialchars($row["name"]) . '" data-price="' . $row["price"] . '">' . t('BTN_ADD_TO_CART') . '</button>';
                            echo '      </div>';
                            echo '    </div>';
                            echo '  </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12"><p class="text-center">' . t('NO_PRODUCTS_FOUND') . '</p></div>';
                    }
                    ?>
                </div>
                <!-- End Product Grid -->

                <!-- Pagination Links (Uses updated build_url) -->
                <nav aria-label="Product navigation" class="mt-5 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php if ($total_pages > 1): ?>
                            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page > 1) ? build_url($current_page - 1) : '#'; ?>" aria-label="Previous"><span aria-hidden="true">«</span></a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>" <?php echo ($i == $current_page) ? 'aria-current="page"' : ''; ?>>
                                    <a class="page-link" href="<?php echo build_url($i); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page < $total_pages) ? build_url($current_page + 1) : '#'; ?>" aria-label="Next"><span aria-hidden="true">»</span></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- End Pagination Links -->

            </div>
            <!-- End Main Content -->

        </div> <!-- End Row -->
     </div> <!-- End Container -->

     <!-- Footer (No changes) -->
     <footer class="text-center mt-5 mb-4"> <p class="text-muted"><?php echo t('FOOTER_COPYRIGHT', date("Y")); ?></p> </footer>

    <?php
    if (isset($stmt)) mysqli_stmt_close($stmt);
    if(isset($conn)) mysqli_close($conn);
    ?>A

    <!-- JS Includes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pass language strings (no changes needed here)
        const globalLangStrings = {
            addedToCart: "<?php echo addslashes(t('ALERT_ADDED_TO_CART', '%s')); ?>",
            // Add other needed strings if cart.js relies on them
        };
    </script>
    <script src="js/cart.js"></script> <!-- Cart logic remains client-side -->
    <script src="js/filter.js"></script> <!-- Needs to be the simplified version -->
</body>
</html>