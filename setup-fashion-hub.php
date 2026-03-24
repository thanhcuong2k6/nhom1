<?php
/**
 * FashionHub - Chuyển đổi dự án thành website bán quần áo
 * Script này thực hiện toàn bộ quá trình chuyển đổi
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

$message = '';
$details = [];
$success = false;
$products_created = [];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // === BƯỚC 1: XÓA TẤT CẢ DỮ LIỆU CŨ ===
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    $conn->query("DELETE FROM order_items");
    $conn->query("DELETE FROM orders");
    $conn->query("DELETE FROM reviews");
    $conn->query("DELETE FROM payments");
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM categories");
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $details[] = [
        'icon' => '✓',
        'text' => 'Đã xóa toàn bộ sản phẩm điện tử cũ (iPhone, Samsung, iPad, MacBook)'
    ];
    
    // === BƯỚC 2: THÊM DANH MỤC QUẦN ÁO ===
    $categories = [
        ['Áo nam', 'ao-nam', 'Áo sơ mi, áo thun, áo polo cho nam'],
        ['Áo nữ', 'ao-nu', 'Áo sơ mi, áo phông, áo dây cho nữ'],
        ['Quần nam', 'quan-nam', 'Quần tây, quần jean cho nam'],
        ['Quần nữ', 'quan-nu', 'Quần jean, quần short cho nữ'],
        ['Váy đầm', 'vay-dam', 'Váy chữ A, đầm midi, đầm dạ hội']
    ];
    
    foreach ($categories as $cat) {
        $sql = "INSERT INTO categories (name, slug, description) VALUES ('{$cat[0]}', '{$cat[1]}', '{$cat[2]}')";
        $conn->query($sql);
    }
    
    $details[] = [
        'icon' => '✓',
        'text' => 'Đã thêm 5 danh mục quần áo (Áo nam, Áo nữ, Quần nam, Quần nữ, Váy đầm)'
    ];
    
    // === BƯỚC 3: THÊM SẢN PHẨM THỜI TRANG ===
    $products = [
        [
            'name' => 'Áo Sơ Mi Nam Oxford Xanh',
            'slug' => 'ao-so-mi-nam-oxford-xanh',
            'description' => 'Áo sơ mi nam chất liệu Oxford cao cấp, thoáng mát, phù hợp công sở',
            'price' => 299000,
            'category_id' => 1,
            'stock' => 50
        ],
        [
            'name' => 'Áo Thun Nam Tay Lỡ',
            'slug' => 'ao-thun-nam-tay-lo',
            'description' => 'Áo thun nam basic chất lượng cao, vải 100% cotton, dễ phối đồ',
            'price' => 149000,
            'category_id' => 1,
            'stock' => 80
        ],
        [
            'name' => 'Áo Phông Nữ Trắng Tay Ngắn',
            'slug' => 'ao-phong-nu-trang-tay-ngan',
            'description' => 'Áo phông nữ trắng tay ngắn, vải mượt mềm, kiểu dáng basic',
            'price' => 129000,
            'category_id' => 2,
            'stock' => 60
        ],
        [
            'name' => 'Quần Jean Nam Xanh Đậm',
            'slug' => 'quan-jean-nam-xanh-dam',
            'description' => 'Quần jean nam chất liệu cao cấp, màu xanh đậm sang trọng, duỗi vừa vặn',
            'price' => 449000,
            'category_id' => 3,
            'stock' => 45
        ],
        [
            'name' => 'Quần Jean Nữ Xanh Nhạt',
            'slug' => 'quan-jean-nu-xanh-nhat',
            'description' => 'Quần jean nữ xanh nhạt, kiểu dáng skinny ôm vừa vặn, tôn dáng',
            'price' => 389000,
            'category_id' => 4,
            'stock' => 55
        ],
        [
            'name' => 'Quần Short Đùi Nữ Đen',
            'slug' => 'quan-short-dui-nu-den',
            'description' => 'Quần short đùi nữ màu đen, chất vải cotton thoáng mát, thích hợp hè',
            'price' => 199000,
            'category_id' => 4,
            'stock' => 70
        ],
        [
            'name' => 'Váy Chữ A Nữ Xám Cổ Điển',
            'slug' => 'vay-chu-a-nu-xam-co-dien',
            'description' => 'Váy chữ A nữ màu xám cổ điển, phù hợp công sở, dạo phố',
            'price' => 599000,
            'category_id' => 5,
            'stock' => 40
        ],
        [
            'name' => 'Đầm Midi Nữ Họa Tiết Hoa',
            'slug' => 'dam-midi-nu-hoa-tiet-hoa',
            'description' => 'Đầm midi nữ họa tiết hoa xinh xắn, dáng xòe thanh lịch, tôn dáng',
            'price' => 699000,
            'category_id' => 5,
            'stock' => 35
        ]
    ];
    
    foreach ($products as $p) {
        $sql = "INSERT INTO products (name, slug, description, price, category_id, stock, active) 
                VALUES ('{$p['name']}', '{$p['slug']}', '{$p['description']}', {$p['price']}, {$p['category_id']}, {$p['stock']}, 1)";
        if ($conn->query($sql)) {
            $products_created[] = $p['name'];
        }
    }
    
    $details[] = [
        'icon' => '✓',
        'text' => 'Đã thêm 8 sản phẩm thời trang mới'
    ];
    
    // === BƯỚC 4: TẠO THƯ MỤC UPLOADS ===
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    // === BƯỚC 5: TẠO ẢNH CHO SẢN PHẨM ===
    $colors = [
        ['667eea', '764ba2'],  // Purple
        ['ff6b6b', 'e74c3c'],  // Red
        ['51cf66', '2ecc71'],  // Green
        ['ffd43b', 'f39c12'],  // Yellow
        ['1971c2', '3498db'],  // Blue
        ['ff922b', 'e67e22'],  // Orange
        ['a78bfa', '8b5cf6'],  // Violet
        ['13b0f5', '0ea5e9']   // Cyan
    ];
    
    $emojis = ['👔', '👗', '👖', '👠', '🧤', '👜', '👒', '🎽'];
    
    $result = $conn->query("SELECT id, name FROM products ORDER BY id");
    $imagesCreated = 0;
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productId = $row['id'];
            $productName = $row['name'];
            
            $colorPair = $colors[($productId - 1) % count($colors)];
            $color1 = $colorPair[0];
            $color2 = $colorPair[1];
            $emoji = $emojis[($productId - 1) % count($emojis)];
            $shortName = substr($productName, 0, 18);
            
            $svg = <<<SVG
<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300">
    <defs>
        <linearGradient id="grad$productId" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#$color1;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#$color2;stop-opacity:1" />
        </linearGradient>
        <filter id="shadow$productId">
            <feDropShadow dx="2" dy="2" stdDeviation="3" flood-opacity="0.3"/>
        </filter>
    </defs>
    
    <rect width="300" height="300" fill="url(#grad$productId)"/>
    
    <circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="250" r="50" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="50" r="30" fill="rgba(0,0,0,0.1)"/>
    
    <g transform="translate(150, 100)">
        <circle cx="0" cy="0" r="45" fill="rgba(255,255,255,0.2)" filter="url(#shadow$productId)"/>
        <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="40" fill="white">
            $emoji
        </text>
    </g>
    
    <text x="150" y="200" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="16" fill="white" font-weight="bold">
        $shortName
    </text>
    
    <text x="150" y="260" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="10" fill="rgba(255,255,255,0.9)">
        FashionHub - Thời trang online
    </text>
</svg>
SVG;
            
            $filename = 'product_' . $productId . '.svg';
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $svg)) {
                $conn->query("UPDATE products SET image = '$filename' WHERE id = $productId");
                $imagesCreated++;
            }
        }
    }
    
    $details[] = [
        'icon' => '✓',
        'text' => 'Đã tạo ' . $imagesCreated . ' ảnh sản phẩm với biểu tượng thời trang'
    ];
    
    $success = true;
    $message = "✓ CHUYỂN ĐỔI THÀNH CÔNG!";
    
} catch (Exception $e) {
    $success = false;
    $message = "✗ LỖI: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyển đổi website - FashionHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { max-width: 800px; width: 100%; }
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 50px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .icon { 
            font-size: 100px; 
            text-align: center; 
            margin-bottom: 30px;
        }
        h1 { 
            text-align: center; 
            color: #333; 
            margin: 20px 0 10px 0;
            font-size: 32px;
        }
        .subtitle {
            text-align: center;
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .message { 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #28a745;
            background: #d4edda;
            color: #155724;
            text-align: center;
        }
        .details {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .detail-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-icon {
            font-size: 24px;
            font-weight: bold;
        }
        .products-list {
            background: #f0f7ff;
            border: 2px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .products-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .product-item {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            font-size: 13px;
            color: #333;
        }
        .emoji { 
            font-size: 18px; 
            margin-right: 8px;
        }
        .button-group { 
            text-align: center; 
            margin-top: 40px;
        }
        .btn { 
            display: inline-block;
            background: #667eea; 
            color: white; 
            padding: 14px 50px; 
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
            margin: 0 10px;
        }
        .btn:hover { 
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #495057;
        }
        .btn-secondary:hover {
            background: #3c4147;
        }
        .summary {
            background: #fff8e1;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">
                <?php echo $success ? '👔' : '✗'; ?>
            </div>
            
            <h1><?php echo $message; ?></h1>
            <div class="subtitle">Dự án đã được chuyển đổi thành website bán quần áo</div>
            
            <?php if ($success): ?>
                <div class="details">
                    <?php foreach ($details as $detail): ?>
                        <div class="detail-item">
                            <span class="detail-icon"><?php echo $detail['icon']; ?></span>
                            <span><?php echo $detail['text']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="products-list">
                    <div class="products-title">📦 8 Sản phẩm quần áo đã được thêm:</div>
                    <div class="product-grid">
                        <div class="product-item"><span class="emoji">👔</span> Áo Sơ Mi Nam - 299k</div>
                        <div class="product-item"><span class="emoji">👔</span> Áo Thun Nam - 149k</div>
                        <div class="product-item"><span class="emoji">👗</span> Áo Phông Nữ - 129k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Jean Nam - 449k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Jean Nữ - 389k</div>
                        <div class="product-item"><span class="emoji">👖</span> Quần Short Nữ - 199k</div>
                        <div class="product-item"><span class="emoji">👗</span> Váy Chữ A Nữ - 599k</div>
                        <div class="product-item"><span class="emoji">👗</span> Đầm Midi Nữ - 699k</div>
                    </div>
                </div>
                
                <div class="summary">
                    <strong>📋 Tóm tắt:</strong><br>
                    • Đã xóa: 5 sản phẩm điện tử cũ (iPhone, Samsung, iPad, MacBook, AirPods)<br>
                    • Đã thêm: 5 danh mục quần áo mới<br>
                    • Đã thêm: 8 sản phẩm thời trang mới<br>
                    • Đã tạo: 8 ảnh sản phẩm với biểu tượng thời trang
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; border: 2px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <strong>✗ Lỗi:</strong> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="button-group">
                <a href="index.php" class="btn">👉 Xem trang chủ</a>
                <a href="pages/products.php" class="btn btn-secondary">📦 Xem tất cả sản phẩm</a>
            </div>
        </div>
    </div>
</body>
</html>
