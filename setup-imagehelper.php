<?php
/**
 * Setup script - Tạo cấu trúc thư mục cho ImageHelper
 * Tạo các thư mục và file cần thiết để dùng ImageHelper
 */

$setup_steps = [];
$errors = [];

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  ImageHelper Setup - Hệ thống ảnh sản phẩm FashionHub     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// 1. Tạo thư mục public/images/products
$products_dir = __DIR__ . '/public/images/products';
if (!is_dir($products_dir)) {
    if (mkdir($products_dir, 0755, true)) {
        $setup_steps[] = "✓ Tạo thư mục: {$products_dir}";
    } else {
        $errors[] = "✗ Lỗi tạo thư mục: {$products_dir}";
    }
} else {
    $setup_steps[] = "✓ Thư mục tồn tại: {$products_dir}";
}

// 2. Kiểm tra và tạo ảnh no-image.jpg nếu chưa có
$no_image_path = __DIR__ . '/public/images/no-image.jpg';
if (!file_exists($no_image_path)) {
    // Tạo ảnh placeholder 250x250px bằng GD Library
    if (extension_loaded('gd')) {
        $image = imagecreatetruecolor(250, 250);
        $bg_color = imagecolorallocate($image, 200, 200, 200);
        $text_color = imagecolorallocate($image, 100, 100, 100);
        
        // Fill background
        imagefill($image, 0, 0, $bg_color);
        
        // Add text
        $font = __DIR__ . '/public/images/no-image.jpg'; // Fallback font
        imagestring($image, 5, 90, 110, "NO IMAGE", $text_color);
        imagestring($image, 2, 80, 130, "250 x 250", $text_color);
        
        // Save image
        if (imagejpeg($image, $no_image_path, 90)) {
            $setup_steps[] = "✓ Tạo ảnh mặc định: {$no_image_path}";
        } else {
            $errors[] = "✗ Lỗi tạo ảnh: {$no_image_path}";
        }
        imagedestroy($image);
    } else {
        $errors[] = "⚠ GD Library chưa cài -> Thủ công tạo no-image.jpg (250x250px)";
    }
} else {
    $setup_steps[] = "✓ Ảnh mặc định tồn tại: {$no_image_path}";
}

// 3. Kiểm tra ImageHelper.php
$image_helper = __DIR__ . '/helpers/ImageHelper.php';
if (file_exists($image_helper)) {
    $setup_steps[] = "✓ ImageHelper.php tồn tại";
} else {
    $errors[] = "✗ ImageHelper.php không tìm thấy: {$image_helper}";
}

// 4. Kiểm tra Config.php
$config_path = __DIR__ . '/config/Config.php';
if (file_exists($config_path)) {
    $setup_steps[] = "✓ Config.php tồn tại";
    
    // Đọc Config để check APP_URL
    $config_content = file_get_contents($config_path);
    if (strpos($config_content, 'APP_URL') !== false) {
        $setup_steps[] = "✓ APP_URL đã định nghĩa trong Config";
    } else {
        $errors[] = "⚠ APP_URL chưa định nghĩa -> Cần thêm Config::set('APP_URL', 'http://localhost')";
    }
} else {
    $errors[] = "✗ Config.php không tìm thấy: {$config_path}";
}

// 5. Hiển thị kết quả
echo "📋 BƯỚC SETUP:\n";
echo "─────────────────────────────────────────────────────────\n";
foreach ($setup_steps as $step) {
    echo "{$step}\n";
}

if (!empty($errors)) {
    echo "\n⚠️  CẢN HỨ LỰI/CẢNH BÁO:\n";
    echo "─────────────────────────────────────────────────────────\n";
    foreach ($errors as $error) {
        echo "{$error}\n";
    }
}

echo "\n✅ CẤU HÌNH THÀNH CÔNG!\n";
echo "─────────────────────────────────────────────────────────\n";
echo "\n📝 Các bước tiếp theo:\n";
echo "1. Upload ảnh sản phẩm vào: public/images/products/\n";
echo "   Đặt tên: product_123.jpg, ao-nam.png, etc\n\n";
echo "2. Cập nhật products table với tên file:\n";
echo "   UPDATE products SET image = 'product_123.jpg' WHERE id = 123\n\n";
echo "3. Dùng trong View:\n";
echo "   <?php echo ImageHelper::renderProductImage(\$product['image'], \$product['name']); ?>\n\n";
echo "4. Test bằng: example-image-helper.php\n";

echo "\n📚 Tài liệu:\n";
echo "   - USAGE_IMAGE_HELPER.php (3+ ví dụ)\n";
echo "   - helpers/ImageHelper.php (source code)\n";

if (PHP_SAPI === 'cli') {
    exit(0); // CLI success
}
?>

<style>
body { font-family: 'Courier New', monospace; line-height: 1.6; color: #333; padding: 20px; background: #f5f5f5; }
pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
.success { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.warning { color: #ffc107; font-weight: bold; }
</style>
