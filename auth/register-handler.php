<?php
/**
 * Register Handler - Xử lý đăng ký
 */

session_start();

// Kết nối database & load config TRƯỚC
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}

// Lấy dữ liệu từ form
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Validate input
$errors = [];

if (empty($name)) {
    $errors[] = 'Tên không được để trống';
}

if (empty($email)) {
    $errors[] = 'Email không được để trống';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không hợp lệ';
}

if (empty($password)) {
    $errors[] = 'Mật khẩu không được để trống';
} elseif (strlen($password) < 6) {
    $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
}

if ($password !== $password_confirm) {
    $errors[] = 'Mật khẩu không khớp';
}

// Nếu có lỗi validation, quay lại register
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}

require_once __DIR__ . '/../helpers/Auth.php';

$db = new Database();
$conn = $db->getConnection();

// Kiểm tra email đã tồn tại
$query = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['register_errors'] = ['Lỗi hệ thống'];
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $_SESSION['register_errors'] = ['Email này đã được đăng ký'];
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}

// Hash password
$hashed_password = Auth::hashPassword($password);

// Insert user vào database
$query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['register_errors'] = ['Lỗi hệ thống'];
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}

$stmt->bind_param('sss', $name, $email, $hashed_password);

if ($stmt->execute()) {
    $stmt->close();
    
    // Tự động login sau khi đăng ký
    $_SESSION['user'] = [
        'id' => $conn->insert_id,
        'name' => $name,
        'email' => $email,
        'role' => 'customer',
        'login_time' => time()
    ];
    
    unset($_SESSION['register_errors']);
    header('Location: ' . Config::get('APP_URL'));
    exit;
} else {
    $_SESSION['register_errors'] = ['Lỗi khi tạo tài khoản'];
    $stmt->close();
    header('Location: ' . Config::get('APP_URL') . 'pages/register.php');
    exit;
}
?>
