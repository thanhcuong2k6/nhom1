<?php
/**
 * FashionHub - Sửa lỗi sản phẩm và ảnh
 * Script này sẽ xóa tất cả và tạo lại sản phẩm quần áo với ảnh chính xác
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
    
    // === BƯỚC 1: XÓA TẤT CẢ DỮ LIỆU CŨ ===
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    $conn->query("DELETE FROM order_items");
    $conn->query("DELETE FROM orders");
    $conn->query("DELETE FROM reviews");
    $conn->query("DELETE FROM payments");
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM categories");
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    $details[] = "✓ Đã xóa dữ liệu cũ";
    
    // === BƯỚC 2: THÊM DANH MỤC ===
    $categories_sql = "INSERT INTO categories (name, slug, description) VALUES 
        ('Áo nam', 'ao-nam', 'Áo sơ mi, áo thun, áo polo và các loại áo nam khác'),
        ('Áo nữ', 'ao-nu', 'Áo sơ mi, áo phông, áo dây và các loại áo nữ đa dạng'),
        ('Quần nam', 'quan-nam', 'Quần tây, quần jean, quần kaki cho nam'),
        ('Quần nữ', 'quan-nu', 'Quần jean, quần dài, quần short cho nữ'),
        ('Váy công sở', 'vay-cong-so', 'Váy chữ A, váy suông, váy midi công sở'),
        ('Đầm dạ hội', 'dam-da-hoi', 'Đầm lịch lãm, đầm dự tiệc, đầm kỹ niệm'),
        ('Giày', 'giay', 'Giày sneaker, giày cao gót, giày đang đôi'),
        ('Phụ kiện', 'phu-kien', 'Túi xách, ví, dây lưng, nón, khăn quàng'),
        ('Đồ lót & tất', 'do-lot-tat', 'Áo lót, quần lót, tất chân'),
        ('Áo khoác & cardigan', 'ao-khoac', 'Áo khoác, blazer, cardigan ấm áp')";
    
    if (!$conn->query($categories_sql)) {
        throw new Exception("Lỗi thêm danh mục: " . $conn->error);
    }
    $details[] = "✓ Đã thêm 10 danh mục quần áo";
    
    // === BƯỚC 3: THÊM SẢN PHẨM ===
    $products = [
        ['Áo Sơ Mi Nam Oxford Xanh', 'ao-so-mi-nam-oxford-xanh', 'Áo sơ mi nam chất liệu Oxford cao cấp, thoáng mát, phù hợp công sở', 299000, 1],
        ['Áo Thun Nam Logo Tay Lỡ', 'ao-thun-nam-logo-tay-lo', 'Áo thun nam basic với logo in độc đáo, vải 100% cotton', 149000, 1],
        ['Áo Phông Nữ Trắng Tay Ngắn', 'ao-phong-nu-trang-tay-ngan', 'Áo phông nữ trắng tay ngắn, vải mượt mềm, dễ phối đồ', 129000, 2],
        ['Áo Sơ Mi Nữ Hồng Pastel', 'ao-so-mi-nu-hong-pastel', 'Áo sơ mi nữ màu hồng pastel dịu dàng, kiểu dáng thanh lịch', 349000, 2],
        ['Quần Jean Nam Xanh Đậm', 'quan-jean-nam-xanh-dam', 'Quần jean nam chất liệu cao cấp, màu xanh đậm sang trọng', 449000, 3],
        ['Quần Tây Nam Đen Công Sở', 'quan-tay-nam-den-cong-so', 'Quần tây nam màu đen, thích hợp cho công sở và sự kiện', 599000, 3],
        ['Quần Jean Nữ Xanh Nhạt', 'quan-jean-nu-xanh-nhat', 'Quần jean nữ xanh nhạt, kiểu dáng skinny ôm vừa vặn', 389000, 4],
        ['Quần Short Đùi Nữ Đen', 'quan-short-dui-nu-den', 'Quần short đùi nữ màu đen, chất vải cotton thoáng mát', 199000, 4],
        ['Váy Chữ A Nữ Xám Cổ Điển', 'vay-chu-a-nu-xam-co-dien', 'Váy chữ A nữ màu xám cổ điển, phù hợp công sở', 599000, 5],
        ['Đầm Midi Nữ Họa Tiết Hoa', 'dam-midi-nu-hoa-tiet-hoa', 'Đầm midi nữ họa tiết hoa, dáng xòe thanh lịch', 699000, 6]
    ];
    
    foreach ($products as $p) {
        $sql = "INSERT INTO products (name, slug, description, price, category_id, stock, active) 
                VALUES ('{$p[0]}', '{$p[1]}', '{$p[2]}', {$p[3]}, {$p[4]}, 50, 1)";
        if (!$conn->query($sql)) {
            throw new Exception("Lỗi thêm sản phẩm: " . $conn->error);
        }
    }
    $details[] = "✓ Đã thêm 10 sản phẩm quần áo";
    
    // === BƯỚC 4: TẠO THƯMỤC UPLOADS ===
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    // === BƯỚC 5: TẠO ẢNH CHO SẢN PHẨM ===
    $colors = [
        ['667eea', '764ba2'],
        ['ff6b6b', 'e74c3c'],
        ['51cf66', '2ecc71'],
        ['ffd43b', 'f39c12'],
        ['1971c2', '3498db'],
        ['ff922b', 'e67e22'],
        ['a78bfa', '8b5cf6'],
        ['13b0f5', '0ea5e9'],
        ['f56565', 'e53e3e'],
        ['ed8936', 'd69e2e']
    ];
    
    $emojis = ['👔', '👗', '👖', '👠', '🧥', '👜', '🧤', '👒', '🎽', '👙'];
    
    $result = $conn->query("SELECT id, name FROM products ORDER BY id");
    $imagesCreated = 0;
    $imageFailed = 0;
    
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
              font-family="Arial, sans-serif" font-size="36" fill="white">
            $emoji
        </text>
    </g>
    
    <text x="150" y="200" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="18" fill="white" font-weight="bold">
        $shortName
    </text>
    
    <text x="150" y="260" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="11" fill="rgba(255,255,255,0.8)">
        FashionHub - Thời trang onl
    </text>
    
    <rect x="20" y="20" width="40" height="30" rx="5" fill="rgba(255,255,255,0.9)"/>
    <text x="40" y="40" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="14" fill="#$color1" font-weight="bold">
        #$productId
    </text>
</svg>
SVG;
            
            $filename = 'product_' . $productId . '.svg';
            $filepath = $uploadDir . $filename;
            
            if (file_put_contents($filepath, $svg)) {
                $conn->query("UPDATE products SET image = '$filename' WHERE id = $productId");
                $imagesCreated++;
            } else {
                $imageFailed++;
            }
        }
    }
    
    $details[] = "✓ Đã tạo $imagesCreated ảnh sản phẩm";
    if ($imageFailed > 0) {
        $details[] = "⚠ $imageFailed ảnh không tạo được (kiểm tra quyền thư mục)";
    }
    
    $success = true;
    $message = "✓ SỬA LỖI THÀNH CÔNG!";
    
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
    <title>Sửa lỗi - FashionHub</title>
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
        .container { max-width: 700px; width: 100%; }
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .icon { 
            font-size: 80px; 
            text-align: center; 
            margin-bottom: 20px;
        }
        h1 { 
            text-align: center; 
            color: #333; 
            margin: 20px 0;
            font-size: 28px;
        }
        .message { 
            padding: 15px 20px; 
            border-radius: 8px; 
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #28a745;
            background: #d4edda;
            color: #155724;
            text-align: center;
        }
        .details {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .detail-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 15px;
            color: #333;
        }
        .detail-item:last-child { border-bottom: none; }
        .products-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .product-card {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            font-size: 13px;
        }
        .emoji { font-size: 18px; margin-right: 5px; }
        .price { color: #ff6b6b; font-weight: bold; }
        .button-group { 
            text-align: center; 
            margin-top: 30px;
        }
        .btn { 
            display: inline-block;
            background: #667eea; 
            color: white; 
            padding: 12px 40px; 
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover { 
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">
                <?php echo $success ? '✓' : '✗'; ?>
            </div>
            
            <h1><?php echo $message; ?></h1>
            
            <div class="details">
                <?php foreach ($details as $detail): ?>
                    <div class="detail-item"><?php echo $detail; ?></div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($success): ?>
                <h3 style="color: #333; margin: 20px 0 15px 0;">Sản phẩm quần áo mới:</h3>
                <div class="products-list">
                    <div class="product-card"><span class="emoji">👔</span> Áo Sơ Mi Nam <span class="price">299k</span></div>
                    <div class="product-card"><span class="emoji">👔</span> Áo Thun Nam <span class="price">149k</span></div>
                    <div class="product-card"><span class="emoji">👗</span> Áo Phông Nữ <span class="price">129k</span></div>
                    <div class="product-card"><span class="emoji">👗</span> Áo Sơ Mi Nữ <span class="price">349k</span></div>
                    <div class="product-card"><span class="emoji">👖</span> Quần Jean Nam <span class="price">449k</span></div>
                    <div class="product-card"><span class="emoji">👖</span> Quần Tây Nam <span class="price">599k</span></div>
                    <div class="product-card"><span class="emoji">👖</span> Quần Jean Nữ <span class="price">389k</span></div>
                    <div class="product-card"><span class="emoji">👖</span> Quần Short Nữ <span class="price">199k</span></div>
                    <div class="product-card"><span class="emoji">👗</span> Váy Chữ A <span class="price">599k</span></div>
                    <div class="product-card"><span class="emoji">👗</span> Đầm Midi <span class="price">699k</span></div>
                </div>
            <?php endif; ?>
            
            <div class="button-group">
                <a href="index.php" class="btn">← Xem trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>
