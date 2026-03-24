<?php
/**
 * ============================================
 * QUICK START - DÙNG AUTH.PHP
 * ============================================
 */

// Cách 1: Dùng trong pages cần protect
//
// Ở đầu file page cần bảo vệ:

/*
session_start();
require_once __DIR__ . '/../helpers/Auth.php';

// Yêu cầu user phải login
Auth::requireLogin();

// Hoặc yêu cầu admin
Auth::requireAdmin();
*/

// ============================================
// VÍ DỤ 1: Bảo vệ trang profile (chỉ user login mới vào được)
// ============================================

/*
<?php
session_start();
require_once __DIR__ . '/../helpers/Auth.php';

// Yêu cầu login
Auth::requireLogin();

$user = Auth::getCurrentUser();
?>

<h1>Profile của <?php echo $user['name']; ?></h1>
<p>Email: <?php echo $user['email']; ?></p>
*/

// ============================================
// VÍ DỤ 2: Bảo vệ trang admin (chỉ admin được)
// ============================================

/*
<?php
session_start();
require_once __DIR__ . '/../helpers/Auth.php';

// Yêu cầu admin
Auth::requireAdmin();

?>

<h1>Admin Dashboard</h1>
*/

// ============================================
// VÍ DỤ 3: Kiểm tra và hiển thị UI khác nhau
// ============================================

/*
<?php
session_start();
require_once __DIR__ . '/../helpers/Auth.php';

if (Auth::isLoggedIn()) {
    echo "Chào " . Auth::getUserName();
    echo '<a href="/auth/logout.php">Đăng xuất</a>';
    
    if (Auth::isAdmin()) {
        echo '<a href="/admin">Admin Panel</a>';
    }
} else {
    echo '<a href="/pages/login.php">Đăng nhập</a>';
}
?>
*/

// ============================================
// DANH SÁCH HÀM - HÀM CHÍNH
// ============================================

/*

1. AUTH::REQUIRELOGIN()
   - Yêu cầu user phải login
   - Nếu chưa → redirect tới login.php
   - Cách dùng:
     Auth::requireLogin();

2. AUTH::REQUIREADMIN()
   - Yêu cầu user phải là admin
   - Nếu không → báo lỗi 403
   - Cách dùng:
     Auth::requireAdmin();

*/

// ============================================
// DANH SÁCH HÀM - HÀM KIỂM TRA
// ============================================

/*

1. AUTH::ISLOGGEDIN()
   - Kiểm tra user đã login chưa
   - Return: true/false
   - Cách dùng:
     if (Auth::isLoggedIn()) { ... }

2. AUTH::ISADMIN()
   - Kiểm tra user có phải admin không
   - Return: true/false
   - Cách dùng:
     if (Auth::isAdmin()) { ... }

3. AUTH::ISCUSTOMER()
   - Kiểm tra user có phải customer không
   - Return: true/false
   - Cách dùng:
     if (Auth::isCustomer()) { ... }

*/

// ============================================
// DANH SÁCH HÀM - LẤY THÔNG TIN
// ============================================

/*

1. AUTH::GETCURRENTUSER()
   - Lấy toàn bộ thông tin user
   - Return: array hoặc null
   - Mảng chứa: id, name, email, role, login_time
   - Cách dùng:
     $user = Auth::getCurrentUser();

2. AUTH::GETUSERID()
   - Lấy ID user
   - Return: int hoặc null
   - Cách dùng:
     $id = Auth::getUserId();

3. AUTH::GETUSERNAME()
   - Lấy tên user
   - Return: string hoặc null
   - Cách dùng:
     echo Auth::getUserName();

4. AUTH::GETUSEREMAIL()
   - Lấy email user
   - Return: string hoặc null
   - Cách dùng:
     echo Auth::getUserEmail();

5. AUTH::GETUSERROLE()
   - Lấy role user
   - Return: string hoặc null ('customer' hoặc 'admin')
   - Cách dùng:
     echo Auth::getUserRole();

*/

// ============================================
// VÍ DỤ 4: Trang quản lý admin
// ============================================

/*
<?php
session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../helpers/Auth.php';

Config::load();

// Bảo vệ - chỉ admin mới được
Auth::requireAdmin();

$admin = Auth::getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <p>Xin chào admin: <?php echo $admin['name']; ?></p>
    
    <nav>
        <a href="<?php echo Config::get('APP_URL'); ?>admin">Dashboard</a>
        <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php">Quản lý users</a>
        <a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">Quản lý sản phẩm</a>
        <a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">Đăng xuất</a>
    </nav>
</body>
</html>
*/

// ============================================
// VÍ DỤ 5: Trang profile user
// ============================================

/*
<?php
session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../helpers/Auth.php';

Config::load();

// Yêu cầu login
Auth::requireLogin();

$user = Auth::getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>
    <h1>Hồ sơ cá nhân</h1>
    
    <div class="user-info">
        <p><strong>Tên:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
    </div>
    
    <a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">Đăng xuất</a>
</body>
</html>
*/

// ============================================
// SESSION DATA STRUCTURE
// ============================================

/*

$_SESSION['user'] = [
    'id' => 1,                    // int - User ID
    'name' => 'Nguyễn A',         // string - Tên user
    'email' => 'user@example.com',// string - Email
    'role' => 'customer',         // string - Role (customer/admin)
    'login_time' => 1234567890    // int - Timestamp khi login
];

*/

?>
