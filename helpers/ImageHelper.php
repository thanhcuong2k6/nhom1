<?php
/**
 * FashionHub - Image Helper
 * Xử lý hiển thị ảnh sản phẩm từ public/images/products/
 * Với fallback tới public/images/no-image.jpg
 */

class ImageHelper {
    
    /**
     * Lấy đường dẫn ảnh sản phẩm
     * 
     * @param string $image Tên file ảnh từ database
     * @return string Đường dẫn đầy đủ đến ảnh
     */
    public static function getProductImage($image) {
        $baseUrl = rtrim(Config::get('APP_URL'), '/');
        $noImageUrl = $baseUrl . '/public/images/no-image.jpg';
        
        // Kiểm tra nếu image rỗng
        if (empty($image) || trim($image) === '') {
            return $noImageUrl;
        }
        
        // Kiểm tra file ảnh có tồn tại không
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/public/images/products/' . basename($image);
        
        if (file_exists($imagePath)) {
            return $baseUrl . '/public/images/products/' . basename($image);
        }
        
        // Nếu file không tồn tại, return ảnh mặc định
        return $noImageUrl;
    }
    
    /**
     * Hiển thị thẻ img sản phẩm
     * 
     * @param string $image Tên file ảnh
     * @param string $alt Text thay thế
     * @param string $class CSS class name
     * @return string HTML img tag
     */
    public static function renderProductImage($image, $alt = 'Ảnh sản phẩm', $class = '') {
        $imagePath = self::getProductImage($image);
        $alt = htmlspecialchars($alt);
        $class = htmlspecialchars($class);
        
        return "<img src=\"$imagePath\" alt=\"$alt\" class=\"$class\" loading=\"lazy\" />";
    }
    
    /**
     * Lấy URL ảnh mặc định
     * 
     * @return string
     */
    public static function getNoImageUrl() {
        $baseUrl = rtrim(Config::get('APP_URL'), '/');
        return $baseUrl . '/public/images/no-image.jpg';
    }
    
    /**
     * Kiểm tra file ảnh có tồn tại không
     * 
     * @param string $image
     * @return bool
     */
    public static function imageExists($image) {
        if (empty($image) || trim($image) === '') {
            return false;
        }
        
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/public/images/products/' . basename($image);
        return file_exists($imagePath);
    }
    
    /**
     * Upload ảnh sản phẩm
     * 
     * @param array $file $_FILES['image']
     * @return array ['success' => bool, 'filename' => string, 'error' => string]
     */
    public static function uploadProductImage($file) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/products/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        
        // Kiểm tra file upload
        if (!isset($file['tmp_name']) || !isset($file['name'])) {
            return ['success' => false, 'error' => 'File không hợp lệ'];
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Kiểm tra type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ hỗ trợ JPEG, PNG, GIF, WebP'];
        }
        
        // Kiểm tra size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File tối đa 5MB'];
        }
        
        // Tạo tên file mới
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        // Move file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'error' => 'Không thể upload file'];
    }
    
    // ===== LEGACY METHODS (for backward compatibility) =====
    
    public static function getImageUrl($imagePath = null) {
        if (!empty($imagePath) && file_exists(__DIR__ . '/../uploads/' . $imagePath)) {
            return Config::get('APP_URL') . '/uploads/' . $imagePath;
        }
        return self::getPlaceholder();
    }

    public static function getPlaceholder($width = 250, $height = 250, $text = 'Không có ảnh') {
        return "https://ui-avatars.com/api/?name=" . urlencode('FashionHub') . "&size=$width&background=random";
    }

    public static function getSvgPlaceholder($width = 250, $height = 250) {
        $svg = <<<SVG
        <svg width="$width" height="$height" xmlns="http://www.w3.org/2000/svg">
            <rect width="$width" height="$height" fill="#f0f0f0"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" 
                  font-family="Arial, sans-serif" font-size="16" fill="#999">
                Không có ảnh
            </text>
        </svg>
        SVG;
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function img($src, $alt = '', $class = '') {
        $imageUrl = self::getImageUrl($src);
        $alt = htmlspecialchars($alt);
        $class = htmlspecialchars($class);
        return "<img src=\"$imageUrl\" alt=\"$alt\" class=\"$class\" onerror=\"this.src='" . self::getSvgPlaceholder() . "'\">";
    }
}
?>
