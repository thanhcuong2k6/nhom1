<?php
/**
 * Orders Page - Xem danh sách đơn hàng của người dùng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// Yêu cầu login
Auth::requireLogin();

$user = Auth::getCurrentUser();
$userId = $user['id'];
$pageTitle = 'Đơn hàng của tôi';

// Kết nối database
$db = new Database();
$conn = $db->getConnection();

// Lấy danh sách đơn hàng của user
$query = "SELECT 
    id, 
    order_number, 
    total_amount, 
    status, 
    created_at 
FROM orders 
WHERE user_id = ? 
ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hàm format status
function getStatusBadge($status) {
    $colors = [
        'pending' => '#fbbf24',
        'processing' => '#3b82f6',
        'shipped' => '#8b5cf6',
        'delivered' => '#10b981',
        'cancelled' => '#ef4444'
    ];
    
    $labels = [
        'pending' => '⏳ Chờ xử lý',
        'processing' => '⚙️ Đang xử lý',
        'shipped' => '🚚 Đang giao',
        'delivered' => '✅ Đã giao',
        'cancelled' => '❌ Hủy'
    ];
    
    $color = $colors[$status] ?? '#666';
    $label = $labels[$status] ?? ucfirst($status);
    
    return "<span class='status-badge' style='background-color: {$color}'>{$label}</span>";
}

include __DIR__ . '/../includes/header.php';
?>

<div class="orders-section">
    <div class="orders-container">
        <div class="orders-header">
            <h1>📦 Đơn hàng của tôi</h1>
            <p>Xem lịch sử các đơn hàng đã đặt</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h2>Chưa có đơn hàng nào</h2>
                <p>Bạn chưa đặt hàng. Hãy bắt đầu mua sắm ngay!</p>
                <a href="<?php echo Config::get('APP_URL'); ?>pages/products.php" class="btn btn-primary">
                    🛍️ Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Đơn hàng #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <p class="order-date">
                                📅 <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div class="order-status">
                            <?php echo getStatusBadge($order['status']); ?>
                        </div>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <label>Tổng tiền:</label>
                            <span class="amount">
                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> 
                                <?php echo Config::get('CURRENCY_SYMBOL', 'đ'); ?>
                            </span>
                        </div>

                        <div class="detail-item">
                            <label>Trạng thái:</label>
                            <span><?php echo ucfirst($order['status']); ?></span>
                        </div>
                    </div>

                    <div class="order-actions">
                        <a href="<?php echo Config::get('APP_URL'); ?>pages/order-confirmation.php?id=<?php echo $order['id']; ?>" class="btn btn-small btn-info">
                            👁️ Xem chi tiết
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="orders-summary">
                <p>
                    📊 Tổng cộng: <strong><?php echo count($orders); ?> đơn hàng</strong>
                </p>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="<?php echo Config::get('APP_URL'); ?>pages/profile.php">
                ← Quay lại hồ sơ
            </a>
        </div>
    </div>
</div>

<style>
.orders-section {
    padding: 40px 20px;
    background: #f8f9fa;
    min-height: calc(100vh - 300px);
}

.orders-container {
    max-width: 900px;
    margin: 0 auto;
}

.orders-header {
    margin-bottom: 40px;
}

.orders-header h1 {
    font-size: 32px;
    color: #333;
    margin: 0 0 10px;
}

.orders-header p {
    color: #666;
    font-size: 16px;
    margin: 0;
}

.empty-state {
    background: white;
    padding: 60px 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-state h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    color: #666;
    margin-bottom: 30px;
}

.btn-primary {
    display: inline-block;
    background: #667eea;
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

.orders-list {
    display: grid;
    gap: 20px;
    margin-bottom: 40px;
}

.order-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.order-info h3 {
    font-size: 18px;
    color: #333;
    margin: 0 0 5px;
}

.order-date {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.order-status {
    text-align: right;
}

.status-badge {
    display: inline-block;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
}

.order-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-item label {
    color: #666;
    font-weight: 600;
}

.detail-item span {
    color: #333;
}

.amount {
    font-weight: 700;
    color: #667eea;
    font-size: 18px;
}

.order-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-small {
    padding: 8px 14px;
    font-size: 13px;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.orders-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    font-size: 16px;
    margin-bottom: 20px;
}

.back-link {
    text-align: center;
    margin-top: 30px;
}

.back-link a {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.back-link a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .orders-section {
        padding: 20px 10px;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .order-status {
        text-align: left;
    }

    .order-details {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
