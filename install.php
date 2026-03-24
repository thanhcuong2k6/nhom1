<?php
/**
 * FashionHub - Installation Script
 */

session_start();
require_once __DIR__ . '/config/Config.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$success = false;

// Kiểm tra nếu đã cài đặt rồi
if (file_exists(__DIR__ . '/.installed')) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FashionHub - Đã cài đặt</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 50px auto; }
            .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .success-icon { font-size: 64px; color: #51cf66; text-align: center; margin-bottom: 20px; }
            h1 { text-align: center; color: #212529; margin-bottom: 20px; }
            p { text-align: center; color: #666; margin-bottom: 15px; }
            .links { text-align: center; }
            .links a { display: inline-block; margin: 10px; padding: 12px 24px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 4px; transition: 0.3s; }
            .links a:hover { background: #ff5252; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="success-icon">✓</div>
                <h1>FashionHub - Đã cài đặt</h1>
                <p>Hệ thống đã được cài đặt thành công!</p>
                <p>Bạn có thể đang truy cập website hoặc bảng quản trị.</p>
                <div class="links">
                    <a href="index.php">Trang chủ</a>
                    <a href="admin/">Bảng quản trị</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    try {
        require_once __DIR__ . '/config/Database.php';
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // Tạo tài khoản admin
        $name = $_POST['admin_name'] ?? 'Admin';
        $email = $_POST['admin_email'] ?? 'admin@fashionhub.local';
        $password = $_POST['admin_password'] ?? 'admin123';
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `active`) 
                VALUES ('$name', '$email', '0123456789', '$password_hash', 'admin', 1)
                ON DUPLICATE KEY UPDATE `password` = '$password_hash'";
        
        $conn->query($sql);
        
        // Tạo file .installed
        file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
        
        $success = true;
        $message = 'Cài đặt hoàn tất thành công!';
        
    } catch (Exception $e) {
        $message = 'Lỗi: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FashionHub - Cài đặt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .card-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .card-body {
            padding: 40px;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ddd;
            z-index: 0;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: #666;
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .step.completed .step-number {
            background: #51cf66;
            color: white;
            border-color: #51cf66;
        }

        .step-title {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .info-box {
            background: #e7f5ff;
            border-left: 4px solid #1971c2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #1971c2;
        }

        .success-box {
            background: #d3f9d8;
            border-left: 4px solid #51cf66;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #2f9e44;
        }

        .error-box {
            background: #ffe0e0;
            border-left: 4px solid #ff7373;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #c92a2a;
        }

        .buttons {
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #ddd;
            color: #333;
        }

        .btn-secondary:hover {
            background: #ccc;
        }

        .next-link {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
        }

        .next-link:hover {
            background: #5568d3;
        }

        .success-message {
            text-align: center;
        }

        .success-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .success-message h2 {
            color: #51cf66;
            margin-bottom: 20px;
        }

        .success-message p {
            color: #666;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #f5f5f5;
            font-weight: 600;
        }

        .status-ok {
            color: #51cf66;
            font-weight: 600;
        }

        .status-error {
            color: #ff7373;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>� FashionHub</h1>
                <p>Hệ thống website bán hàng</p>
            </div>

            <div class="card-body">
                <!-- Steps -->
                <div class="steps">
                    <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                        <div class="step-number"><?php echo $step > 1 ? '✓' : '1'; ?></div>
                        <div class="step-title">Kiểm tra hệ thống</div>
                    </div>
                    <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                        <div class="step-number"><?php echo $step > 2 ? '✓' : '2'; ?></div>
                        <div class="step-title">Tạo tài khoản</div>
                    </div>
                    <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                        <div class="step-number"><?php echo $step > 3 ? '✓' : '3'; ?></div>
                        <div class="step-title">Hoàn tất</div>
                    </div>
                </div>

                <!-- Step 1: System Check -->
                <?php if ($step == 1): ?>
                    <h2 style="margin-bottom: 20px;">Kiểm tra yêu cầu hệ thống</h2>

                    <table>
                        <tr>
                            <th>Yêu cầu</th>
                            <th>Trạng thái</th>
                        </tr>
                        <tr>
                            <td>PHP >= 7.4</td>
                            <td class="status-ok">✓ OK (<?php echo PHP_VERSION; ?>)</td>
                        </tr>
                        <tr>
                            <td>MySQL/MySQLi</td>
                            <td class="<?php echo extension_loaded('mysqli') ? 'status-ok' : 'status-error'; ?>">
                                <?php echo extension_loaded('mysqli') ? '✓ OK' : '✗ Không tìm thấy'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Thư mục uploads ghi được</td>
                            <td class="<?php echo is_writable(__DIR__ . '/uploads') ? 'status-ok' : 'status-error'; ?>">
                                <?php echo is_writable(__DIR__ . '/uploads') ? '✓ OK' : '✗ Không có quyền'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>File .env tồn tại</td>
                            <td class="<?php echo file_exists(__DIR__ . '/.env') ? 'status-ok' : 'status-error'; ?>">
                                <?php echo file_exists(__DIR__ . '/.env') ? '✓ OK' : '✗ Không tìm thấy'; ?>
                            </td>
                        </tr>
                    </table>

                    <div class="info-box">
                        <strong>ℹ️ Thông tin kết nối:</strong><br>
                        Host: <?php echo Config::get('DB_HOST'); ?><br>
                        User: <?php echo Config::get('DB_USER'); ?><br>
                        Database: <?php echo Config::get('DB_NAME'); ?>
                    </div>

                    <div class="buttons">
                        <a href="?step=2" class="next-link">Tiếp tục →</a>
                    </div>

                <?php endif; ?>

                <!-- Step 2: Create Admin Account -->
                <?php if ($step == 2): ?>
                    <h2 style="margin-bottom: 20px;">Tạo tài khoản admin</h2>

                    <?php if ($success): ?>
                        <div class="success-box">✓ Tạo tài khoản thành công!</div>
                        <div class="buttons">
                            <a href="?step=3" class="next-link">Tiếp tục →</a>
                        </div>
                    <?php else: ?>
                        <?php if ($message && !$success): ?>
                            <div class="error-box"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="admin_name">Tên admin</label>
                                <input type="text" id="admin_name" name="admin_name" value="Admin" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_email">Email admin</label>
                                <input type="email" id="admin_email" name="admin_email" value="admin@fashionhub.local" required>
                            </div>

                            <div class="form-group">
                                <label for="admin_password">Mật khẩu admin</label>
                                <input type="password" id="admin_password" name="admin_password" value="admin123" required>
                            </div>

                            <div class="info-box">
                                <strong>⚠️ Lưu ý:</strong> Vui lòng đổi mật khẩu admin sau khi cài đặt.
                            </div>

                            <div class="buttons">
                                <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                            </div>
                        </form>
                    <?php endif; ?>

                <?php endif; ?>

                <!-- Step 3: Completed -->
                <?php if ($step == 3): ?>
                    <div class="success-message">
                        <div class="success-icon">✓</div>
                        <h2>Cài đặt hoàn tất!</h2>
                        <p>Hệ thống FashionHub đã sẵn sàng sử dụng.</p>

                        <div class="info-box" style="text-align: left;">
                            <strong>Tài khoản đăng nhập:</strong><br>
                            Email: admin@fashionhub.local<br>
                            Mật khẩu: admin123
                        </div>

                        <div class="buttons">
                            <a href="index.php" class="next-link">Trang chủ</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
