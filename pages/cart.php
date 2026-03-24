<?php
/**
 * FashionHub - Giỏ hàng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Cart.php';

$pageTitle = 'Giỏ hàng';

$cart = new Cart();
$cartItems = $cart->getItems();
$totalPrice = $cart->getTotalPrice();

include __DIR__ . '/../includes/header.php';
?>

    <div class="page-header">
        <h1>Giỏ hàng của bạn</h1>
    </div>

    <div class="cart-container">
        <?php if (!empty($cartItems)): ?>
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng cộng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                        <tr class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                            <td>
                                <a href="product-detail.php?id=<?php echo $item['product_id']; ?>">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </a>
                            </td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></td>
                            <td>
                                <div class="quantity-control">
                                    <button class="qty-btn-small" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                    <input type="number" value="<?php echo $item['quantity']; ?>" readonly class="qty-input">
                                    <button class="qty-btn-small" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                </div>
                            </td>
                            <td class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <h3>Tóm tắt giỏ hàng</h3>
                
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span class="amount"><?php echo number_format($totalPrice, 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                </div>

                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span class="amount">Tính sau</span>
                </div>

                <div class="summary-row">
                    <span>Thuế:</span>
                    <span class="amount"><?php echo number_format($totalPrice * Config::get('TAX_RATE', 0.1), 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                </div>

                <hr>

                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span class="amount"><?php echo number_format($totalPrice + ($totalPrice * Config::get('TAX_RATE', 0.1)), 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                </div>

                <div class="cart-actions">
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                    <button class="btn btn-primary" onclick="checkout()">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </button>
                </div>

                <button class="btn btn-outline" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Xóa tất cả
                </button>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                <h2>Giỏ hàng trống</h2>
                <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
                <a href="products.php" class="btn btn-primary" style="margin-top: 20px;">
                    Bắt đầu mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function updateQuantity(productId, change) {
    fetch('<?php echo Config::get('APP_URL'); ?>/api/update-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            change: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        fetch('<?php echo Config::get('APP_URL'); ?>/api/remove-from-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function clearCart() {
    if (confirm('Xóa tất cả sản phẩm khỏi giỏ hàng?')) {
        fetch('<?php echo Config::get('APP_URL'); ?>/api/clear-cart.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function checkout() {
    window.location.href = '<?php echo Config::get('APP_URL'); ?>/pages/checkout.php';
}
</script>
