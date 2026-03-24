<?php
/**
 * Profile Page - Hồ sơ cá nhân người dùng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../helpers/Auth.php';

// Yêu cầu login
Auth::requireLogin();

$user = Auth::getCurrentUser();
$pageTitle = 'Hồ sơ cá nhân';

include __DIR__ . '/../includes/header.php';
?>

<div class="profile-section">
    <div class="profile-container">
        <div class="profile-header">
            <h1>👤 Hồ sơ cá nhân</h1>
        </div>

        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                </div>
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="role-badge"><?php echo $user['role'] === 'admin' ? '👑 Admin' : '👤 Khách hàng'; ?></p>
            </div>

            <div class="profile-main">
                <div class="profile-card">
                    <h3>📋 Thông tin cơ bản</h3>
                    
                    <div class="profile-field">
                        <label>Tên đầy đủ:</label>
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>

                    <div class="profile-field">
                        <label>Email:</label>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <div class="profile-field">
                        <label>User ID:</label>
                        <p>#<?php echo $user['id']; ?></p>
                    </div>

                    <div class="profile-field">
                        <label>Quyền:</label>
                        <p><?php echo $user['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng'; ?></p>
                    </div>

                    <div class="profile-field">
                        <label>Đăng nhập lúc:</label>
                        <p><?php echo date('d/m/Y H:i:s', $user['login_time']); ?></p>
                    </div>
                </div>

                <div class="profile-card">
                    <h3>🔧 Thao tác</h3>
                    
                    <div class="profile-actions">
                        <a href="<?php echo Config::get('APP_URL'); ?>pages/orders.php" class="btn btn-primary">
                            📦 Xem đơn hàng của tôi
                        </a>
                        
                        <?php if (Auth::isAdmin()): ?>
                            <a href="<?php echo Config::get('APP_URL'); ?>admin/" class="btn btn-success">
                                ⚙️ Admin Dashboard
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php" class="btn btn-danger">
                            🚪 Đăng xuất
                        </a>
                    </div>
                </div>

                <div class="profile-card info-card">
                    <h3>ℹ️ Thông tin thêm</h3>
                    <p>Bạn có thể xem lịch sử đơn hàng, cập nhật thông tin cá nhân, và quản lý tài khoản của mình từ đây.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-section {
    padding: 40px 20px;
    background: #f8f9fa;
    min-height: calc(100vh - 300px);
}

.profile-container {
    max-width: 1000px;
    margin: 0 auto;
}

.profile-header {
    margin-bottom: 30px;
}

.profile-header h1 {
    font-size: 32px;
    color: #333;
    margin: 0;
}

.profile-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
}

.profile-sidebar {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.profile-avatar {
    margin-bottom: 20px;
}

.avatar-placeholder {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: white;
    font-weight: bold;
    margin: 0 auto;
}

.profile-sidebar h2 {
    font-size: 24px;
    margin: 15px 0 10px;
    color: #333;
}

.role-badge {
    display: inline-block;
    background: #e8f0ff;
    color: #667eea;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin: 0;
}

.profile-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.profile-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-card h3 {
    font-size: 18px;
    color: #333;
    margin-top: 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.profile-field {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 20px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.profile-field:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.profile-field label {
    font-weight: 600;
    color: #666;
}

.profile-field p {
    margin: 0;
    color: #333;
}

.profile-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile-actions .btn {
    padding: 12px 20px;
    text-align: center;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: block;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-2px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.info-card {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.info-card h3 {
    border-bottom-color: #d1d5db;
}

.info-card p {
    color: #666;
    line-height: 1.6;
    margin: 0;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }

    .profile-field {
        grid-template-columns: 1fr;
        gap: 5px;
    }

    .profile-header h1 {
        font-size: 24px;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
