<?php
/**
 * Login Handler - Xử lý đăng nhập
 */

session_start();

// Kết nối database & load config TRƯỚC
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
    exit;
}

// Lấy dữ liệu từ form
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
$errors = [];

if (empty($email)) {
    $errors[] = 'Email không được để trống';
}

if (empty($password)) {
    $errors[] = 'Mật khẩu không được để trống';
}

// Nếu có lỗi validation, quay lại login
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Tìm user theo email
$query = "SELECT id, name, email, password, role, active FROM users WHERE email = ? AND active = 1";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['login_errors'] = ['Lỗi hệ thống'];
    header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra user tồn tại
if ($result->num_rows === 0) {
    $_SESSION['login_errors'] = ['Email hoặc mật khẩu không đúng'];
    $stmt->close();
    header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
    exit;
}

// Lấy thông tin user
$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['login_errors'] = ['Email hoặc mật khẩu không đúng'];
    header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
    exit;
}

// Login thành công - Lưu vào session
$_SESSION['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'login_time' => time()
];

// Xóa error message
unset($_SESSION['login_errors']);

// Redirect tới trang chủ
header('Location: ' . Config::get('APP_URL'));
exit;
?>
