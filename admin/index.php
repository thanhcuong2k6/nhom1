<?php
/**
 * Admin Dashboard - Trang ch�nh qu?n tr?
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$admin = Auth::getCurrentUser();
$pageTitle = 'Admin Dashboard';

// K?t n?i database
$db = new Database();
$conn = $db->getConnection();

// L?y th?ng k�
$stats = [];

// T?ng s?n ph?m
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// T?ng danh m?c
$result = $conn->query("SELECT COUNT(*) as count FROM categories");
$stats['categories'] = $result->fetch_assoc()['count'];

// T?ng don h�ng
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// T?ng users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['users'] = $result->fetch_assoc()['count'];

// Doanh thu th�ng n�y
$result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
$revenue = $result->fetch_assoc()['revenue'] ?? 0;

// �on h�ng m?i nh?t
$result = $conn->query("SELECT id, order_number, total_amount, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5");
$recentOrders = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - FashionHub Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>admin/admin-style.css">

</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>FashionHub</p>
            </div>

            <ul class="sidebar-menu">
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/" class="active">?? Dashboard</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1>?? Dashboard Admin</h1>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($admin['name'], 0, 1)); ?></div>
                    <div class="user-details">
                        <p><?php echo htmlspecialchars($admin['name']); ?></p>
                        <p>?? Qu?n tr? vi�n</p>
                    </div>
                    <a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php" class="logout-btn">�ang xu?t</a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>?? T?ng s?n ph?m</h3>
                    <div class="stat-value"><?php echo number_format($stats['products']); ?></div>
                    <p class="stat-change">? C� <?php echo $stats['products']; ?> s?n ph?m</p>
                </div>

                <div class="stat-card">
                    <h3>?? T?ng danh m?c</h3>
                    <div class="stat-value"><?php echo number_format($stats['categories']); ?></div>
                    <p class="stat-change">? C� <?php echo $stats['categories']; ?> danh m?c</p>
                </div>

                <div class="stat-card">
                    <h3>?? T?ng don h�ng</h3>
                    <div class="stat-value"><?php echo number_format($stats['orders']); ?></div>
                    <p class="stat-change">? C� <?php echo $stats['orders']; ?> don h�ng</p>
                </div>

                <div class="stat-card">
                    <h3>?? Kh�ch h�ng</h3>
                    <div class="stat-value"><?php echo number_format($stats['users']); ?></div>
                    <p class="stat-change">? C� <?php echo $stats['users']; ?> kh�ch h�ng</p>
                </div>

                <div class="stat-card">
                    <h3>?? Doanh thu th�ng n�y</h3>
                    <div class="stat-value"><?php echo number_format($revenue, 0, ',', '.'); ?></div>
                    <p class="stat-change">? Th�ng hi?n t?i</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="content-section">
                <h3 class="section-title">?? �on h�ng g?n d�y</h3>
                <div class="recent-orders">
                    <table>
                        <thead>
                            <tr>
                                <th>M� don h�ng</th>
                                <th>T?ng ti?n</th>
                                <th>Tr?ng th�i</th>
                                <th>Ng�y t?o</th>
                                <th>H�nh d?ng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> d</td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php 
                                        $statusLabels = [
                                            'pending' => '? Ch? x? l�',
                                            'processing' => '?? �ang x? l�',
                                            'shipped' => '?? �ang giao',
                                            'delivered' => '? �� giao',
                                            'cancelled' => '? H?y'
                                        ];
                                        echo $statusLabels[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?id=<?php echo $order['id']; ?>" style="color: #667eea; font-weight: 600;">Chi ti?t ?</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="admin-links">
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/products.php" class="btn-admin">Qu?n l� s?n ph?m</a>
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php" class="btn-admin">Qu?n l� danh m?c</a>
                    <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php" class="btn-admin">Qu?n l� don h�ng</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

