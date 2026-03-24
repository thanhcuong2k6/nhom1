<?php
/**
 * FashionHub - Xác nhận đơn hàng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';

$pageTitle = 'Xác nhận đơn hàng';

// Kiểm tra đơn hàng
if (!isset($_SESSION['order'])) {
    header('Location: products.php');
    exit;
}

$order = $_SESSION['order'];
$orderNumber = 'ORD-' . date('YmdHis');

include __DIR__ . '/../includes/header.php';
?>

    <div class="order-confirmation">
        <div class="confirmation-container">
            <div class="success-message">
                <i class="fas fa-check-circle" style="font-size: 64px; color: #51cf66; margin-bottom: 20px;"></i>
                <h1>Đặt hàng thành công!</h1>
                <p>Cảm ơn bạn đã mua sắm tại FashionHub</p>
            </div>

            <div class="order-info">
                <h2>Thông tin đơn hàng</h2>

                <div class="info-group">
                    <div class="info-row">
                        <span class="label">Mã đơn hàng:</span>
                        <span class="value"><?php echo htmlspecialchars($orderNumber); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Ngày đặt:</span>
                        <span class="value"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Trạng thái:</span>
                        <span class="value status-pending">
                            <i class="fas fa-clock"></i> Đang xử lý
                        </span>
                    </div>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Thông tin giao hàng</h3>

                <div class="info-group">
                    <div class="info-row">
                        <span class="label">Người nhận:</span>
                        <span class="value"><?php echo htmlspecialchars($order['name']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Số điện thoại:</span>
                        <span class="value"><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Địa chỉ:</span>
                        <span class="value"><?php echo htmlspecialchars($order['address']) . ', ' . htmlspecialchars($order['city']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Phương thức thanh toán:</span>
                        <span class="value">
                            <?php
                                $methods = [
                                    'cod' => 'Thanh toán khi nhận hàng',
                                    'bank' => 'Chuyển khoản ngân hàng',
                                    'card' => 'Thẻ tín dụng/Ghi nợ',
                                    'wallet' => 'Ví điện tử'
                                ];
                                echo isset($methods[$order['payment_method']]) ? $methods[$order['payment_method']] : $order['payment_method'];
                            ?>
                        </span>
                    </div>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Chi tiết sản phẩm</h3>

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng cộng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Tóm tắt thanh toán</h3>

                <div class="info-group">
                    <div class="info-row">
                        <span class="label">Tạm tính:</span>
                        <span class="value"><?php 
                            $subtotal = 0;
                            foreach ($order['items'] as $item) {
                                $subtotal += $item['price'] * $item['quantity'];
                            }
                            echo number_format($subtotal, 0, ',', '.');
                        ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Thuế:</span>
                        <span class="value"><?php 
                            $tax = $subtotal * Config::get('TAX_RATE', 0.1);
                            echo number_format($tax, 0, ',', '.');
                        ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="label">Vận chuyển:</span>
                        <span class="value">Miễn phí</span>
                    </div>

                    <hr style="margin: 15px 0;">

                    <div class="info-row total">
                        <span class="label">Tổng tiền:</span>
                        <span class="value"><?php echo number_format($order['total'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <a href="<?php echo Config::get('APP_URL'); ?>" class="btn btn-primary">
                        <i class="fas fa-home"></i> Tiếp tục mua sắm
                    </a>
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> In đơn hàng
                    </button>
                </div>

                <div class="confirmation-note">
                    <p>
                        <strong>Lưu ý:</strong> Đơn hàng của bạn đang được xử lý. 
                        Bạn sẽ nhận được email xác nhận về trạng thái giao hàng. 
                        Vui lòng kiểm tra email để cập nhật thông tin.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .order-confirmation {
            max-width: 800px;
            margin: 40px auto;
        }

        .confirmation-container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .success-message {
            text-align: center;
            padding: 40px;
            background: linear-gradient(135deg, #51cf66, #37b24d);
            color: white;
        }

        .success-message h1 {
            color: white;
            margin-bottom: 10px;
        }

        .order-info {
            padding: 40px;
        }

        .order-info h2 {
            color: var(--dark-color);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
        }

        .order-info h3 {
            color: var(--dark-color);
            margin-bottom: 15px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row .label {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .info-row .value {
            text-align: right;
            color: var(--dark-color);
        }

        .info-row.total {
            font-size: 18px;
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 0;
        }

        .info-row.total .value {
            color: var(--primary-color);
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffd43b;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .order-table thead {
            background-color: var(--light-color);
        }

        .order-table th, .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .order-table th {
            font-weight: 600;
        }

        .confirmation-actions {
            display: flex;
            gap: 10px;
            margin: 30px 0;
        }

        .confirmation-actions .btn {
            flex: 1;
            text-align: center;
        }

        .confirmation-note {
            background-color: #e7f5ff;
            border-left: 4px solid #1971c2;
            padding: 15px;
            border-radius: 4px;
            color: #1971c2;
        }

        @media (max-width: 768px) {
            .order-info {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-row .value {
                text-align: left;
            }

            .confirmation-actions {
                flex-direction: column;
            }
        }

        @media print {
            .confirmation-actions {
                display: none;
            }

            body {
                background-color: white;
            }
        }
    </style>

<?php 
// Xóa đơn hàng khỏi session sau khi hiển thị
unset($_SESSION['order']);
include __DIR__ . '/../includes/footer.php'; 
?>
