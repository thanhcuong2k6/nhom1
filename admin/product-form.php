<?php
/**
 * Admin Product Form - Bi?u m?u th�m/s?a s?n ph?m
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'S?n ph?m';
$isEdit = false;
$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'cost' => '',
    'stock' => '',
    'category_id' => '',
    'image' => '',
    'active' => 1
];

$db = new Database();
$conn = $db->getConnection();

// N?u c� id th� l� ch? d? edit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $isEdit = true;
        $pageTitle = 'S?a s?n ph?m: ' . $product['name'];
    }
    $stmt->close();
}

// L?y danh s�ch danh m?c
$catQuery = "SELECT id, name FROM categories WHERE active = 1 ORDER BY name";
$catResult = $conn->query($catQuery);
$categories = $catResult->fetch_all(MYSQLI_ASSOC);

// X? l� luu
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate
    if (empty($name)) {
        $errors[] = 'T�n s?n ph?m kh�ng du?c d? tr?ng';
    }
    
    if ($price <= 0) {
        $errors[] = 'Gi� s?n ph?m ph?i l?n hon 0';
    }
    
    if ($stock < 0) {
        $errors[] = 'T?n kho kh�ng th? �m';
    }
    
    // X? l� upload ?nh
    $imageFile = $product['image'] ?? null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = __DIR__ . '/../uploads/';
        
        // T?o thu m?c n?u chua c�
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Ch? ch?p nh?n c�c file ?nh (JPG, PNG, GIF, WebP)';
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = 'K�ch thu?c ?nh kh�ng du?c vu?t qu� 5MB';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = $uploadDir . $uniqueName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // X�a ?nh cu n?u c�
                if ($isEdit && $imageFile && file_exists($uploadDir . $imageFile)) {
                    unlink($uploadDir . $imageFile);
                }
                $imageFile = $uniqueName;
            } else {
                $errors[] = 'L?i khi upload ?nh';
            }
        }
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update
                $query = "UPDATE products SET name = ?, description = ?, price = ?, cost = ?, stock = ?, category_id = ?, image = ?, active = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $productId = $product['id'];
                $stmt->bind_param('ssddiiisi', $name, $description, $price, $cost, $stock, $categoryId, $imageFile, $active, $productId);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'C?p nh?t s?n ph?m th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/products.php');
                    exit;
                }
            } else {
                // Insert
                $query = "INSERT INTO products (name, description, price, cost, stock, category_id, image, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ssddiiis', $name, $description, $price, $cost, $stock, $categoryId, $imageFile, $active);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'Th�m s?n ph?m th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/products.php');
                    exit;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = 'L?i: ' . $e->getMessage();
        }
    }
    
    // Update form values n?u c� l?i
    $product = [
        'id' => $_GET['id'] ?? '',
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'cost' => $cost,
        'stock' => $stock,
        'category_id' => $categoryId,
        'image' => $imageFile,
        'active' => $active
    ];
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
                <h1><?php echo $isEdit ? '?? S?a s?n ph?m' : '? Th�m s?n ph?m'; ?></h1>
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
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">T�n s?n ph?m *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Danh m?c</label>
                            <select id="category_id" name="category_id">
                                <option value="">-- Ch?n danh m?c --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] ?? null) == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="description">M� t? s?n ph?m</label>
                            <textarea id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Gi� b�n (?) *</label>
                            <input type="number" id="price" name="price" step="1000" value="<?php echo $product['price'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cost">Gi� v?n (?)</label>
                            <input type="number" id="cost" name="cost" step="1000" value="<?php echo $product['cost'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">T?n kho</label>
                            <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock'] ?? 0; ?>">
                        </div>

                        <div class="form-group">
                            <label for="active" style="margin-bottom: 0;">Tr?ng th�i</label>
                            <div class="checkbox-group" style="margin-top: 15px;">
                                <input type="checkbox" id="active" name="active" <?php echo ($product['active'] ?? true) ? 'checked' : ''; ?>>
                                <label for="active" style="margin-bottom: 0;">K�ch ho?t s?n ph?m</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label>?nh s?n ph?m</label>
                            <div class="image-upload" onclick="document.getElementById('image').click();">
                                <label class="image-upload-label">
                                    ?? Ch?n ?nh ho?c k�o th? v�o d�y
                                </label>
                                <input type="file" id="image" name="image" accept="image/*">
                            </div>
                            <div class="info-text">JPG, PNG, GIF, WebP - T?i da 5MB</div>
                            
                            <?php if (!empty($product['image']) && $product['image'] !== ''): ?>
                                <div class="image-preview">
                                    <div class="current-image">?nh hi?n t?i:</div>
                                    <img src="<?php echo Config::get('APP_URL'); ?>uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">?? <?php echo $isEdit ? 'C?p nh?t' : 'Th�m'; ?></button>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/products.php" class="btn btn-cancel">? H?y</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Drag and drop for image upload
        const imageUpload = document.querySelector('.image-upload');
        const fileInput = document.getElementById('image');

        imageUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUpload.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
            imageUpload.style.borderColor = '#667eea';
        });

        imageUpload.addEventListener('dragleave', () => {
            imageUpload.style.backgroundColor = '';
            imageUpload.style.borderColor = '#ddd';
        });

        imageUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileInput.files = e.dataTransfer.files;
            imageUpload.style.backgroundColor = '';
            imageUpload.style.borderColor = '#ddd';
        });
    </script>
</body>
</html>

