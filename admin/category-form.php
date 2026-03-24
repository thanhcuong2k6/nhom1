<?php
/**
 * Admin Category Form - Bi?u m?u th�m/s?a danh m?c
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Danh m?c';
$isEdit = false;
$category = [
    'id' => '',
    'name' => '',
    'slug' => '',
    'description' => '',
    'image' => '',
    'active' => 1
];

$db = new Database();
$conn = $db->getConnection();

// N?u c� id th� l� ch? d? edit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categoryId = (int)$_GET['id'];
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $isEdit = true;
        $pageTitle = 'S?a danh m?c: ' . $category['name'];
    }
    $stmt->close();
}

// X? l� luu
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $slug = htmlspecialchars(trim($_POST['slug'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate
    if (empty($name)) {
        $errors[] = 'T�n danh m?c kh�ng du?c d? tr?ng';
    }
    
    if (empty($slug)) {
        $errors[] = 'Slug kh�ng du?c d? tr?ng';
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update
                $query = "UPDATE categories SET name = ?, slug = ?, description = ?, active = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $categoryId = $category['id'];
                $stmt->bind_param('sssii', $name, $slug, $description, $active, $categoryId);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'C?p nh?t danh m?c th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/categories.php');
                    exit;
                }
            } else {
                // Insert
                $query = "INSERT INTO categories (name, slug, description, active) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('sssi', $name, $slug, $description, $active);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'Th�m danh m?c th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/categories.php');
                    exit;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = 'L?i: ' . $e->getMessage();
        }
    }
    
    // Update form values n?u c� l?i
    $category = [
        'id' => $_GET['id'] ?? '',
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'active' => $active
    ];
}

// Helper d? t?o slug
if (!isset($_POST['slug']) && isset($_POST['name']) && empty($_POST['slug'])) {
    $category['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name']), '-'));
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php" class="active">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1><?php echo $isEdit ? '?? S?a danh m?c' : '? Th�m danh m?c'; ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="name">T�n danh m?c *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug (URL-friendly) *</label>
                        <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($category['slug'] ?? ''); ?>" required>
                        <div class="slug-info">V� d?: thoi-trang, dien-thoai, may-tinh</div>
                    </div>

                    <div class="form-group">
                        <label for="description">M� t?</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="active" name="active" <?php echo ($category['active'] ?? true) ? 'checked' : ''; ?>>
                            <label for="active" style="margin-bottom: 0;">K�ch ho?t danh m?c</label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">?? <?php echo $isEdit ? 'C?p nh?t' : 'Th�m'; ?></button>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php" class="btn btn-cancel">? H?y</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

