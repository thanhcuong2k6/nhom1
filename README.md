# FashionHub - Shop Thời Trang Online

Một ứng dụng website bán quần áo và thời trang trực tuyến hoàn chỉnh được xây dựng bằng PHP, HTML, CSS và JavaScript.

## Tính năng chính

✅ **Quản lý sản phẩm thời trang** - Danh sách quần áo, chi tiết sản phẩm, tìm kiếm theo danh mục
✅ **Giỏ hàng** - Thêm, sửa, xóa sản phẩm quần áo khỏi giỏ hàng
✅ **Thanh toán** - Hỗ trợ COD, chuyển khoản, thẻ tín dụng
✅ **Quản lý đơn hàng** - Theo dõi đơn hàng quần áo
✅ **Tài khoản người dùng** - Đăng ký, đăng nhập, quản lý hồ sơ
✅ **Đánh giá sản phẩm** - Hệ thống đánh giá từ khách hàng
✅ **Danh mục thời trang** - Áo, quần, váy, giày, phụ kiện
✅ **Responsive Design** - Hoạt động tốt trên mọi thiết bị

## Yêu cầu hệ thống

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Laragon** hoặc server web khác hỗ trợ PHP

## Cấu trúc dự án

```
shopdo/
├── config/                 # Tệp cấu hình
│   ├── Config.php         # Quản lý cấu hình .env
│   └── Database.php       # Kết nối cơ sở dữ liệu
├── models/                # Các lớp mô hình
│   ├── Product.php        # Mô hình sản phẩm
│   └── Cart.php           # Mô hình giỏ hàng
├── pages/                 # Các trang website
│   ├── products.php       # Danh sách sản phẩm
│   ├── product-detail.php # Chi tiết sản phẩm
│   ├── cart.php           # Trang giỏ hàng
│   └── checkout.php       # Trang thanh toán
├── api/                   # API endpoints
│   ├── add-to-cart.php
│   ├── update-cart.php
│   ├── remove-from-cart.php
│   └── clear-cart.php
├── includes/              # Các file include
│   ├── header.php         # Header
│   └── footer.php         # Footer
├── css/                   # Stylesheet
│   └── style.css          # CSS chính
├── js/                    # JavaScript
│   └── main.js            # Script chính
├── database/              # Tệp cơ sở dữ liệu
│   └── schema.sql         # Schema cơ sở dữ liệu
├── uploads/               # Thư mục upload ảnh
├── .env                   # Biến môi trường
└── index.php              # Trang chủ
```

## Cài đặt

### 1. Clone hoặc tải dự án
```bash
# Nếu sử dụng Laragon, đặt dự án trong thư mục www
# d:\laragon\www\shopdo
```

### 2. Cấu hình file .env
```bash
# Chỉnh sửa file .env với thông tin cơ sở dữ liệu của bạn
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=shopdo_db

# Thông tin ứng dụng thời trang
APP_NAME=FashionHub
ADMIN_EMAIL=admin@fashionhub.local
```

### 3. Tạo cơ sở dữ liệu
```bash
# Mở phpMyAdmin hoặc MySQL command line
# Chạy tệp schema.sql
mysql -u root < database/schema.sql

# Hoặc import file SQL qua phpMyAdmin
```

### 4. Cập nhật file .env
Đảm bảo các cài đặt trong file `.env` khớp với cấu hình của bạn:
- `APP_URL` = URL của ứng dụng
- `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`
- Các tùy chọn khác

### 5. Truy cập ứng dụng
```
http://localhost/shopdo
```

## Tài khoản mặc định

**Admin:**
- Email: `admin@fashionhub.local`
- Mật khẩu: `admin123`

**Khách hàng:**
- Email: `customer@example.com`
- Mật khẩu: `admin123`

## Cách sử dụng

### Trang chủ
- Xem sản phẩm nổi bật
- Tìm kiếm sản phẩm
- Xem các tính năng nổi bật

### Danh sách sản phẩm
- Xem tất cả sản phẩm
- Phân trang (12 sản phẩm/trang)
- Thêm sản phẩm vào giỏ hàng

### Chi tiết sản phẩm
- Xem thông tin chi tiết
- Xem hình ảnh sản phẩm
- Chọn số lượng
- Thêm vào giỏ hàng

### Giỏ hàng
- Xem các sản phẩm đã thêm
- Cập nhật số lượng
- Xóa sản phẩm
- Xem tóm tắt giá tiền
- Thanh toán

## API Endpoints

### Giỏ hàng
- `POST /api/add-to-cart.php` - Thêm sản phẩm vào giỏ hàng
- `POST /api/update-cart.php` - Cập nhật số lượng
- `POST /api/remove-from-cart.php` - Xóa sản phẩm
- `POST /api/clear-cart.php` - Xóa toàn bộ giỏ hàng
- `GET /api/get-cart-count.php` - Lấy số lượng giỏ hàng

## Cấu hình thêm

### Thêm sản phẩm mới (qua database)
```sql
INSERT INTO products (name, description, price, category_id, stock, active) 
VALUES ('Tên sản phẩm', 'Mô tả', 1000000, 1, 10, 1);
```

### Tạo danh mục mới
```sql
INSERT INTO categories (name, slug, description) 
VALUES ('Danh mục mới', 'danh-muc-moi', 'Mô tả danh mục');
```

## Phát triển

### Thêm tính năng mới
1. Tạo model trong `models/`
2. Tạo trang hoặc API trong `pages/` hoặc `api/`
3. Thêm route vào header.php

### Tùy chỉnh CSS
- Chỉnh sửa `css/style.css`
- Hoặc thêm file CSS mới

### Mở rộng JavaScript
- Thêm hàm vào `js/main.js`
- Hoặc tạo file JS riêng

## Bảo mật

- Luôn kiểm tra và validate dữ liệu đầu vào
- Sử dụng prepared statements để tránh SQL injection
- Mã hóa mật khẩu trước khi lưu
- Đặt `APP_DEBUG=false` trên production
- Giới hạn quyền truy cập file upload

## Troubleshooting

### "File .env không tồn tại"
- Đảm bảo file `.env` tồn tại trong thư mục gốc dự án

### "Kết nối cơ sở dữ liệu thất bại"
- Kiểm tra thông tin cơ sở dữ liệu trong `.env`
- Đảm bảo MySQL đang chạy
- Tạo cơ sở dữ liệu nếu chưa có

### Ảnh không hiển thị
- Tạo thư mục `uploads/` nếu chưa có
- Đặt quyền write cho thư mục `uploads/`
- Đảm bảo đường dẫn ảnh chính xác

### Session không hoạt động
- Đảm bảo `session_start()` được gọi ở đầu file
- Kiểm tra cài đặt session trong `php.ini`

## Hỗ trợ

Nếu bạn gặp vấn đề, vui lòng:
1. Kiểm tra file log
2. Kiểm tra console browser (F12)
3. Đảm bảo tất cả yêu cầu hệ thống được đáp ứng

## Giấy phép

Dự án này được cung cấp miễn phí cho mục đích học tập và sử dụng cá nhân.

## Tác giả

FashionHub - Xây dựng bởi [Tên của bạn] | Chuyên về thời trang và quần áo

---

**Phiên bản:** 1.0.0  
**Cập nhật lần cuối:** 2024-2026  
**Trạng thái:** Đang phát triển
