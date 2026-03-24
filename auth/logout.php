<?php
/**
 * Logout Handler - Xử lý đăng xuất
 */

session_start();

// Xóa session user
unset($_SESSION['user']);

// Destroy session
session_destroy();

// Redirect tới trang login
require_once __DIR__ . '/../config/Config.php';
Config::load();

header('Location: ' . Config::get('APP_URL') . 'pages/login.php');
exit;
?>
