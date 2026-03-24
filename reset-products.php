<?php
/**
 * FashionHub - Reset Products to Fashion Items
 * Đặt lại sản phẩm thành danh mục thời trang
 */

require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Kết quả
$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reset'])) {
    try {
        // Xóa tất cả sản phẩm cũ
        $conn->query("DELETE FROM `products`");
        $conn->query("DELETE FROM `categories`");
        
        // Insert danh mục mới
        $sql = "INSERT INTO `categories` (`name`, `slug`, `description`) VALUES 
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
        
        if (!$conn->query($sql)) {
            throw new Exception("Lỗi khi thêm danh mục: " . $conn->error);
        }
        
        // Insert sản phẩm mới
        $sql = "INSERT INTO `products` (`name`, `slug`, `description`, `price`, `category_id`, `stock`, `active`) VALUES 
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
        
        if (!$conn->query($sql)) {
            throw new Exception("Lỗi khi thêm sản phẩm: " . $conn->error);
        }
        
        $success = true;
        $message = "✓ Sản phẩm đã được cập nhật thành công! Hệ thống hiện có 10 sản phẩm thời trang.";
    } catch (Exception $e) {
        $message = "✗ Lỗi: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại sản phẩm - FashionHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; }
        .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #212529; margin-bottom: 20px; font-size: 28px; }
        .icon { font-size: 48px; text-align: center; margin-bottom: 20px; }
        .message { padding: 15px; margin: 20px 0; border-radius: 4px; text-align: center; font-weight: 500; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #e7f3ff; border: 1px solid #b3d9ff; color: #004085; margin-bottom: 20px; }
        .info p { margin: 10px 0; }
        .info strong { display: block; margin-bottom: 10px; }
        .products-list { background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .products-list h3 { margin: 10px 0 15px 0; font-size: 16px; color: #333; }
        .product-item { padding: 8px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .product-item:last-child { border-bottom: none; }
        .product-price { color: #ff6b6b; font-weight: bold; float: right; }
        .button-group { text-align: center; margin-top: 30px; }
        button { padding: 12px 30px; margin: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-confirm { background: #ff6b6b; color: white; }
        .btn-confirm:hover { background: #ff5252; }
        .btn-cancel { background: #e0e0e0; color: #333; }
        .btn-cancel:hover { background: #d0d0d0; }
        .btn-home { background: #495057; color: white; text-decoration: none; display: inline-block; }
        .btn-home:hover { background: #3c4147; }
        .check-icon { color: #51cf66; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <?php if ($success): ?>
                <div class="icon">✓</div>
                <div class="message success"><?php echo $message; ?></div>
                <div style="text-align: center;">
                    <a href="index.php" class="btn-home">Về trang chủ</a>
                </div>
            <?php else: ?>
                <h1>👔 Đặt lại sản phẩm</h1>
                <?php if ($message): ?>
                    <div class="message error"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="info">
                    <strong>⚠️ Thao tác này sẽ:</strong>
                    <p><span class="check-icon">•</span> Xóa tất cả sản phẩm cũ (điện thoại, máy tính...)</p>
                    <p><span class="check-icon">•</span> Thêm 10 sản phẩm thời trang mới</p>
                    <p><span class="check-icon">•</span> Cập nhật danh mục thành 10 danh mục thời trang</p>
                </div>

                <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 16px; color: #333;">📦 Sản phẩm mới sẽ được thêm:</h3>
                <div class="products-list">
                    <div class="product-item">Áo Sơ Mi Nam Oxford Xanh <span class="product-price">299.000₫</span></div>
                    <div class="product-item">Áo Thun Nam Logo Tay Lỡ <span class="product-price">149.000₫</span></div>
                    <div class="product-item">Áo Phông Nữ Trắng Tay Ngắn <span class="product-price">129.000₫</span></div>
                    <div class="product-item">Áo Sơ Mi Nữ Hồng Pastel <span class="product-price">349.000₫</span></div>
                    <div class="product-item">Quần Jean Nam Xanh Đậm <span class="product-price">449.000₫</span></div>
                    <div class="product-item">Quần Tây Nam Đen Công Sở <span class="product-price">599.000₫</span></div>
                    <div class="product-item">Quần Jean Nữ Xanh Nhạt <span class="product-price">389.000₫</span></div>
                    <div class="product-item">Quần Short Đùi Nữ Đen <span class="product-price">199.000₫</span></div>
                    <div class="product-item">Váy Chữ A Nữ Xám Cổ Điển <span class="product-price">599.000₫</span></div>
                    <div class="product-item">Đầm Midi Nữ Họa Tiết Hoa <span class="product-price">699.000₫</span></div>
                </div>

                <form method="POST" style="display: none;" id="resetForm">
                    <input type="hidden" name="confirm_reset" value="1">
                </form>

                <div class="button-group">
                    <button class="btn-confirm" onclick="if(confirm('Bạn chắc chắn muốn đặt lại sản phẩm? Sản phẩm cũ sẽ bị xóa.')) { document.getElementById('resetForm').submit(); }">
                        ✓ Xác nhận đặt lại
                    </button>
                    <a href="index.php" class="btn-cancel" style="text-decoration: none; display: inline-block;">← Hủy</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
