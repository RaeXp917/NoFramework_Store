<?php
session_start(); // Start the session to manage login state

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit;
}

// --- Now the rest of your admin page logic begins below ---
require_once '../config.php'; // Include the configuration file
// ... include other necessary files ...
?>
<!-- Here, you can add the HTML or other PHP code for your admin page -->

<?php
require_once '../config.php'; // Include the language.php file for translations
$page_title = t('ADMIN_MANAGE_PRODUCTS_TITLE'); // Get the translated title for the page
require_once 'partials/header.php'; // Include the header for the admin page

// --- Sorting Logic ---
$sort_column = $_GET['sort'] ?? 'name'; // Default sorting is by 'name'
$sort_dir = $_GET['dir'] ?? 'asc'; // Default sorting direction is ascending

// Validate the sort column to ensure it's one of the allowed values
$allowed_sort_columns = ['id', 'name', 'category', 'price'];
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = 'name'; // Fallback to 'name' if it's invalid
}
// Validate the sort direction, ensuring it's either 'asc' or 'desc'
if (!in_array(strtolower($sort_dir), ['asc', 'desc'])) {
    $sort_dir = 'asc'; // Default to ascending if the direction is invalid
}
// --- End Sorting Logic ---

// *** Handling Search Functionality ***
$search_term = isset($_GET['search']) ? trim($_GET['search']) : ''; // Get the search term if provided
// *** End Search Handling ***

// *** Construct the WHERE Clause & Parameters for Searching ***
$where_sql = "";
$params = []; // Prepare an array for query parameters
$param_types = ""; // Define the parameter types for binding

if (!empty($search_term)) {
    // Check if the search term is purely numeric
    if (ctype_digit($search_term)) { // If it's a number, search by ID
        $where_sql = " WHERE id = ?";
        $params[] = $search_term;
        $param_types = "i"; // Treat ID as an integer
    } else {
        // If it's not a number, search by name
        $where_sql = " WHERE name LIKE ?";
        $params[] = "%" . $search_term . "%";
        $param_types = "s"; // Treat name as a string
    }
}
// *** End WHERE Clause Building ***

// Prepare SQL query for fetching products with dynamic sorting and search functionality
$sql = "SELECT id, name, category, price, image FROM products" . $where_sql . " ORDER BY {$sort_column} " . strtoupper($sort_dir);

// *** Execute Prepared Statement for Search ***
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Admin products prepare failed: " . mysqli_error($conn));
    die("Error preparing product query.");
}

// Bind parameters only if a search term exists
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
// *** End Prepared Statement Execution ***

if (!$result) {
    error_log("Database query failed in admin/products.php: " . mysqli_error($conn));
    die("Error fetching products.");
}

// Helper function to generate sort links, including the search term
function sort_link($column_name, $display_text_key, $current_sort_column, $current_sort_dir, $current_search_term) {
    $icon = '';
    $link_dir = 'asc';
    if ($column_name === $current_sort_column) {
        // Change the icon based on the current sorting direction
        $icon = ($current_sort_dir === 'asc') ? ' <i class="bi bi-sort-up"></i>' : ' <i class="bi bi-sort-down"></i>';
        $link_dir = ($current_sort_dir === 'asc') ? 'desc' : 'asc';
    }

    $query_params = $_GET; // Keep all existing query parameters
    $query_params['sort'] = $column_name; // Set the new sort column
    $query_params['dir'] = $link_dir; // Set the new sort direction
    // Add the search term to the link if it's available
    if (!empty($current_search_term)) {
        $query_params['search'] = $current_search_term;
    } else {
        unset($query_params['search']); // Remove the search term if empty
    }

    $link = '?' . http_build_query($query_params);

    return '<a href="' . $link . '">' . t($display_text_key) . $icon . '</a>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $page_title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="product_form.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> <?php echo t('ADMIN_PRODUCTS_ADD_NEW'); ?>
        </a>
    </div>
</div>

<!-- *** Search Form for Products *** -->
<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" action="products.php" class="input-group">
             <!-- Preserve sorting parameters in the hidden inputs -->
             <?php if (isset($_GET['sort'])): ?>
                 <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
             <?php endif; ?>
             <?php if (isset($_GET['dir'])): ?>
                 <input type="hidden" name="dir" value="<?php echo htmlspecialchars($_GET['dir']); ?>">
             <?php endif; ?>

            <input type="search" class="form-control" placeholder="Search by ID or Name..." name="search" value="<?php echo htmlspecialchars($search_term); ?>" aria-label="Search Products">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="bi bi-search"></i> <?php echo t('BTN_SEARCH'); ?>
            </button>
        </form>
    </div>
</div>
<!-- *** End Search Form *** -->


<?php
// Show session messages (e.g., success or error messages)
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['message'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $_SESSION['error'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
}
?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <!-- Generate sort links for each column -->
                <th scope="col"><?php echo sort_link('id', 'ADMIN_PRODUCTS_TABLE_ID', $sort_column, $sort_dir, $search_term); ?></th>
                <th scope="col"><?php echo t('ADMIN_PRODUCTS_TABLE_IMAGE'); ?></th>
                <th scope="col"><?php echo sort_link('name', 'ADMIN_PRODUCTS_TABLE_NAME', $sort_column, $sort_dir, $search_term); ?></th>
                <th scope="col"><?php echo sort_link('category', 'ADMIN_PRODUCTS_TABLE_CATEGORY', $sort_column, $sort_dir, $search_term); ?></th>
                <th scope="col"><?php echo sort_link('price', 'ADMIN_PRODUCTS_TABLE_PRICE', $sort_column, $sort_dir, $search_term); ?></th>
                <th scope="col" class="text-center"><?php echo t('ADMIN_PRODUCTS_TABLE_ACTIONS'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are products to display
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Set the image path (use a placeholder if image is not found)
                    $imagePath = '../images/placeholder.png'; // Default placeholder image
                    $potentialImagePath = '../' . ltrim($row["image"] ?? '', '/'); // Construct potential image path

                    if (!empty($row["image"]) && file_exists($potentialImagePath)) {
                        $imagePath = $potentialImagePath;
                    }

            ?>
                    <tr>
                        <th scope="row"><?php echo $row['id']; ?></th>
                        <td> <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 50px; height: 50px; object-fit: contain;"> </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category'] ?? 'N/A'); ?></td>
                        <td>€<?php echo number_format($row['price'], 2); ?></td>
                        <td class="text-center">
                            <a href="product_form.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" title="<?php echo t('ADMIN_PRODUCTS_EDIT_TITLE'); ?>"> <i class="bi bi-pencil-fill"></i> </a>
                            <!-- Delete form - Prompt the user for confirmation before deleting -->
                            <form action="delete_product.php" method="POST" class="d-inline" onsubmit="return confirm('<?php echo t('ADMIN_PRODUCTS_DELETE_CONFIRM', htmlspecialchars(addslashes($row['name']))); ?>');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="<?php echo t('ADMIN_PRODUCTS_DELETE_TITLE'); ?>">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
            <?php
                }
            } else { // If no results found, show a message
            ?>
                <tr><td colspan="6" class="text-center"><?php echo t('ADMIN_PRODUCTS_NONE_FOUND'); ?><?php echo !empty($search_term) ? ' ' . t('ADMIN_PRODUCTS_MATCHING_SEARCH') : ''; ?></td></tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Close the prepared statement and connection
if(isset($stmt)) mysqli_stmt_close($stmt);
if(isset($conn)) mysqli_close($conn);

// Include the footer for the admin page
require_once 'partials/footer.php';
?>
