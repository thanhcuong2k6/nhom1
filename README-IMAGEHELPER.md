# 📸 ImageHelper - Hệ thống ảnh sản phẩm FashionHub

## 🎯 Mục đích

Giải pháp hoàn chỉnh để hiển thị ảnh sản phẩm với:
- ✅ Tự động fallback khi ảnh không có hoặc file missing
- ✅ Lazy loading tự động
- ✅ Upload ảnh với validation
- ✅ Cấu trúc thư mục chuẩn: `public/images/products/`

## 🚀 Quick Start (30 giây)

### 1️⃣ Setup tự động
```bash
php setup-imagehelper.php
```
Điều này sẽ:
- Tạo thư mục `public/images/products/`
- Tạo ảnh mặc định `public/images/no-image.jpg`
- Kiểm tra cấu hình

### 2️⃣ Dùng trong View (1 dòng code)
```php
<?php require_once 'helpers/ImageHelper.php'; ?>

<!-- Hiển thị ảnh sản phẩm -->
<?php echo ImageHelper::renderProductImage(
    $product['image'], 
    $product['name'], 
    'product-thumb'
); ?>

<!-- Output: -->
<!-- <img src="/public/images/products/product_001.jpg" alt="..." loading="lazy" /> -->
<!-- Hoặc nếu ảnh missing: -->
<!-- <img src="/public/images/no-image.jpg" alt="..." loading="lazy" /> -->
```

### 3️⃣ Database
Cột `products.image` lưu **tên file** (không phải URL):
```sql
products.image = 'product_001.jpg'
products.image = NULL  ← Sẽ dùng no-image.jpg
```

## 📂 Cấu trúc thư mục

```
shopdo/
├── public/
│   └── images/
│       ├── products/           ← Upload ảnh vào đây
│       │   ├── product_001.jpg
│       │   ├── product_002.png
│       │   └── ao-nu.webp
│       └── no-image.jpg        ← Ảnh mặc định
├── helpers/
│   └── ImageHelper.php         ← Class chính
├── setup-imagehelper.php       ← Setup tự động
├── QUICK-START-IMAGEHELPER.php ← Docs + ví dụ
├── USAGE_IMAGE_HELPER.php      ← Chi tiết + 5+ ví dụ
└── example-image-helper.php    ← Demo page
```

## 🔧 ImageHelper Reference

### 1. `getProductImage($image)`
Lấy URL ảnh (hoặc fallback)
```php
$url = ImageHelper::getProductImage($product['image']);
// Return: "/public/images/products/product_001.jpg" 
//         hoặc "/public/images/no-image.jpg"
```

### 2. `renderProductImage($image, $alt, $class)`
**[RECOMMENDED]** Trả về thẻ `<img>` hoàn chỉnh với lazy loading
```php
echo ImageHelper::renderProductImage(
    $product['image'],
    'Tên sản phẩm',
    'product-image'
);
// Output: <img src="..." alt="..." class="product-image" loading="lazy" />
```

### 3. `imageExists($image)`
Kiểm tra ảnh có tồn tại không
```php
if (ImageHelper::imageExists($product['image'])) {
    // Ảnh tồn tại
    $badge = '✓ Có ảnh';
} else {
    // Sẽ dùng no-image.jpg
    $badge = '⚠ Mặc định';
}
```

### 4. `uploadProductImage($file)`
Upload ảnh từ form với validation
```php
if ($_POST && isset($_FILES['image'])) {
    $result = ImageHelper::uploadProductImage($_FILES['image']);
    
    if ($result['success']) {
        $filename = $result['filename']; // "product_1234567890.jpg"
        // UPDATE products SET image = ? WHERE id = ?
    } else {
        echo "Lỗi: " . $result['error']; // "File too large", etc
    }
}
```

**Validation:**
- Định dạng: JPEG, PNG, GIF, WebP
- Kích thước max: 5MB

### 5. `getNoImageUrl()`
Lấy URL ảnh fallback
```php
$fallbackUrl = ImageHelper::getNoImageUrl();
// Return: "/public/images/no-image.jpg"
```

## 📋 Ví dụ thực tế

### Danh sách sản phẩm
```php
<?php 
require_once 'helpers/ImageHelper.php';
$products = $db->select('SELECT * FROM products LIMIT 12');
?>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <!-- Cách 1: Render img tag (recommended) -->
            <?php echo ImageHelper::renderProductImage(
                $product['image'],
                htmlspecialchars($product['name']),
                'product-thumb'
            ); ?>
            
            <!-- Hoặc Cách 2: Lấy URL -->
            <img src="<?php echo ImageHelper::getProductImage($product['image']); ?>" />
            
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><?php echo number_format($product['price'], 0, ',', '.'); ?> ₫</p>
        </div>
    <?php endforeach; ?>
</div>
```

### Admin upload ảnh
```php
<?php require_once 'helpers/ImageHelper.php'; ?>

<?php
if ($_POST && isset($_FILES['product_image'])) {
    $result = ImageHelper::uploadProductImage($_FILES['product_image']);
    if ($result['success']) {
        // Cập nhật database
        $db->execute(
            "UPDATE products SET image = ? WHERE id = ?",
            [$result['filename'], $_POST['product_id']]
        );
        echo "✓ Upload thành công!";
    } else {
        echo "✗ Lỗi: " . $result['error'];
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="product_image" accept=".jpg,.png,.gif,.webp" required />
    <button type="submit">Upload</button>
</form>
```

## 🔍 Troubleshooting

| Vấn đề | Nguyên nhân | Giải pháp |
|--------|-----------|----------|
| ❌ Ảnh không hiển thị | Thư mục chưa tạo | Chạy `php setup-imagehelper.php` |
| ❌ Ảnh không hiển thị | no-image.jpg chưa có | Tạo file `public/images/no-image.jpg` (250x250px) |
| ❌ Lỗi "File type not allowed" | File format sai | Chỉ dùng: JPEG, PNG, GIF, WebP |
| ❌ Lỗi "File too large" | File > 5MB | Nén ảnh trước khi upload |
| ⚠️ URL sai | APP_URL chưa config | Thêm vào Config: `Config::set('APP_URL', 'http://localhost');` |

## 📚 Tài liệu chi tiết

1. **[QUICK-START-IMAGEHELPER.php](QUICK-START-IMAGEHELPER.php)** 
   - Hướng dẫn nhanh với 4 ví dụ code

2. **[USAGE_IMAGE_HELPER.php](USAGE_IMAGE_HELPER.php)**
   - Tài liệu đầy đủ + 5+ use cases

3. **[example-image-helper.php](example-image-helper.php)**
   - Demo page hiển thị danh sách sản phẩm
   - Truy cập: `http://localhost/shopdo/example-image-helper.php`

4. **[helpers/ImageHelper.php](helpers/ImageHelper.php)**
   - Source code gốc + comment chi tiết

## ⚙️ Cấu hình

### Config.php
Đảm bảo có:
```php
Config::set('APP_URL', 'http://localhost');
// Hoặc
Config::set('APP_URL', 'http://shopdo.local');
```

### Thư mục
```
public/
├── images/
│   ├── products/           ← Phải tồn tại
│   │   ├── product_001.jpg
│   │   └── ...
│   └── no-image.jpg        ← Bắt buộc
```

## 🎨 CSS dùng với ImageHelper

```css
/* Lazy loading effect */
img[loading="lazy"] {
    transition: opacity 0.3s;
}

/* Fallback image styling */
.product-image img[src*="no-image"] {
    opacity: 0.6;
    filter: grayscale(100%);
}

/* Product image styling */
.product-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
}
```

## 🤝 Migration từ cách cũ

### ❌ CŨ (không tốt)
```html
<img src="/uploads/<?php echo $product['image']; ?>" alt="..." />
```

### ✅ MỚI (tốt)
```php
<?php echo ImageHelper::renderProductImage(
    $product['image'], 
    $product['name']
); ?>
```

**Lợi ích:**
- ✅ Tự động fallback
- ✅ Lazy loading
- ✅ Đường dẫn chuẩn
- ✅ Validation upload
- ✅ Dễ bảo trì

## ✅ Checklist Setup

- [ ] Chạy `php setup-imagehelper.php`
- [ ] Kiểm tra `public/images/products/` tồn tại
- [ ] Kiểm tra `public/images/no-image.jpg` tồn tại
- [ ] Cấu hình `APP_URL` trong Config.php
- [ ] Upload ảnh sản phẩm vào `public/images/products/`
- [ ] Cập nhật database với tên file ảnh
- [ ] Sửa view dùng `ImageHelper::renderProductImage()`
- [ ] Test trên page danh sách sản phẩm
- [ ] Test upload ảnh mới (admin)

## 📞 Support

Nếu có vấn đề:
1. Xem [QUICK-START-IMAGEHELPER.php](QUICK-START-IMAGEHELPER.php) (Ví dụ code)
2. Xem [USAGE_IMAGE_HELPER.php](USAGE_IMAGE_HELPER.php) (Chi tiết + docs)
3. Test page: [example-image-helper.php](example-image-helper.php)
4. Kiểm tra Settings/Config.php
5. Chạy setup lại: `php setup-imagehelper.php`

---

**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** 2024
