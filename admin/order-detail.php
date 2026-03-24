<?php
/**
 * Admin Order Detail - Chi ti?t don h�ng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Chi ti?t don h�ng';

$db = new Database();
$conn = $db->getConnection();

// Ki?m tra ID don h�ng
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = '�on h�ng kh�ng h?p l?!';
    header('Location: ' . Config::get('APP_URL') . 'admin/orders.php');
    exit;
}

$orderId = (int)$_GET['id'];

// X? l� c?p nh?t tr?ng th�i
if (isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($status, $allowedStatuses)) {
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $status, $orderId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'C?p nh?t tr?ng th�i don h�ng th�nh c�ng!';
            header('Location: ' . Config::get('APP_URL') . 'admin/order-detail.php?id=' . $orderId);
            exit;
        }
        $stmt->close();
    }
}

// L?y th�ng tin don h�ng
$query = "SELECT o.id, o.order_number, o.user_id, o.total_amount, o.status, o.shipping_address, 
          o.phone, o.email, o.created_at, u.name as customer_name
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.id
          WHERE o.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Kh�ng t�m th?y don h�ng!';
    header('Location: ' . Config::get('APP_URL') . 'admin/orders.php');
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// L?y c�c s?n ph?m trong don h�ng
$itemsQuery = "SELECT oi.id, oi.product_id, oi.quantity, oi.price, p.name, p.image
               FROM order_items oi
               LEFT JOIN products p ON oi.product_id = p.id
               WHERE oi.order_id = ?";

$stmt = $conn->prepare($itemsQuery);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$itemsResult = $stmt->get_result();
$items = $itemsResult->fetch_all(MYSQLI_ASSOC);
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

$statusInfo = getStatusBadge($order['status']);
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
                <h1>?? Chi ti?t don h�ng #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php" class="btn-back">? Quay l?i</a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    ? <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <div class="detail-grid">
                <!-- Main Info -->
                <div>
                    <!-- Order Info -->
                    <div class="section">
                        <div class="section-title">Th�ng tin don h�ng</div>
                        
                        <div class="info-row">
                            <span class="info-label">M� don h�ng:</span>
                            <span class="info-value">#<?php echo htmlspecialchars($order['order_number']); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Kh�ch h�ng:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['email'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">�i?n tho?i:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['phone'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">�?a ch? giao h�ng:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Ng�y t?o:</span>
                            <span class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($order['created_at'])); ?></span>
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="section" style="margin-top: 30px;">
                        <div class="section-title">S?n ph?m trong don h�ng</div>
                        
                        <?php if (empty($items)): ?>
                            <p style="text-align: center; color: #999;">Kh�ng c� s?n ph?m n�o</p>
                        <?php else: ?>
                            <table class="products-table">
                                <thead>
                                    <tr>
                                        <th>S?n ph?m</th>
                                        <th>S? lu?ng</th>
                                        <th>Gi�</th>
                                        <th>T?ng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="product-item">
                                                <div class="product-image">??</div>
                                                <div class="product-info">
                                                    <div class="product-name"><?php echo htmlspecialchars($item['name'] ?? 'S?n ph?m'); ?></div>
                                                    <div class="product-id">ID: <?php echo htmlspecialchars($item['product_id']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?>?</td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>?</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <div class="total-row">
                                T?ng c?ng: <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>?
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div>
                    <!-- Status Update -->
                    <div class="section">
                        <div class="section-title">Tr?ng th�i don h�ng</div>
                        
                        <div class="status-badge" style="background-color: <?php echo $statusInfo[1]; ?>; width: 100%; text-align: center; margin-bottom: 20px;">
                            <?php echo $statusInfo[0]; ?>
                        </div>
                        
                        <form method="POST">
                            <div class="status-form" style="flex-wrap: wrap;">
                                <select name="status" class="status-select" style="width: 100%; margin-bottom: 10px;">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>? Ch? x? l�</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>?? �ang x? l�</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>?? �� g?i</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>? �� giao</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>? �� h?y</option>
                                </select>
                                <button type="submit" name="update_status" value="1" class="btn-update" style="width: 100%;">?? C?p nh?t</button>
                            </div>
                        </form>
                    </div>

                    <!-- Summary -->
                    <div class="section" style="margin-top: 30px;">
                        <div class="section-title">T�m t?t</div>
                        
                        <div class="info-grid">
                            <div>
                                <div class="info-label">S? lu?ng s?n ph?m</div>
                                <div class="info-value" style="font-size: 20px; margin-top: 5px;"><?php echo count($items); ?></div>
                            </div>
                            <div>
                                <div class="info-label">T?ng ti?n</div>
                                <div class="info-value" style="font-size: 20px; margin-top: 5px; color: #667eea;"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>?</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

