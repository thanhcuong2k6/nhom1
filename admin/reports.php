<?php
/**
 * Admin Reports - B�o c�o v� th?ng k�
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'B�o c�o th?ng k�';

$db = new Database();
$conn = $db->getConnection();

// L?y th?i gian filter
$timeFilter = $_GET['time'] ?? 'month'; // month, quarter, year
$fromDate = date('Y-m-d', strtotime('-30 days'));
$toDate = date('Y-m-d');

if ($timeFilter === 'quarter') {
    $fromDate = date('Y-m-d', strtotime('-90 days'));
} elseif ($timeFilter === 'year') {
    $fromDate = date('Y-m-d', strtotime('-365 days'));
}

// 1. Doanh thu theo th?i gian
$revenueQuery = "SELECT DATE(o.created_at) as date, SUM(o.total_amount) as revenue, COUNT(o.id) as order_count
                 FROM orders o
                 WHERE o.created_at >= ? AND o.created_at <= ? AND o.status != 'cancelled'
                 GROUP BY DATE(o.created_at)
                 ORDER BY DATE(o.created_at) DESC";

$stmt = $conn->prepare($revenueQuery);
$toDatePlus = date('Y-m-d 23:59:59', strtotime($toDate));
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$revenueResult = $stmt->get_result();
$revenueData = $revenueResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2. Top s?n ph?m b�n ch?y
$topProductsQuery = "SELECT p.id, p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     JOIN orders o ON oi.order_id = o.id
                     WHERE o.created_at >= ? AND o.created_at <= ? AND o.status != 'cancelled'
                     GROUP BY p.id, p.name
                     ORDER BY total_sold DESC
                     LIMIT 10";

$stmt = $conn->prepare($topProductsQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$topProductsResult = $stmt->get_result();
$topProducts = $topProductsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3. Th?ng k� theo danh m?c
$categoryStatsQuery = "SELECT c.name, COUNT(DISTINCT oi.order_id) as orders, SUM(oi.quantity) as items_sold, SUM(oi.quantity * oi.price) as revenue
                       FROM categories c
                       LEFT JOIN products p ON c.id = p.category_id
                       LEFT JOIN order_items oi ON p.id = oi.product_id
                       LEFT JOIN orders o ON oi.order_id = o.id
                       WHERE o.created_at >= ? AND o.created_at <= ? AND o.status != 'cancelled' OR (o.id IS NULL)
                       GROUP BY c.id, c.name
                       ORDER BY revenue DESC";

$stmt = $conn->prepare($categoryStatsQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$categoryStatsResult = $stmt->get_result();
$categoryStats = $categoryStatsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 4. Kh�ch h�ng m?i
$newCustomersQuery = "SELECT COUNT(*) as count FROM users WHERE created_at >= ? AND created_at <= ? AND role = 'customer'";
$stmt = $conn->prepare($newCustomersQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$newCustomersResult = $stmt->get_result();
$newCustomersCount = $newCustomersResult->fetch_assoc()['count'];
$stmt->close();

// 5. T?ng doanh thu
$totalRevenueQuery = "SELECT SUM(total_amount) as total FROM orders 
                      WHERE created_at >= ? AND created_at <= ? AND status != 'cancelled'";
$stmt = $conn->prepare($totalRevenueQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$totalRevenueResult = $stmt->get_result();
$totalRevenue = (int)($totalRevenueResult->fetch_assoc()['total'] ?? 0);
$stmt->close();

// 6. Tr?ng th�i don h�ng
$statusQuery = "SELECT status, COUNT(*) as count FROM orders 
                WHERE created_at >= ? AND created_at <= ?
                GROUP BY status";
$stmt = $conn->prepare($statusQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$statusResult = $stmt->get_result();
$statuses = $statusResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 7. Kh�ch h�ng c� t?ng chi ti�u cao nh?t
$topCustomersQuery = "SELECT u.id, u.name, u.email, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent
                      FROM users u
                      LEFT JOIN orders o ON u.id = o.user_id
                      WHERE o.created_at >= ? AND o.created_at <= ? AND o.status != 'cancelled'
                      GROUP BY u.id, u.name, u.email
                      ORDER BY total_spent DESC
                      LIMIT 10";

$stmt = $conn->prepare($topCustomersQuery);
$stmt->bind_param('ss', $fromDate, $toDatePlus);
$stmt->execute();
$topCustomersResult = $stmt->get_result();
$topCustomers = $topCustomersResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>admin/admin-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>

            <ul class="sidebar-menu">
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/">?? Dashboard</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php" class="active">?? B�o c�o</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/users.php">?? Ngu?i d�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php">??? Voucher</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>?? B�o c�o th?ng k�</h1>
                <div class="filters">
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php?time=month" class="filter-btn <?php echo $timeFilter === 'month' ? 'active' : ''; ?>">Th�ng</a>
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php?time=quarter" class="filter-btn <?php echo $timeFilter === 'quarter' ? 'active' : ''; ?>">Qu�</a>
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php?time=year" class="filter-btn <?php echo $timeFilter === 'year' ? 'active' : ''; ?>">Nam</a>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">?? T?ng doanh thu</div>
                    <div class="stat-value"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>?</div>
                    <div class="stat-change">?? +12% so v?i k? tru?c</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">?? S? don h�ng</div>
                    <div class="stat-value"><?php echo count($revenueData); ?></div>
                    <div class="stat-change">?? Ng�y b�nh qu�n</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">?? Kh�ch h�ng m?i</div>
                    <div class="stat-value"><?php echo $newCustomersCount; ?></div>
                    <div class="stat-change">?? +5% tang</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">? Trung b�nh don h�ng</div>
                    <div class="stat-value"><?php echo count($revenueData) > 0 ? number_format($totalRevenue / count($revenueData), 0, ',', '.') : '0'; ?>?</div>
                    <div class="stat-change">?? M?c ti�u AOV</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-title">?? Doanh thu theo ng�y</div>
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="chart-container">
                    <div class="chart-title">?? Tr?ng th�i don h�ng</div>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Top Products -->
            <div class="section-title">?? Top s?n ph?m b�n ch?y</div>
            <table>
                <thead>
                    <tr>
                        <th>S?n ph?m</th>
                        <th>S? lu?ng b�n</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['total_sold']; ?> s?n ph?m</td>
                        <td><?php echo number_format($product['revenue'], 0, ',', '.'); ?>?</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Category Stats -->
            <div class="section-title">?? Th?ng k� theo danh m?c</div>
            <table>
                <thead>
                    <tr>
                        <th>Danh m?c</th>
                        <th>�on h�ng</th>
                        <th>S?n ph?m b�n</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoryStats as $cat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo $cat['orders'] ?? 0; ?></td>
                        <td><?php echo $cat['items_sold'] ?? 0; ?></td>
                        <td><?php echo number_format($cat['revenue'] ?? 0, 0, ',', '.'); ?>?</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Top Customers -->
            <div class="section-title">?? Kh�ch h�ng chi ti�u cao nh?t</div>
            <table>
                <thead>
                    <tr>
                        <th>Kh�ch h�ng</th>
                        <th>Email</th>
                        <th>S? don</th>
                        <th>T?ng chi ti�u</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topCustomers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo $customer['order_count']; ?></td>
                        <td><?php echo number_format($customer['total_spent'] ?? 0, 0, ',', '.'); ?>?</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Revenue Chart
        <?php
        $dates = array_column($revenueData, 'date');
        $revenues = array_column($revenueData, 'revenue');
        $dates = array_reverse($dates);
        $revenues = array_reverse($revenues);
        $datesJson = json_encode(array_map(function($d) { return date('d/m', strtotime($d)); }, $dates));
        $revenuesJson = json_encode($revenues);
        ?>
        
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo $datesJson; ?>,
                datasets: [{
                    label: 'Doanh thu (?)',
                    data: <?php echo $revenuesJson; ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Status Chart
        <?php
        $statusLabels = array_column($statuses, 'status');
        $statusCounts = array_column($statuses, 'count');
        $statusLabelsJson = json_encode($statusLabels);
        $statusCountsJson = json_encode($statusCounts);
        ?>

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo $statusLabelsJson; ?>,
                datasets: [{
                    data: <?php echo $statusCountsJson; ?>,
                    backgroundColor: [
                        '#FCD34D',
                        '#60A5FA',
                        '#A78BFA',
                        '#6EE7B7',
                        '#F87171'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>

