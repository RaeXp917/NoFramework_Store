<?php
session_start();

// Check if the admin is logged in, if not redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=login_required');
    exit;
}

require_once '../config.php'; // Load database and language settings
$page_title = t('ADMIN_DASHBOARD_TITLE'); // Set dynamic page title
require_once 'partials/header.php'; // Load the header
?>

<?php
// --- Fetch data for the chart ---
$chart_labels = [];
$chart_data = [];
$chart_error = null;

// Get top 5 best-selling products by quantity sold
$chart_sql = "
    SELECT p.name, SUM(oi.quantity) as total_quantity
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id, p.name
    ORDER BY total_quantity DESC
    LIMIT 5
";

$result = mysqli_query($conn, $chart_sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $chart_labels[] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $chart_data[] = (int)$row['total_quantity'];
        }
    } else {
        $chart_error = t('ADMIN_CHART_NO_DATA'); // No data to display yet
    }
} else {
    error_log("Chart SQL error: " . mysqli_error($conn));
    $chart_error = t('ADMIN_CHART_ERROR'); // Friendly error message for user
}

// Encode data safely for JS
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $page_title; ?></h1>
</div>

<p><?php echo t('ADMIN_DASHBOARD_WELCOME'); ?></p>
<p><?php echo t('ADMIN_DASHBOARD_INFO'); ?></p>

<!-- Chart Section -->
<div class="row mt-4">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <?php echo t('ADMIN_CHART_TITLE'); ?>
            </div>
            <div class="card-body">
                <?php if ($chart_error): ?>
                    <div class="alert alert-info"><?php echo $chart_error; ?></div>
                <?php elseif (!empty($chart_labels)): ?>
                    <canvas id="mostPurchasedChart" width="400" height="200"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const ctx = document.getElementById('mostPurchasedChart');
                            if (!ctx) {
                                console.error("Chart canvas not found!");
                                return;
                            }

                            const labels = <?php echo $chart_labels_json; ?>;
                            const data = <?php echo $chart_data_json; ?>;

                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: '<?php echo t('ADMIN_CHART_LABEL_QUANTITY'); ?>',
                                        data: data,
                                        backgroundColor: [
                                            'rgba(54, 162, 235, 0.6)',
                                            'rgba(255, 99, 132, 0.6)',
                                            'rgba(75, 192, 192, 0.6)',
                                            'rgba(255, 206, 86, 0.6)',
                                            'rgba(153, 102, 255, 0.6)'
                                        ],
                                        borderColor: [
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(153, 102, 255, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1,
                                                precision: 0
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        title: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                <?php else: ?>
                    <div class="alert alert-info"><?php echo t('ADMIN_CHART_NO_DATA'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Admin Options -->
<div class="row mt-4">
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo t('ADMIN_MANAGE_PRODUCTS_TITLE'); ?></h5>
                <p class="card-text"><?php echo t('ADMIN_MANAGE_PRODUCTS_TEXT'); ?></p>
                <a href="products.php" class="btn btn-primary"><?php echo t('ADMIN_MANAGE_PRODUCTS_BTN'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo t('ADMIN_VIEW_ORDERS_TITLE'); ?></h5>
                <p class="card-text"><?php echo t('ADMIN_VIEW_ORDERS_TEXT'); ?></p>
                <a href="orders.php" class="btn btn-secondary"><?php echo t('ADMIN_VIEW_ORDERS_BTN'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'partials/footer.php';
?>
