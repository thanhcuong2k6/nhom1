<?php
/**
 * ===================================
 * HƯỚNG DẪN SỬ DỤNG IMAGE HELPER
 * ===================================
 * 
 * Cấu trúc thư mục yêu cầu:
 * ├── public/
 * │   └── images/
 * │       ├── products/          (ảnh sản phẩm)
 * │       └── no-image.jpg       (ảnh mặc định)
 */

// ============================================
// CÁCH 1: HỎI URL ẢNH
// ============================================

// Lấy URL ảnh sản phẩm
$imageUrl = ImageHelper::getProductImage($product['image']);
// Nếu $product['image'] rỗng → trả về public/images/no-image.jpg
// Nếu file không tồn tại → trả về public/images/no-image.jpg
// Nếu file tồn tại → trả về public/images/products/[filename]

echo '<img src="' . $imageUrl . '" alt="' . $product['name'] . '">';

// ============================================
// CÁCH 2: HIỂN THỊ TRỰC TIẾP (cách này đơn giản hơn)
// ============================================

// Render img tag hoàn chỉnh
echo ImageHelper::renderProductImage(
    $product['image'],
    $product['name'],
    'product-image'
);

// HTML output:
// <img src="http://localhost/shopdo/public/images/products/product_123.jpg" 
//      alt="Áo Sơ Mi Nam" 
//      class="product-image" 
//      loading="lazy" />

// ============================================
// CÁCH 3: KIỂM TRA ẢNH CÓ TỒN TẠI KHÔNG
// ============================================

if (ImageHelper::imageExists($product['image'])) {
    echo "Ảnh sản phẩm tồn tại";
} else {
    echo "Ảnh sản phẩm không tồn tại, sẽ dùng ảnh mặc định";
}

// ============================================
// CÁCH 4: UPLOAD ẢNH
// ============================================

if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $result = ImageHelper::uploadProductImage($_FILES['product_image']);
    
    if ($result['success']) {
        // Cập nhật database
        $filename = $result['filename'];
        $product->update(['image' => $filename]);
        echo "Ảnh đã upload thành công: " . $filename;
    } else {
        echo "Lỗi: " . $result['error'];
    }
}

// ============================================
// THỰC TIỄN: SỬ DỤNG TRONG VIEW
// ============================================
?>

<!-- Ví dụ 1: Trong product list (trang chủ) -->
<?php foreach ($products as $product): ?>
    <div class="product-card">
        <div class="product-image">
            <?php echo ImageHelper::renderProductImage(
                $product['image'],
                $product['name'],
                'product-img'
            ); ?>
        </div>
        <div class="product-info">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
        </div>
    </div>
<?php endforeach; ?>

<!-- Ví dụ 2: Trong product detail -->
<div class="product-detail">
    <div class="product-main-image">
        <?php echo ImageHelper::renderProductImage(
            $product['image'],
            $product['name'],
            'main-image'
        ); ?>
    </div>
    <div class="product-content">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="price"><?php echo number_format($product['price']); ?> ₫</p>
    </div>
</div>

<!-- Ví dụ 3: Admin upload ảnh -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="product_image" accept="image/*">
    <button type="submit">Upload ảnh sản phẩm</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image'])) {
    $result = ImageHelper::uploadProductImage($_FILES['product_image']);
    if ($result['success']) {
        $productId = $_POST['product_id'];
        // Update database
        $db->query("UPDATE products SET image = ? WHERE id = ?", 
                  [$result['filename'], $productId]);
        echo "Thành công!";
    } else {
        echo "Lỗi: " . $result['error'];
    }
}
?>

<?php
/**
 * ===================================
 * PHƯƠNG THỨC AVAILABLE
 * ===================================
 * 
 * 1. getProductImage($image)
 *    - Lấy URL ảnh sản phẩm
 *    - Return: "public/images/products/[filename]" hoặc "public/images/no-image.jpg"
 * 
 * 2. renderProductImage($image, $alt, $class)
 *    - Hiển thị thẻ <img> hoàn chỉnh
 *    - Return: HTML string
 * 
 * 3. getNoImageUrl()
 *    - Lấy URL ảnh mặc định
 *    - Return: "public/images/no-image.jpg"
 * 
 * 4. imageExists($image)
 *    - Kiểm tra file ảnh có tồn tại không
 *    - Return: true/false
 * 
 * 5. uploadProductImage($file)
 *    - Upload ảnh sản phẩm (_FILES['image'])
 *    - Return: ['success' => bool, 'filename' => string, 'error' => string]
 * 
 * ===================================
 * LƯU Ý QUAN TRỌNG
 * ===================================
 * 
 * ✓ Tạo thư mục: public/images/products/
 * ✓ Tạo file: public/images/no-image.jpg (ảnh mặc định)
 * ✓ Đảm bảo quyền write cho thư mục public/images/products/
 * ✓ Database column: products.image (lưu tên file, VD: "product_123.jpg")
 */
?>
