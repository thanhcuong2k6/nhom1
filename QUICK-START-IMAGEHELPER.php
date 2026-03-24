<?php
/**
 * QUICK START - ImageHelper cho FashionHub
 * 
 * Mục đích: Hiển thị ảnh sản phẩm từ public/images/products/{image}
 *          Fallback: public/images/no-image.jpg nếu ảnh không có
 */

/**
 * ============================================================================
 * BƯỚC 1: SETUP CẤU TRÚC THƯ MỤC
 * ============================================================================
 * 
 * Chạy file này trong terminal để tự động tạo:
 *   php setup-imagehelper.php
 * 
 * Hoặc thủ công tạo:
 *   - public/images/
 *   - public/images/products/       ← Upload ảnh sản phẩm vào đây
 *   - public/images/no-image.jpg    ← Ảnh placeholder (bắt buộc)
 */

/**
 * ============================================================================
 * BƯỚC 2: DATABASE - Cấu hình cột ảnh
 * ============================================================================
 * 
 * Cột: products.image (VARCHAR(255))
 * Lưu: Tên file chứ KHÔNG phải URL
 * 
 * Ví dụ:
 *   id | name     | image            | price
 *   1  | Áo nam   | product_001.jpg  | 150000
 *   2  | Quần nữ  | ao-nu-001.png    | 200000
 *   3  | Váy      | NULL             | 250000 ← Sẽ hiển thị no-image.jpg
 */

/**
 * ============================================================================
 * BƯỚC 3: CODE - Hiển thị ảnh trong View
 * ============================================================================
 */

// Ví dụ 1: Cách đơn giản nhất (Recommended)
// ──────────────────────────────────────────────────────────────────────────
?>

<!-- Trong file view, hiển thị ảnh sản phẩm: -->
<?php // example1_simple.php ?>

<?php 
// Ở đầu file:
require_once __DIR__ . '/helpers/ImageHelper.php';
?>

<div class="product-card">
    <div class="product-image">
        <!-- CÁCH NÀY TỐT NHẤT - Chỉ 1 dòng code -->
        <?php echo ImageHelper::renderProductImage(
            $product['image'],
            $product['name'],
            'product-thumb'
        ); ?>
    </div>
    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
    <p><?php echo number_format($product['price'], 0, ',', '.'); ?> ₫</p>
</div>

<?php // Output:
// <img src="/public/images/products/product_001.jpg" alt="Áo nam" class="product-thumb" loading="lazy" />
// HOẶC nếu ảnh không có:
// <img src="/public/images/no-image.jpg" alt="Áo nam" class="product-thumb" loading="lazy" />
?>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php // Ví dụ 2: Lấy URL để dùng trong JavaScript ?>

<?php // example2_url.php ?>

<?php
// Lấy URL ảnh
$imageUrl = ImageHelper::getProductImage($product['image']);
// Output: "/public/images/products/product_001.jpg" hoặc "/public/images/no-image.jpg"
?>

<div class="product-card" data-image="<?php echo htmlspecialchars($imageUrl); ?>">
    <!-- Dùng JavaScript hoặc CSS cho background -->
</div>

<?php // JavaScript có thể dùng như này:
// const image = document.querySelector('.product-card').dataset.image;
// img.src = image;
?>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php // Ví dụ 3: Kiểm tra ảnh có tồn tại không ?>

<?php // example3_check.php ?>

<?php
// Kiểm tra ảnh
if (ImageHelper::imageExists($product['image'])) {
    // Ảnh tồn tại
    $class = 'has-image';
} else {
    // Ảnh không tồn tại, sẽ dùng no-image.jpg
    $class = 'no-image-placeholder';
}
?>

<img src="<?php echo ImageHelper::getProductImage($product['image']); ?>" 
     class="<?php echo $class; ?>" />

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php // Ví dụ 4: Upload ảnh từ form ?>

<?php // example4_upload.php ?>

<?php
if ($_POST && isset($_FILES['product_image'])) {
    // Upload ảnh
    $result = ImageHelper::uploadProductImage($_FILES['product_image']);
    
    if ($result['success']) {
        // Success
        $filename = $result['filename']; // VD: "product_1234567890.jpg"
        
        // Cập nhật database
        // UPDATE products SET image = '$filename' WHERE id = ?
        
        echo "Upload thành công: " . htmlspecialchars($filename);
    } else {
        // Error
        echo "Lỗi: " . $result['error'];
        // Có thể là: "File type not allowed", "File too large", etc
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="product_image" accept=".jpg,.jpeg,.png,.gif,.webp" required />
    <button type="submit">Upload</button>
</form>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php
/**
 * ============================================================================
 * NHỮNG PHƯƠNG THỨC CÓ SẲN
 * ============================================================================
 * 
 * 1. getProductImage($image)
 *    - Trả về URL ảnh hoặc URL no-image.jpg
 *    - Dùng khi cần URL
 *    - Return: string (full URL path)
 * 
 * 2. renderProductImage($image, $alt, $class = '')
 *    - Trả về thẻ <img> hoàn chỉnh
 *    - RECOMMENDED - Dùng trong view
 *    - Có lazy loading
 *    - Return: string (HTML img tag)
 * 
 * 3. imageExists($image)
 *    - Kiểm tra ảnh có tồn tại không
 *    - Return: boolean
 * 
 * 4. uploadProductImage($file)
 *    - Upload ảnh từ $_FILES
 *    - Validate type (JPEG, PNG, GIF, WebP)
 *    - Validate size (max 5MB)
 *    - Return: array ['success' => true/false, 'filename' => '...', 'error' => '...']
 * 
 * 5. getNoImageUrl()
 *    - Trả về URL ảnh no-image.jpg
 *    - Return: string (URL)
 */
?>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php
/**
 * ============================================================================
 * COMMON ISSUES - KHẮC PHỤC VẤN ĐỀ
 * ============================================================================
 * 
 * ❌ Vấn đề: Ảnh không hiển thị
 * ✓ Giải pháp:
 *   - Kiểm tra public/images/products/ tồn tại không?
 *   - Kiểm tra public/images/no-image.jpg tồn tại không?
 *   - Kiểm tra APP_URL trong Config đúng không?
 *   - Dùng php setup-imagehelper.php để auto-setup
 * 
 * ❌ Vấn đề: Lỗi "File type not allowed" khi upload
 * ✓ Giải pháp:
 *   - Chỉ cho phép: JPEG, PNG, GIF, WebP
 *   - File size max 5MB
 *   - Kiểm tra trong ImageHelper::uploadProductImage()
 * 
 * ❌ Vấn đề: Hình ảnh bị... chậm loading
 * ✓ Giải pháp:
 *   - ImageHelper có lazy loading (loading="lazy")
 *   - Nén ảnh trước khi upload
 *   - Dùng WebP format cho size nhỏ hơn
 * 
 * ❌ Vấn đề: Config::get('APP_URL') không hoạt động
 * ✓ Giải pháp:
 *   - Thêm vào config/Config.php:
 *     Config::set('APP_URL', 'http://localhost');
 *   - Hoặc (nếu VirtualHost): Config::set('APP_URL', 'http://shopdo.local');
 */
?>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php
/**
 * ============================================================================
 * MIGRATION - Chuyển từ cách cũ sang cách mới
 * ============================================================================
 * 
 * CŨ (không tốt):
 * ───────────────────────────────────────────────────────────────────────
 * <img src="/uploads/<?php echo $product['image']; ?>" />
 * 
 * MỚI (tốt):
 * ───────────────────────────────────────────────────────────────────────
 * <?php echo ImageHelper::renderProductImage($product['image'], $product['name']); ?>
 * 
 * Lợi ích:
 * - Tự động fallback khi ảnh missing
 * - Lazy loading
 * - Đường dẫn chuẩn
 * - Dễ quản lý
 */
?>

<!-- ──────────────────────────────────────────────────────────────────────── -->

<?php
/**
 * ============================================================================
 * NEXT STEPS
 * ============================================================================
 * 
 * 1. Chạy setup:
 *    & php setup-imagehelper.php
 * 
 * 2. Upload ảnh vào:
 *    public/images/products/
 * 
 * 3. Cập nhật sản phẩm với tên file ảnh:
 *    UPDATE products SET image = 'product_001.jpg' WHERE id = 1
 * 
 * 4. Sửa view:
 *    <?php echo ImageHelper::renderProductImage($product['image'], $product['name']); ?>
 * 
 * 5. Test:
 *    - Xem example-image-helper.php để demo
 *    - Hoặc test trực tiếp trên trang danh sách sản phẩm
 * 
 * 6. Tài liệu chi tiết:
 *    - USAGE_IMAGE_HELPER.php (ví dụ + docs)
 *    - helpers/ImageHelper.php (source code + comments)
 */
?>

✅ Xong! Đây là hệ thống ảnh hoàn chỉnh cho FashionHub.
