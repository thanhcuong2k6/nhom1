<?php
/**
 * Admin Categories Management - Qu?n l� danh m?c
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Qu?n l� danh m?c';

$db = new Database();
$conn = $db->getConnection();

// X? l� x�a
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryId = (int)$_GET['delete'];
    
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $categoryId);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'X�a danh m?c th�nh c�ng!';
    } else {
        $_SESSION['error_message'] = 'L?i khi x�a danh m?c!';
    }
    $stmt->close();
    
    header('Location: ' . Config::get('APP_URL') . 'admin/categories.php');
    exit;
}

// L?y danh s�ch danh m?c
$query = "SELECT id, name, slug, description, image, active, created_at FROM categories ORDER BY created_at DESC";
$result = $conn->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php" class="active">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>?? Qu?n l� danh m?c</h1>
                <a href="<?php echo Config::get('APP_URL'); ?>admin/category-form.php" class="btn-primary">+ Th�m danh m?c</a>
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
                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <p>?? Chua c� danh m?c n�o</p>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/category-form.php" class="btn-primary">Th�m danh m?c d?u ti�n</a>
                    </div>
                <?php else: ?>
                    <div class="categories-grid">
                        <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <div class="category-image">??</div>
                            <div class="category-content">
                                <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                                <div class="category-slug">/ <?php echo htmlspecialchars($category['slug']); ?></div>
                                <div class="category-description"><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)); ?></div>
                                <div class="category-footer">
                                    <a href="<?php echo Config::get('APP_URL'); ?>admin/category-form.php?id=<?php echo $category['id']; ?>" class="btn-small btn-edit">?? S?a</a>
                                    <a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php?delete=<?php echo $category['id']; ?>" class="btn-small btn-delete" onclick="return confirm('B?n ch?c ch?n?')">??? X�a</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

