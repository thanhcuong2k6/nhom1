<?php
/**
 * FashionHub - Chi tiết sản phẩm
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Product.php';

$pageTitle = 'Chi tiết sản phẩm';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$productId = (int)$_GET['id'];
$productModel = new Product();
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: products.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

    <div class="product-detail">
        <div class="product-detail-container">
            <div class="product-image-large">
                <img src="<?php echo !empty($product['image']) ? Config::get('APP_URL') . '/uploads/' . $product['image'] : 'https://placehold.co/500x500/cccccc/999999?text=Khong+co+anh'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     loading="lazy"
                     onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22500%22 height=%22500%22><rect fill=%22%23f0f0f0%22 width=%22500%22 height=%22500%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2220%22>Khong co anh</text></svg>'">
            </div>

            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="product-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half"></i>
                    <span>(125 đánh giá)</span>
                </div>

                <div class="product-price-detail">
                    <span class="price-large"><?php echo number_format($product['price'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                    <span class="original-price"><?php echo number_format($product['price'] * 1.2, 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                </div>

                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng (<?php echo $product['stock']; ?> sản phẩm)</span>
                    <?php else: ?>
                        <span class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <h4>Mô tả sản phẩm</h4>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="product-quantity">
                    <label>Số lượng:</label>
                    <div class="quantity-selector">
                        <button class="qty-btn" onclick="decreaseQty()">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                        <button class="qty-btn" onclick="increaseQty(<?php echo $product['stock']; ?>)">+</button>
                    </div>
                </div>

                <div class="product-actions-detail">
                    <?php if ($product['stock'] > 0): ?>
                        <button class="btn btn-primary btn-lg" onclick="addToCartDetail(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                        <button class="btn btn-secondary btn-lg">
                            <i class="fas fa-heart"></i> Yêu thích
                        </button>
                    <?php else: ?>
                        <button class="btn btn-lg" disabled>Hết hàng</button>
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['id']); ?></p>
                    <p><strong>Các dòng:</strong> Sản phẩm, Bán chạy nhất</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <section class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="products-grid">
            <!-- Thêm sản phẩm liên quan ở đây -->
        </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function increaseQty(max) {
    let qty = parseInt(document.getElementById('quantity').value);
    if (qty < max) {
        document.getElementById('quantity').value = qty + 1;
    }
}

function decreaseQty() {
    let qty = parseInt(document.getElementById('quantity').value);
    if (qty > 1) {
        document.getElementById('quantity').value = qty - 1;
    }
}

function addToCartDetail(productId, productName, price) {
    let quantity = parseInt(document.getElementById('quantity').value);
    
    fetch('<?php echo Config::get('APP_URL'); ?>/api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            document.getElementById('quantity').value = 1;
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}
</script>
