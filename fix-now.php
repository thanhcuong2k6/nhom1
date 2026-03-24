<?php
/**
 * FashionHub - Sửa sản phẩm ngay lập tức
 * Script này sẽ trực tiếp xóa sản phẩm cũ và thêm sản phẩm quần áo
 */

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Xóa toàn bộ dữ liệu cũ
    $conn->query("DELETE FROM order_items");
    $conn->query("DELETE FROM orders");
    $conn->query("DELETE FROM reviews");
    $conn->query("DELETE FROM payments");
    $conn->query("DELETE FROM products");
    $conn->query("DELETE FROM categories");
    
    // Thêm danh mục quần áo
    $sql = "INSERT INTO categories (name, slug, description) VALUES 
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
    
    $conn->query($sql);
    
    // Thêm sản phẩm quần áo
    $sql = "INSERT INTO products (name, slug, description, price, category_id, stock, active) VALUES 
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
    
    $conn->query($sql);
    
    // Tạo thư mục uploads
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    // Màu sắc và emoji
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
    
    // Tạo ảnh cho sản phẩm
    $result = $conn->query("SELECT id, name FROM products ORDER BY id");
    
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
            file_put_contents($filepath, $svg);
            $conn->query("UPDATE products SET image = '$filename' WHERE id = $productId");
        }
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sửa sản phẩm - FashionHub</title>
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
            .container { max-width: 600px; width: 100%; }
            .card { 
                background: white; 
                border-radius: 12px; 
                padding: 40px; 
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                text-align: center;
            }
            .icon { font-size: 80px; margin-bottom: 20px; }
            h1 { color: #333; margin: 20px 0; font-size: 28px; }
            .message { 
                color: #155724;
                background: #d4edda;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                border: 2px solid #28a745;
                font-size: 16px;
            }
            .products-grid { 
                display: grid; 
                grid-template-columns: repeat(2, 1fr); 
                gap: 10px;
                margin: 30px 0;
                text-align: left;
            }
            .product-item {
                background: #f9f9f9;
                padding: 10px;
                border-radius: 6px;
                font-size: 13px;
                border-left: 3px solid #667eea;
            }
            .emoji { font-size: 20px; margin-right: 8px; }
            .btn { 
                display: inline-block;
                background: #667eea; 
                color: white; 
                padding: 12px 30px; 
                border: none;
                border-radius: 6px;
                cursor: pointer;
                text-decoration: none;
                font-size: 16px;
                transition: 0.3s;
            }
            .btn:hover { background: #5568d3; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="icon">✓</div>
                <h1>Đã sửa thành công!</h1>
                <div class="message">
                    ✓ Đã xóa sản phẩm cũ (điện thoại, máy tính)<br>
                    ✓ Đã thêm 10 sản phẩm quần áo mới<br>
                    ✓ Đã tạo ảnh cho tất cả sản phẩm
                </div>
                
                <h3 style="color: #333; text-align: left; margin: 20px 0;">Sản phẩm mới:</h3>
                <div class="products-grid">
                    <div class="product-item"><span class="emoji">👔</span> Áo Sơ Mi Nam - 299k</div>
                    <div class="product-item"><span class="emoji">👔</span> Áo Thun Nam - 149k</div>
                    <div class="product-item"><span class="emoji">👗</span> Áo Phông Nữ - 129k</div>
                    <div class="product-item"><span class="emoji">👗</span> Áo Sơ Mi Nữ - 349k</div>
                    <div class="product-item"><span class="emoji">👖</span> Quần Jean Nam - 449k</div>
                    <div class="product-item"><span class="emoji">👖</span> Quần Tây Nam - 599k</div>
                    <div class="product-item"><span class="emoji">👖</span> Quần Jean Nữ - 389k</div>
                    <div class="product-item"><span class="emoji">👖</span> Quần Short Nữ - 199k</div>
                    <div class="product-item"><span class="emoji">👗</span> Váy Chữ A - 599k</div>
                    <div class="product-item"><span class="emoji">👗</span> Đầm Midi - 699k</div>
                </div>
                
                <a href="index.php" class="btn">← Về trang chủ xem sản phẩm</a>
            </div>
        </div>
    </body>
    </html>
<?php
} catch (Exception $e) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Lỗi</title>
        <style>
            body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
            .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h2>✗ Có lỗi:</h2>
            <p><?php echo $e->getMessage(); ?></p>
        </div>
    </body>
    </html>
    <?php
}
?>
