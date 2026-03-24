<?php
/**
 * Admin Users Management - Qu?n l� ngu?i d�ng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Qu?n l� ngu?i d�ng';

$db = new Database();
$conn = $db->getConnection();

// X? l� x�a ngu?i d�ng
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    
    // Kh�ng du?c x�a admin
    $checkQuery = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user && $user['role'] === 'admin') {
        $_SESSION['error_message'] = 'Kh�ng th? x�a t�i kho?n Admin!';
    } else {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'X�a ngu?i d�ng th�nh c�ng!';
        } else {
            $_SESSION['error_message'] = 'L?i khi x�a ngu?i d�ng!';
        }
        $stmt->close();
    }
    
    header('Location: ' . Config::get('APP_URL') . 'admin/users.php');
    exit;
}

// X? l� k�ch ho?t/v� hi?u h�a ngu?i d�ng
if (isset($_POST['toggle_active'])) {
    $userId = (int)$_POST['user_id'];
    $active = isset($_POST['active']) ? 1 : 0;
    
    $query = "UPDATE users SET active = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $active, $userId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'C?p nh?t tr?ng th�i th�nh c�ng!';
    }
    $stmt->close();
    
    header('Location: ' . Config::get('APP_URL') . 'admin/users.php');
    exit;
}

// L?y danh s�ch ngu?i d�ng
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// L?y t?ng s? ngu?i d�ng
$countQuery = "SELECT COUNT(*) as total FROM users";
$countResult = $conn->query($countQuery);
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $perPage);

// L?y danh s�ch ngu?i d�ng
$query = "SELECT id, name, email, phone, role, active, created_at FROM users 
          ORDER BY created_at DESC
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/users.php" class="active">?? Ngu?i d�ng</a></li>
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
                <h1>?? Qu?n l� ngu?i d�ng</h1>
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
                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <p>?? Kh�ng c� ngu?i d�ng n�o</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>T�n ngu?i d�ng</th>
                                <th>Email</th>
                                <th>�i?n tho?i</th>
                                <th>Vai tr�</th>
                                <th>Tr?ng th�i</th>
                                <th>Ng�y t?o</th>
                                <th>Thao t�c</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-customer'; ?>">
                                        <?php echo $user['role'] === 'admin' ? '?? Admin' : '?? Kh�ch'; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="toggle_active" value="1">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="active" <?php echo $user['active'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </form>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo Config::get('APP_URL'); ?>admin/user-form.php?id=<?php echo $user['id']; ?>" class="btn-small btn-edit">?? S?a</a>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?delete=<?php echo $user['id']; ?>" class="btn-small btn-delete" onclick="return confirm('B?n ch?c ch?n mu?n x�a?')">??? X�a</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?page=1">� �?u</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?page=<?php echo $page - 1; ?>">� Tru?c</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?page=<?php echo $page + 1; ?>">Sau �</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php?page=<?php echo $totalPages; ?>">Cu?i �</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

