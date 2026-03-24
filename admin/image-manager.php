<?php
/**
 * FashionHub - Admin - Upload ?nh s?n ph?m
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Product.php';

// Ki?m tra admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . Config::get('APP_URL') . '/auth/login.php');
    exit;
}

$message = '';
$messageType = '';

// X? l� upload ?nh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        
        // T?o thu m?c n?u chua t?n t?i
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $_FILES['image'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedExt)) {
            $message = 'Ch? ch?p nh?n ?nh d?nh d?ng: jpg, jpeg, png, gif';
            $messageType = 'error';
        } elseif ($file['size'] > 5242880) { // 5MB
            $message = '?nh qu� l?n (t?i da 5MB)';
            $messageType = 'error';
        } else {
            $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $message = 'Upload ?nh th�nh c�ng: ' . $filename;
                $messageType = 'success';
                
                // C?p nh?t s?n ph?m n?u c� product_id
                if (isset($_POST['product_id'])) {
                    $productId = (int)$_POST['product_id'];
                    $productModel = new Product();
                    $productModel->update($productId, ['image' => $filename]);
                }
            } else {
                $message = 'L?i upload ?nh';
                $messageType = 'error';
            }
        }
    }
}

// L?y danh s�ch s?n ph?m
$productModel = new Product();
$products = $productModel->getAll(100);

// L?y danh s�ch ?nh d� upload
$uploadDir = __DIR__ . '/../uploads/';
$images = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
            $images[] = $file;
        }
    }
}
sort($images);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qu?n l� ?nh s?n ph?m - Admin FashionHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>admin/admin-style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>??? Qu?n l� ?nh s?n ph?m</h1>
            <a href="<?php echo Config::get('APP_URL'); ?>">? Quay l?i trang ch?</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="tabs">
                <div class="tab active" onclick="switchTab(event, 'upload')">?? Upload ?nh</div>
                <div class="tab" onclick="switchTab(event, 'images')">??? ?nh d� upload (<?php echo count($images); ?>)</div>
                <div class="tab" onclick="switchTab(event, 'products')">?? G�n ?nh cho s?n ph?m</div>
            </div>

            <!-- Upload Tab -->
            <div id="upload" class="tab-content active">
                <h2>Upload ?nh m?i</h2>
                
                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <div class="form-group">
                        <label for="image">Ch?n ?nh:</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>

                    <div class="form-group">
                        <label for="product_id">G�n cho s?n ph?m (T�y ch?n):</label>
                        <select id="product_id" name="product_id">
                            <option value="">-- Ch?n s?n ph?m --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn">
                        <i class="fas fa-upload"></i> Upload ?nh
                    </button>
                </form>

                <div style="margin-top: 20px; padding: 15px; background: #e7f5ff; border-left: 4px solid #1971c2; border-radius: 4px; color: #1971c2;">
                    <strong>?? Th�ng tin:</strong><br>
                    � �?nh d?ng h? tr?: JPG, JPEG, PNG, GIF<br>
                    � K�ch thu?c t?i da: 5MB<br>
                    � ?nh s? du?c luu v�o thu m?c: uploads/
                </div>
            </div>

            <!-- Images Tab -->
            <div id="images" class="tab-content">
                <h2>?nh d� upload</h2>

                <?php if (!empty($images)): ?>
                    <div class="images-grid">
                        <?php foreach ($images as $image): ?>
                            <div class="image-card">
                                <img src="<?php echo Config::get('APP_URL'); ?>/uploads/<?php echo $image; ?>" alt="<?php echo $image; ?>">
                                <div class="image-card-info">
                                    <div style="word-break: break-all; margin-bottom: 8px;">
                                        <strong>File:</strong> <?php echo $image; ?>
                                    </div>
                                    <div>
                                        <strong>URL:</strong><br>
                                        <code style="font-size: 11px; background: #f0f0f0; padding: 3px; border-radius: 2px; display: block; word-break: break-all;">
                                            /uploads/<?php echo $image; ?>
                                        </code>
                                    </div>
                                </div>
                                <div class="image-card-actions">
                                    <button class="copy-btn" onclick="copyToClipboard('/uploads/<?php echo $image; ?>')">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 30px;">Chua c� ?nh n�o du?c upload</p>
                <?php endif; ?>
            </div>

            <!-- Products Tab -->
            <div id="products" class="tab-content">
                <h2>Danh s�ch s?n ph?m</h2>

                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>?nh</th>
                            <th>T�n s?n ph?m</th>
                            <th>Gi�</th>
                            <th>Kho</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="<?php 
                                        echo !empty($product['image']) 
                                            ? Config::get('APP_URL') . '/uploads/' . $product['image']
                                            : 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2250%22><rect fill=%22%23f0f0f0%22 width=%2250%22 height=%2250%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%228%22>NO IMG</text></svg>';
                                    ?>" alt="">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.'); ?> d</td>
                                <td><?php echo $product['stock']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(e, tabName) {
            // ?n t?t c? tab
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            // B? active tr�n t?t c? tab
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Hi?n tab du?c ch?n
            document.getElementById(tabName).classList.add('active');
            e.target.classList.add('active');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('�� sao ch�p: ' + text);
            });
        }
    </script>
</body>
</html>

