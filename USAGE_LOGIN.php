<?php
/**
 * ============================================
 * VÍ DỤ SỬ DỤNG HỆ THỐNG LOGIN
 * ============================================
 */

// -------- VÍ DỤ 1: Kim tra user đã login chưa --------
session_start();
require_once __DIR__ . '/helpers/Auth.php';

// Kiểm tra login
if (Auth::isLoggedIn()) {
    echo "Xin chào, " . Auth::getUserName() . "!";
} else {
    echo "Bạn chưa đăng nhập";
}


// -------- VÍ DỤ 2: Lấy thông tin user --------
$user = Auth::getCurrentUser();
if ($user) {
    echo "User ID: " . $user['id'];
    echo "Email: " . $user['email'];
    echo "Role: " . $user['role'];
}


// -------- VÍ DỤ 3: Require login - Yêu cầu phải login --------
// Auth::requireLogin(); // Nếu user chưa login sẽ redirect tới login page


// -------- VÍ DỤ 4: Require admin - Yêu cầu phải là admin --------
// Auth::requireAdmin(); // Nếu user không là admin sẽ bị từ chối


// -------- VÍ DỤ 5: Kiểm tra role --------
if (Auth::isAdmin()) {
    echo "Bạn là admin";
}

if (Auth::isCustomer()) {
    echo "Bạn là khách hàng";
}


// -------- VÍ DỤ 6: Hash password khi tạo user --------
$plain_password = "password123";
$hashed_password = Auth::hashPassword($plain_password);
// Lưu $hashed_password vào database


// -------- VÍ DỤ 7: Verify password khi login --------
$entered_password = "password123";
$hash_from_db = "$2y$10$..."; // từ database

if (Auth::verifyPassword($entered_password, $hash_from_db)) {
    echo "Mật khẩu đúng";
} else {
    echo "Mật khẩu sai";
}


// -------- VÍ DỤ 8: Manual login --------
// require_once __DIR__ . '/config/Database.php';
// $db = new Database();
// $conn = $db->getConnection();
// 
// $result = Auth::login('user@example.com', 'password123', $conn);
// if ($result['success']) {
//     echo "Đăng nhập thành công";
// } else {
//     echo "Lỗi: " . $result['message'];
// }


// -------- VÍ DỤ 9: Logout --------
// Auth::logout(); // Xóa session và redirect



// ========================================
// HƯỚNG DẪN SỬ DỤNG
// ========================================

/*

1. FORM LOGIN:
   - File: /pages/login.php
   - Form gửi POST tới /auth/login-handler.php
   - Lưu user vào $_SESSION['user']

2. FORM REGISTER:
   - File: /pages/register.php
   - Form gửi POST tới /auth/register-handler.php
   - Tự động hash password
   - Tự động login sau khi đăng ký

3. AUTH HELPER:
   - File: /helpers/Auth.php
   - Cung cấp các hàm tiện ích
   
   Hàm kiểm tra:
   - Auth::isLoggedIn()           // Kiểm tra đã login
   - Auth::isAdmin()              // Kiểm tra là admin
   - Auth::isCustomer()           // Kiểm tra là customer
   - Auth::getCurrentUser()       // Lấy toàn bộ thông tin user
   - Auth::getUserId()            // Lấy ID
   - Auth::getUserEmail()         // Lấy email
   - Auth::getUserName()          // Lấy tên
   - Auth::getUserRole()          // Lấy role
   
   Hàm yêu cầu:
   - Auth::requireLogin()         // Yêu cầu login, nếu không sẽ redirect
   - Auth::requireAdmin()         // Yêu cầu admin, nếu không sẽ báo lỗi 403
   
   Hàm password:
   - Auth::hashPassword()         // Hash password
   - Auth::verifyPassword()       // Verify password
   
   Hàm login/logout:
   - Auth::login()                // Login người dùng
   - Auth::logout()               // Logout người dùng

4. SESSION DATA:
   $_SESSION['user'] = [
       'id'         => int,
       'name'       => string,
       'email'      => string,
       'role'       => string ('customer' hoặc 'admin'),
       'login_time' => timestamp
   ]

5. LOGOUT:
   - File: /auth/logout.php
   - Xóa session và redirect tới login

6. SỬ DỤNG TRONG PAGE:
   session_start();
   require_once 'helpers/Auth.php';
   
   if (Auth::isLoggedIn()) {
       echo "Chào " . Auth::getUserName();
   } else {
       header('Location: pages/login.php');
   }

*/
?>
