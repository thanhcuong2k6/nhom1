<?php
/**
 * Register Page - Trang đăng ký
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();

// Nếu đã login, redirect tới trang chủ
if (isset($_SESSION['user'])) {
    header('Location: ' . Config::get('APP_URL'));
    exit;
}

// Lấy error message nếu có
$errors = isset($_SESSION['register_errors']) ? $_SESSION['register_errors'] : [];
unset($_SESSION['register_errors']);

// Lấy dữ liệu nếu có
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - ShopDo</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-message li {
            margin: 5px 0;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Đăng ký</h1>
            <p>Tạo tài khoản mới</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo Config::get('APP_URL'); ?>auth/register-handler.php">
            <div class="form-group">
                <label for="name">Tên đầy đủ</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo $name; ?>" 
                    placeholder="Nhập tên của bạn" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo $email; ?>" 
                    placeholder="Nhập email của bạn" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Ít nhất 6 ký tự" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="password_confirm">Xác nhận mật khẩu</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    placeholder="Nhập lại mật khẩu" 
                    required
                >
            </div>

            <button type="submit" class="submit-btn">Đăng ký</button>
        </form>

        <div class="register-footer">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a></p>
        </div>

        <div class="back-link">
            <a href="<?php echo Config::get('APP_URL'); ?>">← Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>
