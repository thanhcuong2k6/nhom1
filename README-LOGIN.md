# Hệ Thống Login PHP Thuần

Tôi đã tạo hệ thống login PHP thuần sử dụng session và password_verify. Dưới đây là hướng dẫn đầy đủ.

## 📁 Cấu Trúc File

```
shopdo/
├── auth/
│   ├── login-handler.php       # Xử lý logic login
│   ├── logout.php              # Xử lý logout
│   ├── register-handler.php    # Xử lý register
├── pages/
│   ├── login.php               # Form đăng nhập
│   ├── register.php            # Form đăng ký
├── helpers/
│   └── Auth.php                # Helper class
└── USAGE_LOGIN.php             # Ví dụ sử dụng
```

---

## 🔐 Cách Hoạt Động

### 1. **Login Handler** (`auth/login-handler.php`)

Xử lý form đăng nhập:
- Nhận POST request từ form
- Validate email & password
- Tìm user trong database theo email
- Verify password dùng `password_verify()`
- **Lưu user vào `$_SESSION['user']`**
- Redirect nếu thành công/thất bại

```php
// Session được lưu như sau:
$_SESSION['user'] = [
    'id' => 1,
    'name' => 'Nguyễn A',
    'email' => 'user@example.com',
    'role' => 'customer',
    'login_time' => 1234567890
];
```

### 2. **Login Form** (`pages/login.php`)

```html
<form method="POST" action="../auth/login-handler.php">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Đăng nhập</button>
</form>
```

### 3. **Register Handler** (`auth/register-handler.php`)

Xử lý đăng ký tài khoản mới:
- Validate tất cả input
- Hash password với `Auth::hashPassword()`
- Kiểm tra email đã tồn tại
- Insert vào database
- Tự động login sau khi đăng ký

### 4. **Logout** (`auth/logout.php`)

- Xóa `$_SESSION['user']`
- Destroy toàn bộ session
- Redirect về login

---

## 🛠️ Auth Helper Class

File `helpers/Auth.php` cung cấp các hàm tiện ích:

### Kiểm Tra Login

```php
// Kiểm tra đã login chưa
if (Auth::isLoggedIn()) {
    echo "Đã login";
}

// Lấy toàn bộ thông tin user
$user = Auth::getCurrentUser();

// Lấy thông tin riêng
$id = Auth::getUserId();
$email = Auth::getUserEmail();
$name = Auth::getUserName();
$role = Auth::getUserRole();
```

### Kiểm Tra Role

```php
// Kiểm tra admin
if (Auth::isAdmin()) {
    // Admin-only code
}

// Kiểm tra customer
if (Auth::isCustomer()) {
    // Customer-only code
}
```

### Yêu Cầu Login

```php
// Nếu chưa login sẽ redirect tới login.php
Auth::requireLogin();

// Nếu không là admin sẽ báo lỗi 403
Auth::requireAdmin();
```

### Password Hash & Verify

```php
// Hash password
$hashed = Auth::hashPassword('password123');

// Verify password
if (Auth::verifyPassword('password123', $hashed)) {
    echo "Đúng";
}
```

### Login/Logout Programmatically

```php
// Manual login
$result = Auth::login('user@example.com', 'password123', $conn);
if ($result['success']) {
    echo "Login thành công";
}

// Logout
Auth::logout();
```

---

## 📝 Ví Dụ Sử Dụng

### Bảo vệ một page yêu cầu login

```php
<?php
session_start();
require_once 'helpers/Auth.php';

// Yêu cầu user phải login
Auth::requireLogin();

// Lấy thông tin user
$user = Auth::getCurrentUser();
?>

<h1>Chào mừng <?php echo $user['name']; ?></h1>
```

### Hiển thị thông tin user

```php
<?php
session_start();
require_once 'helpers/Auth.php';

if (Auth::isLoggedIn()) {
    echo "Email: " . Auth::getUserEmail();
    echo "Role: " . Auth::getUserRole();
} else {
    echo "Chưa đăng nhập";
}
?>
```

### Admin-only page

```php
<?php
session_start();
require_once 'helpers/Auth.php';

// Chỉ admin mới có thể truy cập
Auth::requireAdmin();

// Admin code ở đây
?>
```

### Thêm link logout

```html
<?php if (Auth::isLoggedIn()): ?>
    <a href="auth/logout.php">Đăng xuất</a>
<?php else: ?>
    <a href="pages/login.php">Đăng nhập</a>
<?php endif; ?>
```

---

## 🔒 Bảo Mật

✅ **Đã thực hiện:**
- ✅ Password hash với BCRYPT (cost=10)
- ✅ SQL Injection protection (dùng prepared statements)
- ✅ XSS protection (dùng htmlspecialchars)
- ✅ Session-based authentication
- ✅ Password verify dùng `password_verify()`

⚠️ **Nên thêm:**
- Password reset functionality
- CSRF token cho form
- Rate limiting cho login attempts
- HTTPS enforcement
- Session timeout
- Remember me functionality

---

## 🚀 Quick Start

### Bước 1: Tạo user để test

Vào `/pages/register.php` để tạo tài khoản mới

### Bước 2: Đăng nhập

Vào `/pages/login.php` để đăng nhập

### Bước 3: Kiểm tra session

```php
<?php
session_start();
var_dump($_SESSION['user']); // Xem thông tin user
?>
```

---

## 📊 Database Schema

Tabbảng `users` có cả trường `password`:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ...
);
```

---

## 🔧 Troubleshooting

### Session không lưu

Đảm bảo gọi `session_start()` **ở đầu file** trước mọi output:

```php
<?php
session_start(); // Phải ở đầu tiên
require_once 'helpers/Auth.php';
?>
```

### Password verify luôn false

Kiểm tra:
- Password được hash đúng cách khi tạo user?
- Dùng `Auth::hashPassword()` để hash
- Database lưu hash đủ dài không? (VARCHAR 255)

### Login redirect không hoạt động

Sự dụng relative path:
```php
header('Location: ../auth/login-handler.php');
```

---

## 📚 Hàm Nhanh

| Hàm | Mô Tả |
|-----|-------|
| `Auth::isLoggedIn()` | Kiểm tra login |
| `Auth::getCurrentUser()` | Lấy user object |
| `Auth::getUserId()` | Lấy ID |
| `Auth::getUserEmail()` | Lấy email |
| `Auth::getUserName()` | Lấy tên |
| `Auth::getUserRole()` | Lấy role |
| `Auth::isAdmin()` | Kiểm tra admin |
| `Auth::isCustomer()` | Kiểm tra customer |
| `Auth::requireLogin()` | Yêu cầu login |
| `Auth::requireAdmin()` | Yêu cầu admin |
| `Auth::hashPassword(pwd)` | Hash password |
| `Auth::verifyPassword(pwd, hash)` | Verify password |
| `Auth::login(email, pwd, conn)` | Manual login |
| `Auth::logout()` | Logout |

---

## 🎯 Session Data Structure

```php
$_SESSION['user'] = [
    'id' => int,              // User ID từ database
    'name' => string,         // Tên đầy đủ
    'email' => string,        // Email
    'role' => string,         // 'customer' hoặc 'admin'
    'login_time' => int       // Unix timestamp
];
```

---

**Tạo bởi:** AI Assistant  
**Ngày tạo:** 2024
