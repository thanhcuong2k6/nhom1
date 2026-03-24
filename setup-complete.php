<?php
/**
 * FashionHub - Setup Website Bán Quần Áo Hoàn Chỉnh
 * Script này xóa sản phẩm cũ, thêm 10 sản phẩm quần áo và tạo ảnh
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

$message = '';
$details = [];
$success = false;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // === BƯỚC 1: CLEAR DỮ LIỆU CŨ ===
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    $conn->query("DELETE FROM order_items");
    $conn->query("DELETE FROM orders");
    $conn->query("DELETE FROM reviews");
    $conn->query("DELETE FROM payments");
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM categories");
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $details[] = '✓ Xóa sản phẩm điện tử cũ';
    
    // === BƯỚC 2: THÊM DANH MỤC ===
    $categories_data = [
        ['Áo nam', 'ao-nam', 'Áo sơ mi, áo thun, áo polo cho nam'],
        ['Áo nữ', 'ao-nu', 'Áo sơ mi, áo phông, áo liền cho nữ'],
        ['Quần nam', 'quan-nam', 'Quần tây, quần jean, quần short cho nam'],
        ['Quần nữ', 'quan-nu', 'Quần jean, quần léo, quần short cho nữ'],
        ['Váy đầm', 'vay-dam', 'Váy chữ A, đầm midi, đầm dạ hội']
    ];
    
    foreach ($categories_data as $cat) {
        $sql = "INSERT INTO categories (name, slug, description) 
                VALUES ('{$cat[0]}', '{$cat[1]}', '{$cat[2]}')";
        $conn->query($sql);
    }
    
    $details[] = '✓ Thêm 5 danh mục quần áo';
    
    // === BƯỚC 3: THÊM 10 SẢN PHẨM ===
    $products_data = [
        [
            'name' => 'Áo Sơ Mi Nam Oxford Xanh',
            'price' => 299000,
            'description' => 'Áo sơ mi nam chất liệu Oxford cao cấp, thoáng mát, phù hợp công sở',
            'category_id' => 1,
            'stock' => 50,
            'emoji' => '👔'
        ],
        [
            'name' => 'Áo Thun Nam Tay Lỡ',
            'price' => 149000,
            'description' => 'Áo thun nam basic chất lượng cao, vải 100% cotton, dễ phối đồ',
            'category_id' => 1,
            'stock' => 80,
            'emoji' => '👔'
        ],
        [
            'name' => 'Áo Phông Nữ Trắng',
            'price' => 129000,
            'description' => 'Áo phông nữ trắng tay ngắn, vải mượt mềm, kiểu dáng basic dễ mix',
            'category_id' => 2,
            'stock' => 60,
            'emoji' => '👗'
        ],
        [
            'name' => 'Áo Sơ Mi Nữ Hồng Pastel',
            'price' => 349000,
            'description' => 'Áo sơ mi nữ màu hồng pastel dịu dàng, kiểu dáng thanh lịch',
            'category_id' => 2,
            'stock' => 40,
            'emoji' => '👗'
        ],
        [
            'name' => 'Quần Jean Nam Xanh Đậm',
            'price' => 449000,
            'description' => 'Quần jean nam chất liệu cao cấp, màu xanh đậm sang trọng, duỗi vừa vặn',
            'category_id' => 3,
            'stock' => 45,
            'emoji' => '👖'
        ],
        [
            'name' => 'Quần Tây Nam Đen Công Sở',
            'price' => 599000,
            'description' => 'Quần tây nam màu đen chất liệu polyester, thích hợp công sở',
            'category_id' => 3,
            'stock' => 35,
            'emoji' => '👖'
        ],
        [
            'name' => 'Quần Jean Nữ Xanh Nhạt',
            'price' => 389000,
            'description' => 'Quần jean nữ xanh nhạt, kiểu dáng skinny ôm vừa vặn, tôn dáng',
            'category_id' => 4,
            'stock' => 55,
            'emoji' => '👖'
        ],
        [
            'name' => 'Quần Short Đùi Nữ Đen',
            'price' => 199000,
            'description' => 'Quần short đùi nữ màu đen, chất vải cotton thoáng mát, thích hợp hè',
            'category_id' => 4,
            'stock' => 70,
            'emoji' => '👖'
        ],
        [
            'name' => 'Váy Chữ A Nữ Xám',
            'price' => 599000,
            'description' => 'Váy chữ A nữ màu xám cổ điển, phù hợp công sở, dạo phố',
            'category_id' => 5,
            'stock' => 40,
            'emoji' => '👗'
        ],
        [
            'name' => 'Đầm Midi Nữ Họa Tiết Hoa',
            'price' => 699000,
            'description' => 'Đầm midi nữ họa tiết hoa xinh xắn, dáng xòe thanh lịch, tôn dáng',
            'category_id' => 5,
            'stock' => 35,
            'emoji' => '👗'
        ]
    ];
    
    // Tạo thư mục uploads nếu chưa có
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    $colors = [
        ['667eea', '764ba2'],
        ['ff6b6b', 'e74c3c'],
        ['51cf66', '2ecc71'],
        ['ffd43b', 'f39c12'],
        ['1971c2', '3498db'],
        ['ff922b', 'e67e22'],
        ['a78bfa', '8b5cf6'],
        ['13b0f5', '0ea5e9'],
        ['ec4899', 'd946a6'],
        ['06b6d4', '0891b2']
    ];
    
    $productCount = 0;
    $imageCount = 0;
    
    foreach ($products_data as $index => $p) {
        // Thêm sản phẩm vào database
        $slug = strtolower(str_replace(' ', '-', $p['name']));
        $sql = "INSERT INTO products (name, slug, description, price, category_id, stock, active) 
                VALUES ('{$p['name']}', '{$slug}', '{$p['description']}', {$p['price']}, {$p['category_id']}, {$p['stock']}, 1)";
        
        if ($conn->query($sql)) {
            $productId = $conn->lastInsertId();
            $productCount++;
            
            // Tạo SVG ảnh
            $colorPair = $colors[$index];
            $color1 = $colorPair[0];
            $color2 = $colorPair[1];
            $emoji = $p['emoji'];
            $shortName = substr($p['name'], 0, 16);
            
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
    
    <!-- Decorative circles -->
    <circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="250" r="50" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="50" r="30" fill="rgba(0,0,0,0.1)"/>
    
    <!-- Main emoji icon -->
    <g transform="translate(150, 100)">
        <circle cx="0" cy="0" r="50" fill="rgba(255,255,255,0.2)" filter="url(#shadow)"/>
        <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="44" fill="white">
            $emoji
        </text>
    </g>
    
    <!-- Product name -->
    <text x="150" y="208" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="16" fill="white" font-weight="bold">
        $shortName
    </text>
    
    <!-- Footer text -->
    <text x="150" y="265" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="10" fill="rgba(255,255,255,0.9)">
        FashionHub - Thời trang online
    </text>
</svg>
SVG;
            
            $filename = 'product_' . $productId . '.svg';
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $svg)) {
                // Cập nhật database với tên file ảnh
                $conn->query("UPDATE products SET image = '$filename' WHERE id = $productId");
                $imageCount++;
            }
        }
    }
    
    $details[] = "✓ Thêm $productCount sản phẩm quần áo";
    $details[] = "✓ Tạo $imageCount ảnh sản phẩm";
    
    $success = true;
    $message = "✅ SETUP THÀNH CÔNG!";

} catch (Exception $e) {
    $success = false;
    $message = "❌ LỖI: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup FashionHub - Website Bán Quần Áo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { max-width: 900px; width: 100%; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 60px 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .icon {
            font-size: 120px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #999;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #28a745;
            background: #d4edda;
            color: #155724;
        }
        .details {
            background: #f8f9fa;
            border-left: 5px solid #667eea;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .detail-item {
            padding: 12px 0;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-item:last-child { border-bottom: none; }
        .products-section {
            margin: 40px 0;
        }
        .products-title {
            color: #333;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .product-item {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 1px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            transition: all 0.3s;
        }
        .product-item:hover {
            background: linear-gradient(135deg, #667eea25 0%, #764ba225 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .emoji { font-size: 20px; margin-right: 10px; }
        .info-box {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            font-size: 14px;
            color: #856404;
            line-height: 1.6;
        }
        .buttons {
            text-align: center;
            margin-top: 40px;
        }
        .btn {
            display: inline-block;
            padding: 14px 40px;
            margin: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="icon"><?php echo $success ? '👔' : '⚠️'; ?></div>
                <h1><?php echo $message; ?></h1>
                <p class="subtitle">Website bán quần áo - FashionHub</p>
            </div>
            
            <div class="message">
                <?php if ($success): ?>
                    Website bán quần áo đã được setup thành công!
                <?php else: ?>
                    Có lỗi xảy ra, vui lòng kiểm tra lại
                <?php endif; ?>
            </div>
            
            <div class="details">
                <?php foreach ($details as $detail): ?>
                    <div class="detail-item">✓ <?php echo $detail; ?></div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($success): ?>
                <div class="products-section">
                    <div class="products-title">📦 10 Sản Phẩm Quần Áo Được Thêm:</div>
                    <div class="products-grid">
                        <div class="product-item"><span class="emoji">👔</span> Áo Sơ Mi Nam Oxford - 299k</div>
                        <div class="product-item"><span class="emoji">👔</span> Áo Thun Nam Tay Lỡ - 149k</div>
                        <div class="product-item"><span class="emoji">👗</span> Áo Phông Nữ Trắng - 129k</div>
                        <div class="product-item"><span class="emoji">👗</span> Áo Sơ Mi Nữ Hồng - 349k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Jean Nam Xanh - 449k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Tây Nam Đen - 599k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Jean Nữ Xanh - 389k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Short Nữ Đen - 199k</div>
                        <div class="product-item"><span class="emoji">👗</span> Váy Chữ A Nữ Xám - 599k</div>
                        <div class="product-item"><span class="emoji">👗</span> Đầm Midi Nữ Hoa - 699k</div>
                    </div>
                </div>
                
                <div class="info-box">
                    <strong>ℹ️ Thông tin quan trọng:</strong><br>
                    • Tất cả ảnh sản phẩm được lưu trong thư mục <code>/uploads/</code><br>
                    • Nếu không có ảnh, sẽ hiển thị ảnh mặc định<br>
                    • Sản phẩm đã được thêm vào database (products) và danh mục (categories)<br>
                    • Cấu trúc database không thay đổi
                </div>
            <?php endif; ?>
            
            <div class="buttons">
                <a href="index.php" class="btn btn-primary">👉 Xem Trang Chủ</a>
                <a href="pages/products.php" class="btn btn-secondary">📦 Xem Tất Cả Sản Phẩm</a>
            </div>
        </div>
    </div>
</body>
</html>
