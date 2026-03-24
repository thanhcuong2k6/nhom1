<?php
/**
 * FashionHub - Tự động tạo ảnh sản phẩm thời trang
 * Chỉ cần truy cập URL này một lần để tạo ảnh cho tất cả sản phẩm quần áo
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Tạo thư mục uploads nếu chưa tồn tại
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Danh sách màu sắc gradient
    $colors = [
        ['#667eea', '#764ba2'],
        ['#ff6b6b', '#e74c3c'],
        ['#51cf66', '#2ecc71'],
        ['#ffd43b', '#f39c12'],
        ['#1971c2', '#3498db'],
        ['#ff922b', '#e67e22'],
        ['#a78bfa', '#8b5cf6'],
        ['#13b0f5', '#0ea5e9']
    ];

    // Lấy danh sách sản phẩm
    $result = $conn->query("SELECT id, name FROM products ORDER BY id");
    $productsCreated = 0;
    $productDetails = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productId = $row['id'];
            $productName = $row['name'];
            
            // Chọn màu sắc dựa vào ID sản phẩm
            $colorPair = $colors[($productId - 1) % count($colors)];
            $color1 = $colorPair[0];
            $color2 = $colorPair[1];
            
            // Tạo SVG
            $svg = generateProductImage($productId, $productName, $color1, $color2);
            
            // Lưu file SVG
            $filename = 'product_' . $productId . '.svg';
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $svg)) {
                // Cập nhật database
                $conn->query("UPDATE products SET image = '$filename' WHERE id = $productId");
                
                $productsCreated++;
                $productDetails[] = [
                    'id' => $productId,
                    'name' => $productName,
                    'image' => $filename,
                    'status' => '✓'
                ];
            }
        }
    }

    $success = true;
    $message = "Đã tạo thành công $productsCreated ảnh sản phẩm!";

} catch (Exception $e) {
    $success = false;
    $message = 'Lỗi: ' . $e->getMessage();
    $productDetails = [];
}

// Hàm tạo SVG ảnh sản phẩm thời trang
function generateProductImage($productId, $productName, $color1, $color2) {
    // Xóa # từ màu nếu có
    $color1 = str_replace('#', '', $color1);
    $color2 = str_replace('#', '', $color2);
    
    // Cắt tên sản phẩm
    $shortName = substr($productName, 0, 20);
    
    // Chọn emoji dựa vào sản phẩm
    $emojis = ['👔', '👗', '👖', '👠', '🧥', '👜', '🧤', '👒', '🎽', '👙'];
    $emoji = $emojis[($productId - 1) % count($emojis)];
    
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

    return $svg;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo ảnh sản phẩm - FashionHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .card-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .card-body {
            padding: 40px;
        }

        .message {
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .message.success {
            background: #d3f9d8;
            color: #2f9e44;
            border-left: 4px solid #51cf66;
        }

        .message.error {
            background: #ffe0e0;
            color: #c92a2a;
            border-left: 4px solid #ff7373;
        }

        .message-icon {
            font-size: 24px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table thead {
            background: #f5f5f5;
        }

        .products-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        .products-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .products-table tr:hover {
            background: #f9f9f9;
        }

        .status-ok {
            color: #51cf66;
            font-weight: bold;
            font-size: 18px;
        }

        .product-image-preview {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .info-box {
            background: #e7f5ff;
            border-left: 4px solid #1971c2;
            padding: 20px;
            border-radius: 8px;
            color: #1971c2;
            margin-top: 30px;
        }

        .info-box h3 {
            margin-bottom: 10px;
            color: #1971c2;
        }

        .info-box ul {
            margin-left: 20px;
        }

        .info-box li {
            margin-bottom: 8px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 14px;
            opacity: 0.9;
        }

        .no-products {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 20px;
            }

            .card-header h1 {
                font-size: 24px;
            }

            .card-body {
                padding: 20px;
            }

            .products-table {
                font-size: 14px;
            }

            .products-table th,
            .products-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>🎨 Tạo ảnh sản phẩm thời trang</h1>
                <p>Tự động tạo ảnh đẹp cho tất cả sản phẩm quần áo</p>
            </div>

            <div class="card-body">
                <?php if ($success): ?>
                    <div class="message success">
                        <div class="message-icon">✓</div>
                        <div><?php echo htmlspecialchars($message); ?></div>
                    </div>

                    <?php if ($productsCreated > 0): ?>
                        <div class="stats">
                            <div class="stat-card">
                                <h3><?php echo $productsCreated; ?></h3>
                                <p>Ảnh đã được tạo</p>
                            </div>
                        </div>

                        <h3 style="margin-bottom: 15px; color: #333;">Danh sách ảnh đã tạo:</h3>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Trạng thái</th>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>File ảnh</th>
                                    <th>Xem trước</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productDetails as $detail): ?>
                                <tr>
                                    <td><span class="status-ok"><?php echo $detail['status']; ?></span></td>
                                    <td><strong><?php echo $detail['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($detail['name']); ?></td>
                                    <td><code style="font-size: 12px; background: #f5f5f5; padding: 5px; border-radius: 3px;"><?php echo $detail['image']; ?></code></td>
                                    <td>
                                        <img src="<?php echo Config::get('APP_URL'); ?>/uploads/<?php echo $detail['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($detail['name']); ?>" 
                                             class="product-image-preview">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="info-box">
                            <h3>✓ Thông tin</h3>
                            <ul>
                                <li>Ảnh đã được lưu vào thư mục <code>uploads/</code></li>
                                <li>Database đã được cập nhật với tên file ảnh</li>
                                <li>Ảnh được tạo dưới định dạng SVG (nhẹ & đẹp)</li>
                                <li>Mỗi sản phẩm quần áo có biểu tượng riêng (👔, 👗, 👖, 👠, v.v.)</li>
                                <li>Mỗi sản phẩm có màu sắc gradient độc đáo để dễ phân biệt</li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="no-products">
                            <p>Không có sản phẩm nào để tạo ảnh</p>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="message error">
                        <div class="message-icon">✗</div>
                        <div><?php echo htmlspecialchars($message); ?></div>
                    </div>
                <?php endif; ?>

                <div class="button-group">
                    <a href="<?php echo Config::get('APP_URL'); ?>" class="btn btn-primary">
                        ← Quay lại trang chủ
                    </a>
                    <a href="<?php echo Config::get('APP_URL'); ?>/pages/products.php" class="btn btn-secondary">
                        Xem danh sách sản phẩm →
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
