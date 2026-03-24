<?php
/**
 * FashionHub - Cập nhật sản phẩm từ điện thoại sang quần áo
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

$message = '';
$success = false;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Xóa tất cả sản phẩm cũ (điện thoại, máy tính)
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM categories");
    
    // Thêm danh mục quần áo
    $categories = "INSERT INTO categories (name, slug, description) VALUES 
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
    
    if (!$conn->query($categories)) {
        throw new Exception("Lỗi thêm danh mục: " . $conn->error);
    }
    
    // Thêm sản phẩm quần áo
    $products = "INSERT INTO products (name, slug, description, price, category_id, stock, active) VALUES 
        ('Áo Sơ Mi Nam Oxford Xanh', 'ao-so-mi-nam-oxford-xanh', 'Áo sơ mi nam chất liệu Oxford cao cấp, thoáng mát, phù hợp công sở', 299000, 1, 25, 1),
        ('Áo Thun Nam Logo Tay Lỡ', 'ao-thun-nam-logo-tay-lo', 'Áo thun nam basic với logo in độc đáo, vải 100% cotton', 149000, 1, 35, 1),
        ('Áo Phông Nữ Trắng Tay Ngắn', 'ao-phong-nu-trang-tay-ngan', 'Áo phông nữ trắng tay ngắn, vải mượt mềm, dễ phối đồ', 129000, 2, 30, 1),
        ('Áo Sơ Mi Nữ Hồng Pastel', 'ao-so-mi-nu-hong-pastel', 'Áo sơ mi nữ màu hồng pastel dịu dàng, kiểu dáng thanh lịch', 349000, 2, 20, 1),
        ('Quần Jean Nam Xanh Đậm', 'quan-jean-nam-xanh-dam', 'Quần jean nam chất liệu cao cấp, màu xanh đậm sang trọng', 449000, 3, 28, 1),
        ('Quần Tây Nam Đen Công Sở', 'quan-tay-nam-den-cong-so', 'Quần tây nam màu đen, thích hợp cho công sở và sự kiện', 599000, 3, 15, 1),
        ('Quần Jean Nữ Xanh Nhạt', 'quan-jean-nu-xanh-nhat', 'Quần jean nữ xanh nhạt, kiểu dáng skinny ôm vừa vặn', 389000, 4, 32, 1),
        ('Quần Short Đùi Nữ Đen', 'quan-short-dui-nu-den', 'Quần short đùi nữ màu đen, chất vải cotton thoáng mát', 199000, 4, 40, 1),
        ('Váy Chữ A Nữ Xám Cổ Điển', 'vay-chu-a-nu-xam-co-dien', 'Váy chữ A nữ màu xám cổ điển, phù hợp công sở', 599000, 5, 18, 1),
        ('Đầm Midi Nữ Họa Tiết Hoa', 'dam-midi-nu-hoa-tiet-hoa', 'Đầm midi nữ họa tiết hoa, dáng xòe thanh lịch', 699000, 6, 12, 1)";
    
    if (!$conn->query($products)) {
        throw new Exception("Lỗi thêm sản phẩm: " . $conn->error);
    }
    
    // Tạo ảnh cho sản phẩm
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
        ['13b0f5', '0ea5e9']
    ];
    
    $emojis = ['👔', '👗', '👖', '👠', '🧥', '👜', '🧤', '👒', '🎽', '👙'];
    
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
            
            $shortName = substr($productName, 0, 20);
            
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
    
    <rect width="300" height="300" fill="url(#grad$productId)"/>
    
    <circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="250" r="50" fill="rgba(255,255,255,0.1)"/>
    <circle cx="250" cy="50" r="30" fill="rgba(0,0,0,0.1)"/>
    
    <g transform="translate(150, 100)">
        <circle cx="0" cy="0" r="45" fill="rgba(255,255,255,0.2)" filter="url(#shadow)"/>
        <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" 
              font-family="Arial, sans-serif" font-size="36" fill="white">
            $emoji
        </text>
    </g>
    
    <text x="150" y="200" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="20" fill="white" font-weight="bold">
        $shortName
    </text>
    
    <text x="150" y="260" dominant-baseline="middle" text-anchor="middle" 
          font-family="Arial, sans-serif" font-size="12" fill="rgba(255,255,255,0.8)">
        FashionHub - Thời trang chất lượng
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
            }
        }
    }
    
    $success = true;
    $message = "✓ Cập nhật thành công! Đã thêm 10 sản phẩm quần áo và tạo $imagesCreated ảnh.";

} catch (Exception $e) {
    $success = false;
    $message = 'Lỗi: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật sản phẩm - FashionHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { max-width: 600px; width: 100%; }
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .icon { font-size: 64px; text-align: center; margin-bottom: 20px; }
        .message { 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0; 
            font-size: 18px; 
            font-weight: 500;
            text-align: center;
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border: 2px solid #28a745;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 2px solid #f5c6cb;
        }
        h1 { text-align: center; color: #333; margin: 20px 0; }
        .products-list {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .product-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-item:last-child { border-bottom: none; }
        .emoji { font-size: 20px; margin-right: 10px; }
        .price { color: #ff6b6b; font-weight: bold; }
        .button-group { text-align: center; margin-top: 30px; }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <?php if ($success): ?>
                <div class="icon">✓</div>
                <div class="message success"><?php echo $message; ?></div>
                <h1>👔 Sản phẩm mới</h1>
                <div class="products-list">
                    <div class="product-item"><div><span class="emoji">👔</span> Áo Sơ Mi Nam Oxford</div><span class="price">299.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👔</span> Áo Thun Nam Logo</div><span class="price">149.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👗</span> Áo Phông Nữ Trắng</div><span class="price">129.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👗</span> Áo Sơ Mi Nữ Hồng</div><span class="price">349.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👖</span> Quần Jean Nam Xanh</div><span class="price">449.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👖</span> Quần Tây Nam Đen</div><span class="price">599.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👖</span> Quần Jean Nữ Xanh</div><span class="price">389.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👖</span> Quần Short Đùi Nữ</div><span class="price">199.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👗</span> Váy Chữ A Xám</div><span class="price">599.000₫</span></div>
                    <div class="product-item"><div><span class="emoji">👗</span> Đầm Midi Hoa</div><span class="price">699.000₫</span></div>
                </div>
            <?php else: ?>
                <div class="icon">✗</div>
                <div class="message error"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="button-group">
                <a href="index.php" class="btn btn-home">← Về trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>
