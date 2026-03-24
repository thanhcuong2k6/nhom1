<?php
/**
 * Admin Products Management - Qu?n l� s?n ph?m
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$admin = Auth::getCurrentUser();
$pageTitle = 'Qu?n l� s?n ph?m';

$db = new Database();
$conn = $db->getConnection();

// X? l� x�a
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $productId = (int)$_GET['delete'];
    
    // X�a file ?nh n?u c�
    $query = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product && !empty($product['image'])) {
        $filePath = __DIR__ . '/../uploads/' . $product['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // X�a s?n ph?m
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'X�a s?n ph?m th�nh c�ng!';
    } else {
        $_SESSION['error_message'] = 'L?i khi x�a s?n ph?m!';
    }
    $stmt->close();
    
    header('Location: ' . Config::get('APP_URL') . 'admin/products.php');
    exit;
}

// L?y danh s�ch s?n ph?m
$query = "SELECT p.id, p.name, p.price, p.stock, p.active, p.created_at, c.name as category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          ORDER BY p.created_at DESC";
          
$result = $conn->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php" class="active">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>?? Qu?n l� s?n ph?m</h1>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/product-form.php" class="btn-primary">+ Th�m s?n ph?m</a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    ? <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    ? <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <p>?? Chua c� s?n ph?m n�o</p>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/product-form.php" class="btn-primary">Th�m s?n ph?m d?u ti�n</a>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>T�n s?n ph?m</th>
                                    <th>Danh m?c</th>
                                    <th>Gi�</th>
                                    <th>Stock</th>
                                    <th>Tr?ng th�i</th>
                                    <th>Ng�y t?o</th>
                                    <th>H�nh d?ng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($product['price'], 0, ',', '.'); ?> d</td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $product['active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $product['active'] ? '? Ho?t d?ng' : '? T?m d?ng'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?php echo Config::get('APP_URL'); ?>admin/product-form.php?id=<?php echo $product['id']; ?>" class="btn-edit">?? S?a</a>
                                            <a href="<?php echo Config::get('APP_URL'); ?>admin/products.php?delete=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('B?n ch?c ch?n mu?n x�a?')">??? X�a</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

