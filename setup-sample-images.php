<?php
/**
 * FashionHub - Tạo mẫu ảnh sản phẩm
 */

require_once __DIR__ . '/config/Config.php';

// Tạo ảnh mẫu bằng SVG
function createSampleImage($productName, $productId) {
    $uploadDir = __DIR__ . '/uploads/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Tạo SVG ảnh
    $colors = ['667eea', 'ff6b6b', '51cf66', 'ffd43b', '1971c2', 'ff922b', 'a78bfa', '13b0f5'];
    $color = $colors[$productId % count($colors)];
    
    $svg = <<<SVG
    <svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="grad$productId" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#$color;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#667eea;stop-opacity:1" />
            </linearGradient>
        </defs>
        <rect width="300" height="300" fill="url(#grad$productId)"/>
        <text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="24" fill="white" font-weight="bold">
            FashionHub
        </text>
        <text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="14" fill="rgba(255,255,255,0.8)">
            Sản phẩm mẫu
        </text>
    </svg>
    SVG;

    $filename = 'sample_product_' . $productId . '.svg';
    file_put_contents($uploadDir . $filename, $svg);
    
    return $filename;
}

// Lấy các sản phẩm và tạo ảnh mẫu
require_once __DIR__ . '/config/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $result = $conn->query("SELECT id, name FROM products LIMIT 10");
    
    if ($result) {
        $created = 0;
        while ($row = $result->fetch_assoc()) {
            $filename = createSampleImage($row['name'], $row['id']);
            
            // Cập nhật product với ảnh nếu chưa có
            $conn->query("UPDATE products SET image = '$filename' WHERE id = {$row['id']} AND (image IS NULL OR image = '')");
            
            $created++;
        }
        
        echo "<div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #d3f9d8; border-left: 4px solid #51cf66; border-radius: 4px; color: #2f9e44;'>";
        echo "<h3>✓ Đã tạo ảnh mẫu thành công!</h3>";
        echo "<p>Tổng cộng: $created ảnh mẫu đã được tạo</p>";
        echo "<p><a href='" . Config::get('APP_URL') . "' style='color: #1971c2; text-decoration: underline;'>← Quay lại trang chủ</a></p>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #ffe0e0; border-left: 4px solid #ff7373; border-radius: 4px; color: #c92a2a;'>";
    echo "<h3>✗ Có lỗi xảy ra!</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo ảnh mẫu - FashionHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
    </style>
</head>
<body>
</body>
</html>
