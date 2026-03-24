<?php
/**
 * Admin Vouchers Management - Qu?n l� m� gi?m gi�
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Qu?n l� voucher';

$db = new Database();
$conn = $db->getConnection();

// X? l� x�a voucher
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $voucherId = (int)$_GET['delete'];
    
    $query = "DELETE FROM vouchers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $voucherId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'X�a voucher th�nh c�ng!';
    } else {
        $_SESSION['error_message'] = 'L?i khi x�a voucher!';
    }
    $stmt->close();
    
    header('Location: ' . Config::get('APP_URL') . 'admin/vouchers.php');
    exit;
}

// L?y danh s�ch voucher
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// L?y t?ng s? voucher
$countQuery = "SELECT COUNT(*) as total FROM vouchers";
$countResult = $conn->query($countQuery);
$totalVouchers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalVouchers / $perPage);

// L?y danh s�ch voucher
$query = "SELECT id, code, description, discount_type, discount_value, min_amount, max_uses, used_count, start_date, end_date, active
          FROM vouchers
          ORDER BY created_at DESC
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
$vouchers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// H�m ki?m tra tr?ng th�i voucher
function getVoucherStatus($voucher) {
    $now = new DateTime();
    $startDate = new DateTime($voucher['start_date']);
    $endDate = new DateTime($voucher['end_date']);
    
    if ($now < $startDate) {
        return ['S?p di?n ra', '#FCD34D'];
    } elseif ($now > $endDate) {
        return ['H?t hi?u l?c', '#F87171'];
    } elseif ($voucher['max_uses'] && $voucher['used_count'] >= $voucher['max_uses']) {
        return ['H?t lu?ng', '#F87171'];
    } elseif (!$voucher['active']) {
        return ['�� v� hi?u', '#999'];
    } else {
        return ['�ang ho?t d?ng', '#6EE7B7'];
    }
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php">?? B�o c�o</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/users.php">?? Ngu?i d�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php" class="active">??? Voucher</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>??? Qu?n l� voucher</h1>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/voucher-form.php" class="btn-primary">+ Th�m voucher</a>
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
                <?php if (empty($vouchers)): ?>
                    <div class="empty-state">
                        <p>?? Chua c� voucher n�o</p>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/voucher-form.php" class="btn-primary">Th�m voucher d?u ti�n</a>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>M� voucher</th>
                                <th>M� t?</th>
                                <th>Gi?m gi�</th>
                                <th>�on h�ng t?i thi?u</th>
                                <th>Lu?ng s? d?ng</th>
                                <th>Th?i h?n</th>
                                <th>Tr?ng th�i</th>
                                <th>Thao t�c</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vouchers as $voucher): ?>
                            <?php 
                                $statusInfo = getVoucherStatus($voucher);
                                $statusLabel = $statusInfo[0];
                                $statusColor = $statusInfo[1];
                            ?>
                            <tr>
                                <td><span class="code"><?php echo htmlspecialchars($voucher['code']); ?></span></td>
                                <td><?php echo htmlspecialchars(substr($voucher['description'] ?? '', 0, 30)); ?></td>
                                <td>
                                    <?php if ($voucher['discount_type'] === 'percentage'): ?>
                                        <strong><?php echo $voucher['discount_value']; ?>%</strong>
                                    <?php else: ?>
                                        <strong><?php echo number_format($voucher['discount_value'], 0, ',', '.'); ?>?</strong>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($voucher['min_amount'], 0, ',', '.'); ?>?</td>
                                <td>
                                    <div class="usage-info">
                                        <?php echo $voucher['used_count']; ?>
                                        <?php if ($voucher['max_uses']): ?>
                                            / <?php echo $voucher['max_uses']; ?>
                                        <?php else: ?>
                                            / 8
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="usage-info">
                                        <?php echo date('d/m/Y', strtotime($voucher['start_date'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($voucher['end_date'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $statusColor; ?>;">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo Config::get('APP_URL'); ?>admin/voucher-form.php?id=<?php echo $voucher['id']; ?>" class="btn-small btn-edit">?? S?a</a>
                                        <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?delete=<?php echo $voucher['id']; ?>" class="btn-small btn-delete" onclick="return confirm('B?n ch?c ch?n?')">??? X�a</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?page=1">� �?u</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?page=<?php echo $page - 1; ?>">� Tru?c</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?page=<?php echo $page + 1; ?>">Sau �</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php?page=<?php echo $totalPages; ?>">Cu?i �</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

