<?php
/**
 * Admin Orders Management - Qu?n l� don h�ng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Qu?n l� don h�ng';

$db = new Database();
$conn = $db->getConnection();

// X? l� c?p nh?t tr?ng th�i
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($status, $allowedStatuses)) {
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $status, $orderId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'C?p nh?t tr?ng th�i don h�ng th�nh c�ng!';
        }
        $stmt->close();
    }
    
    header('Location: ' . Config::get('APP_URL') . 'admin/orders.php');
    exit;
}

// L?y danh s�ch don h�ng
$filter = $_GET['filter'] ?? 'all';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$whereClause = '';
if ($filter !== 'all') {
    $whereClause = " WHERE o.status = '" . $conn->real_escape_string($filter) . "'";
}

// L?y t?ng s? don h�ng
$countQuery = "SELECT COUNT(*) as total FROM orders o" . $whereClause;
$countResult = $conn->query($countQuery);
$totalOrders = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalOrders / $perPage);

// L?y danh s�ch don h�ng
$query = "SELECT o.id, o.order_number, o.user_id, u.name, o.total_amount, o.status, o.created_at 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id" . 
          $whereClause . 
          " ORDER BY o.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// H�m l?y badge status
function getStatusBadge($status) {
    $badges = [
        'pending' => ['? Ch? x? l�', '#FCD34D'],
        'processing' => ['?? �ang x? l�', '#60A5FA'],
        'shipped' => ['?? �� g?i', '#A78BFA'],
        'delivered' => ['? �� giao', '#6EE7B7'],
        'cancelled' => ['? �� h?y', '#F87171']
    ];
    
    if (isset($badges[$status])) {
        return $badges[$status];
    }
    return [$status, '#D1D5DB'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>admin/admin-style.css">
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php" class="active">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>?? Qu?n l� don h�ng</h1>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    ? <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <div class="filters">
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">T?t c?</a>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=pending" class="filter-btn <?php echo $filter === 'pending' ? 'active' : ''; ?>">? Ch? x? l�</a>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=processing" class="filter-btn <?php echo $filter === 'processing' ? 'active' : ''; ?>">?? �ang x? l�</a>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=shipped" class="filter-btn <?php echo $filter === 'shipped' ? 'active' : ''; ?>">?? �� g?i</a>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=delivered" class="filter-btn <?php echo $filter === 'delivered' ? 'active' : ''; ?>">? �� giao</a>
            </div>

            <div class="content-section">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <p>?? Kh�ng c� don h�ng n�o</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>M� don h�ng</th>
                                <th>Kh�ch h�ng</th>
                                <th>T?ng ti?n</th>
                                <th>Tr?ng th�i</th>
                                <th>Ng�y t?o</th>
                                <th>Thao t�c</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <?php 
                                $statusInfo = getStatusBadge($order['status']);
                                $statusLabel = $statusInfo[0];
                                $statusColor = $statusInfo[1];
                            ?>
                            <tr>
                                <td><span class="order-number">#<?php echo htmlspecialchars($order['order_number']); ?></span></td>
                                <td><span class="customer-name"><?php echo htmlspecialchars($order['name'] ?? 'Guest'); ?></span></td>
                                <td><span class="amount"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>?</span></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit();">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>? Ch? x? l�</option>
                                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>?? �ang x? l�</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>?? �� g?i</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>? �� giao</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>? �� h?y</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo Config::get('APP_URL'); ?>admin/order-detail.php?id=<?php echo $order['id']; ?>" class="btn-small btn-view">??? Xem</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=<?php echo urlencode($filter); ?>&page=1">� �?u</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=<?php echo urlencode($filter); ?>&page=<?php echo $page - 1; ?>">� Tru?c</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=<?php echo urlencode($filter); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=<?php echo urlencode($filter); ?>&page=<?php echo $page + 1; ?>">Sau �</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php?filter=<?php echo urlencode($filter); ?>&page=<?php echo $totalPages; ?>">Cu?i �</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

