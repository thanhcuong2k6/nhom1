<?php
/**
 * FashionHub - Ví dụ hiển thị sản phẩm với ImageHelper
 * Sử dụng public/images/products/{image}
 * Fallback: public/images/no-image.jpg
 */

session_start();
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/helpers/ImageHelper.php';

$db = new Database();
$productModel = new Product();
$products = $productModel->getAll(12);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm - ImageHelper Demo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { 
            text-align: center; 
            margin-bottom: 30px;
            color: #333;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }
        .product-image {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
            background: #f0f0f0;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .product-info {
            padding: 15px;
        }
        .product-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .product-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.4;
            max-height: 2.8em;
            overflow: hidden;
        }
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .product-price {
            font-size: 16px;
            font-weight: bold;
            color: #ff6b6b;
        }
        .product-stock {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .stock-available {
            background: #d4edda;
            color: #155724;
        }
        .stock-out {
            background: #f8d7da;
            color: #721c24;
        }
        .info-box {
            background: #e7f3ff;
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            color: #004085;
        }
        .info-box h3 {
            margin-bottom: 10px;
            color: #0a3d62;
        }
        .info-box ul {
            list-style: none;
            padding-left: 20px;
        }
        .info-box li {
            margin-bottom: 8px;
            position: relative;
        }
        .info-box li:before {
            content: "✓";
            position: absolute;
            left: -20px;
            color: #28a745;
            font-weight: bold;
        }
        .code-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            border-radius: 4px;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Demo Sản Phẩm - ImageHelper</h1>
        
        <div class="info-box">
            <h3>ℹ️ Cách dùng ImageHelper</h3>
            <p><strong>1. Lấy URL ảnh:</strong></p>
            <div class="code-box">
$imageUrl = ImageHelper::getProductImage($product['image']);
            </div>
            
            <p><strong>2. Hiển thị thẻ img (cách nhanh nhất):</strong></p>
            <div class="code-box">
&lt;?php echo ImageHelper::renderProductImage($product['image'], $product['name'], 'product-img'); ?&gt;
            </div>
            
            <p><strong>Tính năng:</strong></p>
            <ul>
                <li>Ảnh từ: <strong>public/images/products/{image}</strong></li>
                <li>Fallback: <strong>public/images/no-image.jpg</strong></li>
                <li>Tự động kiểm tra file có tồn tại không</li>
                <li>Hỗ trợ lazy loading</li>
            </ul>
        </div>

        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <!-- Cách sử dụng: renderProductImage -->
                        <div class="product-image">
                            <?php 
                            // Kiểm tra ảnh có tồn tại không
                            $hasImage = ImageHelper::imageExists($product['image']);
                            ?>
                            <?php echo ImageHelper::renderProductImage(
                                $product['image'],
                                htmlspecialchars($product['name']),
                                'product-img'
                            ); ?>
                            <span class="product-badge">
                                <?php echo $hasImage ? '✓ Có ảnh' : '⚠ Mặc định'; ?>
                            </span>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-name">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </div>
                            <p class="product-description">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                            
                            <div class="product-footer">
                                <div class="product-price">
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> ₫
                                </div>
                                <span class="product-stock <?php echo $product['stock'] > 0 ? 'stock-available' : 'stock-out'; ?>">
                                    <?php echo $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message" style="grid-column: 1/-1;">
                    <p>😢 Chưa có sản phẩm nào</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="info-box" style="background: #fff3cd; border-color: #ffc107; color: #856404;">
            <h3>💡 Hướng dẫn cài đặt</h3>
            <p><strong>1. Tạo cấu trúc thư mục:</strong></p>
            <div class="code-box" style="background: #fffbea; border-color: #ffc107; color: #856404;">
public/
  └── images/
      ├── products/          ← Upload ảnh sản phẩm vào đây
      └── no-image.jpg       ← Ảnh mặc định (bắt buộc)
            </div>
            
            <p><strong>2. Database column:</strong></p>
            <div class="code-box" style="background: #fffbea; border-color: #ffc107; color: #856404;">
products.image (VARCHAR) - Lưu tên file, VD: "product_123.jpg"
            </div>
            
            <p><strong>3. Tạo ảnh no-image.jpg:</strong></p>
            <div class="code-box" style="background: #fffbea; border-color: #ffc107; color: #856404;">
- Dùng Photoshop, Canva, hoặc tool online tạo ảnh 250x250px
- Hoặc copy ảnh placeholder có sẵn
- Đặt tại: public/images/no-image.jpg
            </div>
        </div>
    </div>
</body>
</html>
