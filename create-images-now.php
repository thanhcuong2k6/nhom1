<?php
/**
 * FashionHub - Tạo hình ảnh sản phẩm (Tự động)
 * Script này sẽ tự động tạo ảnh cho tất cả sản phẩm
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

$message = '';
$productDetails = [];
$success = false;

// Tạo thư mục uploads nếu chưa tồn tại
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Danh sách màu sắc gradient
    $colors = [
        ['667eea', '764ba2'],
        ['ff6b6b', 'e74c3c'],
        ['51cf66', '2ecc71'],
        ['ffd43b', 'f39c12'],
        ['1971c2', '3498db'],
        ['ff922b', 'e67e22'],
        ['a78bfa', '8b5cf6'],
        ['13b0f5', '0ea5e9']
    ];

    // Danh sách emoji thời trang
    $emojis = ['👔', '👗', '👖', '👠', '🧥', '👜', '🧤', '👒', '🎽', '👙'];

    // Lấy danh sách sản phẩm
    $result = $conn->query("SELECT id, name FROM products ORDER BY id");
    $productsCreated = 0;

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productId = $row['id'];
            $productName = $row['name'];
            
            // Chọn màu sắc và emoji dựa vào ID sản phẩm
            $colorPair = $colors[($productId - 1) % count($colors)];
            $color1 = $colorPair[0];
            $color2 = $colorPair[1];
            $emoji = $emojis[($productId - 1) % count($emojis)];
            
            // Cắt tên sản phẩm
            $shortName = substr($productName, 0, 20);
            
            // Tạo SVG
            $svg = <<<SVG
<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300">
    <defs>
        <linearGradient id="grad$productId" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#$color1;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#$color2;stop-opacity:1" />
        </linearGradient>
        <filter id="shadow">
            <feDropShadow dx="2" dy="2" stdDeviation="3" flood-opacity="0.3"/>
        </filter>
    </defs>
    
    <!-- Background -->
    <rect width="300" height="300" fill="url(#grad$productId)"/>
    
    <!-- Pattern -->
    <circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="250" r="50" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="50" r="30" fill="rgba(0,0,0,0.1)"/>
    
    <!-- Icon/Badge -->
    <g transform="translate(150, 100)">
        <circle cx="0" cy="0" r="45" fill="rgba(255,255,255,0.2)" filter="url(#shadow)"/>
        <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="36" fill="white">
            $emoji
        </text>
    </g>
    
    <!-- Product Name -->
    <text x="150" y="200" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="20" fill="white" font-weight="bold">
        $shortName
    </text>
    
    <!-- Bottom Text -->
    <text x="150" y="260" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="12" fill="rgba(255,255,255,0.8)">
        FashionHub - Thời trang chất lượng
    </text>
    
    <!-- Product ID Badge -->
    <rect x="20" y="20" width="40" height="30" rx="5" fill="rgba(255,255,255,0.9)"/>
    <text x="40" y="40" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="14" fill="#$color1" font-weight="bold">
        #$productId
    </text>
</svg>
SVG;
            
            // Lưu file SVG
            $filename = 'product_' . $productId . '.svg';
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $svg)) {
                // Cập nhật database
                $updateQuery = "UPDATE products SET image = '$filename' WHERE id = $productId";
                if ($conn->query($updateQuery)) {
                    $productsCreated++;
                    $productDetails[] = [
                        'id' => $productId,
                        'name' => $productName,
                        'emoji' => $emoji,
                        'image' => $filename,
                        'status' => 'success'
                    ];
                } else {
                    $productDetails[] = [
                        'id' => $productId,
                        'name' => $productName,
                        'emoji' => $emoji,
                        'image' => $filename,
                        'status' => 'db_error'
                    ];
                }
            } else {
                $productDetails[] = [
                    'id' => $productId,
                    'name' => $productName,
                    'emoji' => $emoji,
                    'image' => $filename,
                    'status' => 'file_error'
                ];
            }
        }
    }

    $success = ($productsCreated > 0);
    $message = $productsCreated > 0 ? "✓ Đã tạo thành công $productsCreated ảnh sản phẩm!" : "Chưa tạo được ảnh nào";

} catch (Exception $e) {
    $success = false;
    $message = 'Lỗi: ' . $e->getMessage();
    $productDetails = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo hình ảnh sản phẩm - FashionHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .header { 
            text-align: center; 
            color: white; 
            margin-bottom: 30px;
            padding-top: 20px;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { font-size: 16px; opacity: 0.9; }
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 30px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .status-message { 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 18px; 
            font-weight: 500;
            text-align: center;
        }
        .status-success { 
            background: #d4edda; 
            color: #155724; 
            border: 2px solid #28a745;
        }
        .status-error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 2px solid #f5c6cb;
        }
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            background: #f9f9f9;
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        .product-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        .product-emoji { font-size: 48px; margin: 10px 0; }
        .product-name { 
            font-weight: bold; 
            color: #333; 
            margin: 10px 0;
            font-size: 14px;
        }
        .product-id { 
            color: #999; 
            font-size: 12px;
            margin: 5px 0;
        }
        .product-status {
            font-size: 12px;
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
        }
        .status-ok {
            background: #d4edda;
            color: #155724;
        }
        .status-fail {
            background: #f8d7da;
            color: #721c24;
        }
        .button-group { 
            text-align: center; 
            margin-top: 30px;
        }
        .btn { 
            padding: 12px 30px; 
            margin: 10px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home { 
            background: #667eea; 
            color: white; 
        }
        .btn-home:hover { 
            background: #5568d3;
        }
        .btn-reload { 
            background: #28a745; 
            color: white; 
        }
        .btn-reload:hover { 
            background: #218838;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box strong { display: block; margin-bottom: 10px; color: #004085; }
        .info-box p { color: #0a3d62; font-size: 14px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👔 FashionHub</h1>
            <p>Tạo hình ảnh sản phẩm thời trang</p>
        </div>

        <div class="card">
            <div class="status-message <?php echo $success ? 'status-success' : 'status-error'; ?>">
                <?php echo $message; ?>
            </div>

            <?php if ($success): ?>
                <div class="info-box">
                    <strong>✓ Hoàn thành!</strong>
                    <p><?php echo count($productDetails); ?> hình ảnh sản phẩm đã được tạo và lưu vào cơ sở dữ liệu. Hình ảnh sẽ hiển thị trên trang chủ và trang sản phẩm.</p>
                </div>

                <h3 style="margin: 20px 0 15px 0; color: #333;">📦 Sản phẩm đã tạo ảnh:</h3>
                <div class="products-grid">
                    <?php foreach ($productDetails as $product): ?>
                        <div class="product-card">
                            <div class="product-emoji"><?php echo $product['emoji']; ?></div>
                            <div class="product-id">#<?php echo $product['id']; ?></div>
                            <div class="product-name"><?php echo htmlspecialchars(substr($product['name'], 0, 25)); ?></div>
                            <div class="product-status <?php echo $product['status'] === 'success' ? 'status-ok' : 'status-fail'; ?>">
                                <?php echo $product['status'] === 'success' ? '✓ Thành công' : '✗ Lỗi'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="info-box">
                    <strong>⚠️ Có lỗi xảy ra</strong>
                    <p>Vui lòng kiểm tra kết nối cơ sở dữ liệu và thử lại.</p>
                </div>
            <?php endif; ?>

            <div class="button-group">
                <a href="index.php" class="btn btn-home">← Về trang chủ</a>
                <a href="create-images-now.php" class="btn btn-reload">🔄 Tạo lại ảnh</a>
            </div>
        </div>
    </div>
</body>
</html>
