# Hướng dẫn cài đặt FashionHub - Shop Thời Trang Online

## Yêu cầu hệ thống

- **PHP:** 7.4 trở lên
- **MySQL:** 5.7 trở lên hoặc MariaDB
- **Laragon** (hoặc bất kỳ server web nào hỗ trợ PHP)
- **Browser:** Chrome, Firefox, Safari, Edge (phiên bản mới)

## Bước 1: Chuẩn bị môi trường

### Nếu sử dụng Laragon:

1. Tải và cài đặt Laragon từ https://laragon.org
2. Khởi động Laragon
3. Nhấp "Start All" để khởi động Apache và MySQL

### Nếu sử dụng XAMPP:

1. Tải và cài đặt XAMPP từ https://www.apachefriends.org
2. Đặt dự án trong thư mục `C:\xampp\htdocs\`
3. Khởi động Apache và MySQL từ Control Panel

## Bước 2: Tải dự án

1. Tải dự án hoặc clone từ repository
2. Giải nén vào thư mục `www` (Laragon) hoặc `htdocs` (XAMPP)
3. Đảm bảo thư mục tên là `shopdo`

```
Laragon: d:\laragon\www\shopdo
XAMPP:   C:\xampp\htdocs\shopdo
```

## Bước 3: Cấu hình file .env

1. Mở file `.env` trong thư mục gốc dự án
2. Kiểm tra và cập nhật cấu hình:

```env
# DATABASE CONFIGURATION
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=        # Để trống nếu không có mật khẩu
DB_NAME=shopdo_db
DB_PORT=3306

# APP CONFIGURATION
APP_URL=http://localhost/shopdo
```

### Tìm port MySQL:
- **Laragon**: Mặc định 3306
- **XAMPP**: Mặc định 3306
- Kiểm tra trong `config/my.ini` hoặc `my.cnf`

## Bước 4: Tạo cơ sở dữ liệu

### Cách 1: Sử dụng phpMyAdmin

1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Đăng nhập (user: `root`, password: rỗng)
3. Nhấp **New** để tạo database mới
4. Đặt tên là `shopdo_db`
5. Chọn **Create**
6. Đi tới tab **Import**
7. Chọn file `d:\laragon\www\shopdo\database\schema.sql`
8. Nhấp **Import**

### Cách 2: Sử dụng MySQL command line

```bash
# Mở Command Prompt hoặc Terminal
# Điều hướng tới thư mục MySQL
cd d:\laragon\bin\mysql\mysql5.7.4-win32\bin

# Tạo database
mysql -u root < d:\laragon\www\shopdo\database\schema.sql

# Hoặc nếu có mật khẩu
mysql -u root -p < d:\laragon\www\shopdo\database\schema.sql
```

## Bước 5: Cấp quyền thư mục

Đảm bảo thư mục `uploads/` có quyền write:

### Trên Windows:

1. Chuột phải vào thư mục `uploads`
2. Chọn **Properties**
3. Chọn tab **Security**
4. Nhấp **Edit**
5. Chọn **Users** hoặc **Everyone**
6. Đánh dấu **Full Control**
7. Nhấp **OK**

## Bước 6: Khởi động ứng dụng

1. Mở trình duyệt web
2. Nhập URL: `http://localhost/shopdo`
3. Bạn sẽ thấy trang chủ FashionHub

## Tài khoản mặc định

### Admin:
```
Email:    admin@fashionhub.local
Password: admin123
```

### Khách hàng:
```
Email:    customer@example.com
Password: admin123
```

## Khắc phục sự cố

### Lỗi: "File .env không tồn tại"

**Giải pháp:**
- Kiểm tra file `.env` có trong thư mục gốc
- Nếu không có, sao chép file `.env.example` và đổi tên thành `.env`

### Lỗi: "Kết nối cơ sở dữ liệu thất bại"

**Giải pháp:**
1. Kiểm tra MySQL đang chạy
2. Kiểm tra thông tin cơ sở dữ liệu trong `.env`
3. Tạo database `shopdo_db` nếu chưa có
4. Import file `database/schema.sql`

### Lỗi: "Call to undefined function"

**Giải pháp:**
- Đảm bảo tất cả file model được require chính xác
- Kiểm tra đường dẫn tệp trong các include statement

### Ảnh không hiển thị

**Giải pháp:**
1. Tạo thư mục `uploads/` nếu chưa có
2. Cấp quyền write cho thư mục `uploads/`
3. Đảm bảo đường dẫn ảnh chính xác

### Session không hoạt động

**Giải pháp:**
1. Đảm bảo `session_start()` được gọi ở đầu file
2. Kiểm tra cấu hình session trong `php.ini`:
   ```
   session.save_path = "/tmp"
   session.use_cookies = 1
   ```

### Lỗi 403 Forbidden

**Giải pháp:**
- Kiểm tra quyền truy cập thư mục
- Thử cấp quyền 755 cho thư mục

### Website quá chậm

**Giải pháp:**
1. Kiểm tra kết nối cơ sở dữ liệu
2. Tối ưu hóa truy vấn SQL
3. Thêm index vào database
4. Sử dụng cache

## Phát triển tiếp theo

### Thêm sản phẩm mới

```sql
INSERT INTO products (name, description, price, category_id, stock, active) 
VALUES ('Sản phẩm mới', 'Mô tả sản phẩm', 1000000, 1, 10, 1);
```

### Tạo tài khoản admin mới

```bash
# Tính hash mật khẩu
php -r "echo password_hash('password123', PASSWORD_BCRYPT);"
```

```sql
INSERT INTO users (name, email, password, role, active) 
VALUES ('Admin mới', 'admin2@fashionhub.local', '$2y$10$...', 'admin', 1);
```

### Sửa thông tin trang web

- Sửa tên: File `.env` - `APP_NAME`
- Sửa URL: File `.env` - `APP_URL`
- Sửa thông tin liên hệ: File `includes/footer.php`

## Mẹo bảo mật

1. **Đổi mật khẩu admin mặc định** ngay lập tức
2. **Đặt `APP_DEBUG=false`** trong `.env` trên production
3. **Kiểm tra dữ liệu đầu vào** trước khi lưu vào database
4. **Sử dụng HTTPS** trên production
5. **Giữ PHP cập nhật** lên phiên bản mới nhất
6. **Sử dụng prepared statements** để tránh SQL injection
7. **Giới hạn quyền truy cập** file upload

## Hỗ trợ thêm

- Tham khảo `README.md` để biết thêm thông tin
- Kiểm tra `config/Config.php` để hiểu cấu hình
- Xem các model trong `models/` để học cách xử lý dữ liệu
- Tham khảo API endpoints trong `api/` folder

## Liên hệ & Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra file log
2. Mở Developer Tools (F12) để xem lỗi
3. Kiểm tra console PHP/MySQL

---

**Version:** 1.0.0  
**Last Updated:** 2024-2026
