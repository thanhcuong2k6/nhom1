<?php
/**
 * FashionHub - Trang chủ
 */

session_start();
require_once __DIR__ . '/config/Config.php';
Config::load();
require_once __DIR__ . '/models/Product.php';

$pageTitle = 'Trang chủ';

// Lấy danh sách sản phẩm
$productModel = new Product();
$products = $productModel->getAll(8);

include __DIR__ . '/includes/header.php';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Chào mừng đến FashionHub</h1>
            <p>Khám phá bộ sưu tập thời trang cao cấp và trẻ trung</p>
            <a href="<?php echo Config::get('APP_URL'); ?>/pages/products.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <h2>🔥 Sản phẩm nổi bật hôm nay</h2>
        
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo !empty($product['image']) ? Config::get('APP_URL') . '/uploads/' . $product['image'] : 'https://placehold.co/250x250/cccccc/999999?text=Khong+co+anh'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             loading="lazy"
                             onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22250%22><rect fill=%22%23f0f0f0%22 width=%22250%22 height=%22250%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2214%22>Khong co anh</text></svg>'">
                        <span class="product-badge">Mới</span>
                    </div>
                    
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo substr(htmlspecialchars($product['description']), 0, 50); ?>...</p>
                        
                        <div class="product-price">
                            <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="stock">Còn hàng</span>
                            <?php else: ?>
                                <span class="stock out">Hết hàng</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-sm btn-secondary" onclick="viewProduct(<?php echo $product['id']; ?>)">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </button>
                            <?php if ($product['stock'] > 0): ?>
                                <button class="btn btn-sm btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">Hiện chưa có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-item">
            <i class="fas fa-dolly"></i>
            <h3>Giao hàng miễn phí</h3>
            <p>Miễn phí vận chuyển cho đơn hàng trên 500.000₫</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-lock"></i>
            <h3>Thanh toán an toàn</h3>
            <p>Hỗ trợ nhiều phương thức thanh toán an toàn</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-exchange-alt"></i>
            <h3>Đổi trả dễ dàng</h3>
            <p>Đổi size, đổi màu, hoàn tiền trong 30 ngày</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-phone-alt"></i>
            <h3>Hỗ trợ 24/7</h3>
            <p>Đội hỗ trợ khách hàng luôn sẵn sàng giúp</p>
        </div>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
function viewProduct(productId) {
    window.location.href = '<?php echo Config::get('APP_URL'); ?>/pages/product-detail.php?id=' + productId;
}

function addToCart(productId, productName, price) {
    // Gửi AJAX request để thêm sản phẩm vào giỏ hàng
    fetch('<?php echo Config::get('APP_URL'); ?>/api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            updateCartCount();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateCartCount() {
    // Cập nhật số lượng giỏ hàng
    fetch('<?php echo Config::get('APP_URL'); ?>/api/get-cart-count.php')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.cart-count').textContent = data.count;
        });
}
</script>
