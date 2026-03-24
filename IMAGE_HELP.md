# Hướng dẫn sửa lỗi hình ảnh

## 🐛 Vấn đề
Hình ảnh sản phẩm không hiển thị hoặc hiển thị không đúng.

## ✅ Giải pháp

### Cách 1: Tự động tạo ảnh mẫu (Nhanh nhất)

1. Truy cập: `http://localhost/shopdo/setup-sample-images.php`
2. Hệ thống sẽ tự động tạo ảnh mẫu cho tất cả sản phẩm
3. Quay lại trang chủ để xem kết quả

### Cách 2: Upload ảnh thực qua Admin

1. Truy cập: `http://localhost/shopdo/admin/image-manager.php`
2. Chọn tab **Upload ảnh**
3. Chọn ảnh từ máy tính
4. Chọn sản phẩm để gán ảnh (tùy chọn)
5. Nhấp **Upload ảnh**

**Lưu ý:** 
- Định dạng hỗ trợ: JPG, JPEG, PNG, GIF
- Kích thước tối đa: 5MB

### Cách 3: Upload qua Database

```sql
UPDATE products SET image = 'ten-file-anh.jpg' WHERE id = 1;
```

## 🎨 Cách thêm ảnh sản phẩm

### 1. Tự động tạo ảnh mẫu
```
http://localhost/shopdo/setup-sample-images.php
```

### 2. Upload ảnh qua giao diện admin
```
http://localhost/shopdo/admin/image-manager.php
```

### 3. Upload ảnh qua FTP
- Kết nối FTP tới thư mục `uploads/`
- Upload file ảnh vào thư mục này
- Cập nhật tên file ảnh vào database

### 4. Sao chép ảnh mẫu
- Copy file ảnh từ máy tính
- Paste vào thư mục `uploads/` của website

## 📂 Cấu trúc thư mục uploads

```
shopdo/
└── uploads/
    ├── sample_product_1.svg
    ├── sample_product_2.svg
    ├── product_1234567890_abc123.jpg
    └── ...
```

## 🔧 Kiểm tra xử lý ảnh

Các file đã được cập nhật để hỗ trợ:
- ✅ Placeholder tự động nếu ảnh không tồn tại
- ✅ SVG fallback nếu URL bị lỗi
- ✅ Lazy loading cho hiệu suất tốt hơn

## 💡 Mẹo

1. **Tạo ảnh nhanh**: Sử dụng trang setup ảnh mẫu
2. **Quản lý ảnh dễ**: Sử dụng trang admin image-manager
3. **Ảnh không thay đổi**: Xóa cache browser (Ctrl + F5)
4. **Tối ưu ảnh**: Nén ảnh trước khi upload để tiết kiệm dung lượng

## 📞 Liên hệ hỗ trợ

Nếu vẫn gặp vấn đề:
1. Kiểm tra thư mục `uploads/` có quyền write
2. Kiểm tra tệp ảnh có trong database
3. Xóa cache browser và tải lại trang
4. Kiểm tra lỗi trong console (F12)

---

**Last Updated:** 2024-2026
