<?php
/**
 * Auth Helper - Helper class xử lý authentication
 */

class Auth {
    /**
     * Kiểm tra user đã login chưa
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Lấy thông tin user từ session
     */
    public static function getCurrentUser() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    /**
     * Lấy ID user
     */
    public static function getUserId() {
        return isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    }

    /**
     * Lấy email user
     */
    public static function getUserEmail() {
        return isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : null;
    }

    /**
     * Lấy tên user
     */
    public static function getUserName() {
        return isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : null;
    }

    /**
     * Lấy role user
     */
    public static function getUserRole() {
        return isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;
    }

    /**
     * Kiểm tra user có là admin không
     */
    public static function isAdmin() {
        return self::isLoggedIn() && self::getUserRole() === 'admin';
    }

    /**
     * Kiểm tra user có là customer không
     */
    public static function isCustomer() {
        return self::isLoggedIn() && self::getUserRole() === 'customer';
    }

    /**
     * Require login - Yêu cầu phải login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            require_once __DIR__ . '/../config/Config.php';
            Config::load();
            header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
            exit;
        }
    }

    /**
     * Require admin - Yêu cầu phải là admin
     */
    public static function requireAdmin() {
        if (!self::isLoggedIn()) {
            require_once __DIR__ . '/../config/Config.php';
            Config::load();
            header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
            exit;
        }
        
        if (self::getUserRole() !== 'admin') {
            header('HTTP/1.0 403 Forbidden');
            die('❌ Bạn không có quyền truy cập trang này. Chỉ admin mới được phép!');
        }
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Login user
     */
    public static function login($email, $password, $database_connection) {
        $query = "SELECT id, name, email, password, role, active FROM users WHERE email = ? AND active = 1";
        $stmt = $database_connection->prepare($query);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email không tồn tại'];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        if (!self::verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu không đúng'];
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'login_time' => time()
        ];

        return ['success' => true, 'message' => 'Đăng nhập thành công'];
    }

    /**
     * Logout user
     */
    public static function logout() {
        unset($_SESSION['user']);
        session_destroy();
    }
}
?>
