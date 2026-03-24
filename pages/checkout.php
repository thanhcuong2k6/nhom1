<?php
/**
 * FashionHub - Thanh toán sản phẩm thời trang
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Cart.php';

$pageTitle = 'Thanh toán';

// Kiểm tra giỏ hàng trống
$cart = new Cart();
$cartItems = $cart->getItems();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$totalPrice = $cart->getTotalPrice();
$tax = $totalPrice * Config::get('TAX_RATE', 0.1);
$finalTotal = $totalPrice + $tax;

// Xử lý form thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cod';

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($address)) {
        // Tạo đơn hàng (trong production, lưu vào database)
        $_SESSION['order'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'payment_method' => $payment_method,
            'items' => $cartItems,
            'total' => $finalTotal,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Xóa giỏ hàng
        $cart->clear();

        // Chuyển hướng đến trang xác nhận
        header('Location: order-confirmation.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

    <div class="page-header">
        <h1>Thanh toán</h1>
    </div>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Thông tin giao hàng</h2>
            
            <form method="POST" class="form-checkout">
                <div class="form-group">
                    <label for="name">Họ và tên *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ *</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-group">
                    <label for="city">Thành phố/Tỉnh *</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Phương thức thanh toán</h3>

                <div class="payment-methods">
                    <div class="payment-option">
                        <input type="radio" id="cod" name="payment_method" value="cod" checked>
                        <label for="cod">
                            <i class="fas fa-money-bill"></i>
                            Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="bank" name="payment_method" value="bank">
                        <label for="bank">
                            <i class="fas fa-university"></i>
                            Chuyển khoản ngân hàng
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="card" name="payment_method" value="card">
                        <label for="card">
                            <i class="fas fa-credit-card"></i>
                            Thẻ tín dụng/Ghi nợ
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="wallet" name="payment_method" value="wallet">
                        <label for="wallet">
                            <i class="fas fa-wallet"></i>
                            Ví điện tử
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 30px;">
                    <i class="fas fa-check"></i> Xác nhận đơn hàng
                </button>
            </form>
        </div>

        <div class="checkout-summary">
            <h2>Tóm tắt đơn hàng</h2>

            <div class="summary-items">
                <?php foreach ($cartItems as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <hr style="margin: 15px 0;">

            <div class="summary-row">
                <span>Tạm tính:</span>
                <span><?php echo number_format($totalPrice, 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
            </div>

            <div class="summary-row">
                <span>Thuế (<?php echo Config::get('TAX_RATE', 0.1) * 100; ?>%):</span>
                <span><?php echo number_format($tax, 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
            </div>

            <div class="summary-row">
                <span>Phí vận chuyển:</span>
                <span>Miễn phí</span>
            </div>

            <hr style="margin: 15px 0;">

            <div class="summary-row total">
                <span>Tổng cộng:</span>
                <span><?php echo number_format($finalTotal, 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
            </div>

            <a href="cart.php" class="btn btn-secondary" style="display: block; text-align: center; margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
            </a>
        </div>
    </div>

    <style>
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 40px;
        }

        .form-checkout {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }

        .payment-methods {
            display: grid;
            gap: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-option:hover {
            border-color: var(--primary-color);
            background-color: rgba(255, 107, 107, 0.05);
        }

        .payment-option input[type="radio"] {
            margin-right: 10px;
        }

        .payment-option label {
            flex: 1;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkout-summary {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 8px;
            height: fit-content;
        }

        .summary-items {
            margin-bottom: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
