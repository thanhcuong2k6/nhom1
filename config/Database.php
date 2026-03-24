<?php
/**
 * FashionHub - Lớp kết nối cơ sở dữ liệu
 */

require_once __DIR__ . '/Config.php';

class Database {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct() {
        $this->host = Config::get('DB_HOST', 'localhost');
        $this->user = Config::get('DB_USER', 'root');
        $this->password = Config::get('DB_PASSWORD', '');
        $this->database = Config::get('DB_NAME', 'shopdo_db');
        
        $this->connect();
    }

    public function connect() {
        // Kết nối không có database trước
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->password === '' ? null : $this->password
        );

        if ($this->conn->connect_error) {
            die('Kết nối server MySQL thất bại: ' . $this->conn->connect_error);
        }

        // Kiểm tra và tạo database nếu không tồn tại
        $this->createDatabaseIfNotExists();

        // Kết nối lại với database đã tạo
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->password === '' ? null : $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            die('Kết nối cơ sở dữ liệu thất bại: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset('utf8mb4');
    }

    private function createDatabaseIfNotExists() {
        // Tạo database nếu không tồn tại
        $sql = "CREATE DATABASE IF NOT EXISTS `" . $this->conn->real_escape_string($this->database) . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if (!$this->conn->query($sql)) {
            die('Lỗi tạo database: ' . $this->conn->error);
        }

        // Chọn database
        if (!$this->conn->select_db($this->database)) {
            die('Lỗi chọn database: ' . $this->conn->error);
        }

        // Tạo bảng nếu không tồn tại
        $this->createTablesIfNotExist();
    }

    private function createTablesIfNotExist() {
        // Bảng categories
        $sql = "CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL UNIQUE,
            `slug` VARCHAR(255) NOT NULL UNIQUE,
            `description` TEXT,
            `image` VARCHAR(255),
            `active` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng products
        $sql = "CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NOT NULL UNIQUE,
            `description` LONGTEXT,
            `price` DECIMAL(10, 2) NOT NULL,
            `category_id` INT NOT NULL,
            `image` VARCHAR(255),
            `stock` INT DEFAULT 0,
            `active` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng users
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `phone` VARCHAR(20),
            `password` VARCHAR(255) NOT NULL,
            `address` TEXT,
            `city` VARCHAR(255),
            `country` VARCHAR(255),
            `postal_code` VARCHAR(20),
            `role` ENUM('customer', 'admin') DEFAULT 'customer',
            `active` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng orders
        $sql = "CREATE TABLE IF NOT EXISTS `orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT,
            `order_number` VARCHAR(50) NOT NULL UNIQUE,
            `total_amount` DECIMAL(10, 2) NOT NULL,
            `tax_amount` DECIMAL(10, 2) DEFAULT 0,
            `shipping_amount` DECIMAL(10, 2) DEFAULT 0,
            `discount_amount` DECIMAL(10, 2) DEFAULT 0,
            `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            `payment_method` VARCHAR(50),
            `payment_status` ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
            `shipping_address` TEXT,
            `notes` TEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng order_items
        $sql = "CREATE TABLE IF NOT EXISTS `order_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_id` INT NOT NULL,
            `product_id` INT,
            `product_name` VARCHAR(255) NOT NULL,
            `price` DECIMAL(10, 2) NOT NULL,
            `quantity` INT NOT NULL,
            `subtotal` DECIMAL(10, 2) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng reviews
        $sql = "CREATE TABLE IF NOT EXISTS `reviews` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `user_id` INT,
            `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            `title` VARCHAR(255),
            `comment` TEXT,
            `approved` TINYINT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Bảng payments
        $sql = "CREATE TABLE IF NOT EXISTS `payments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_id` INT,
            `amount` DECIMAL(10, 2) NOT NULL,
            `method` VARCHAR(50),
            `transaction_id` VARCHAR(255),
            `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->conn->query($sql);

        // Insert dữ liệu mẫu nếu categories trống
        $result = $this->conn->query("SELECT COUNT(*) as count FROM `categories`");
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $this->insertSampleData();
        }
    }

    private function insertSampleData() {
        // Insert categories - Danh mục quần áo
        $sql = "INSERT INTO `categories` (`name`, `slug`, `description`) VALUES 
                ('Áo nam', 'ao-nam', 'Áo sơ mi, áo thun, áo polo và các loại áo nam khác'),
                ('Áo nữ', 'ao-nu', 'Áo sơ mi, áo phông, áo dây và các loại áo nữ đa dạng'),
                ('Quần nam', 'quan-nam', 'Quần tây, quần jean, quần kaki cho nam'),
                ('Quần nữ', 'quan-nu', 'Quần jean, quần dài, quần short cho nữ'),
                ('Váy công sở', 'vay-cong-so', 'Váy chữ A, váy suông, váy midi công sở'),
                ('Đầm dạ hội', 'dam-da-hoi', 'Đầm lịch lãm, đầm dự tiệc, đầm kỹ niệm'),
                ('Giày', 'giay', 'Giày sneaker, giày cao gót, giày đang đôi'),
                ('Phụ kiện', 'phu-kien', 'Túi xách, ví, dây lưng, nón, khăn quàng'),
                ('Đồ lót & tất', 'do-lot-tat', 'Áo lót, quần lót, tất chân'),
                ('Áo khoác & cardigan', 'ao-khoac', 'Áo khoác, blazer, cardigan ấm áp')";
        $this->conn->query($sql);

        // Insert products - Sản phẩm mẫu quần áo
        $sql = "INSERT INTO `products` (`name`, `slug`, `description`, `price`, `category_id`, `stock`, `active`) VALUES 
                ('Áo Sơ Mi Nam Oxford Xanh', 'ao-so-mi-nam-oxford-xanh', 'Áo sơ mi nam chất liệu Oxford cao cấp, thoáng mát, phù hợp công sở', 299000, 1, 25, 1),
                ('Áo Thun Nam Logo Tay Lỡ', 'ao-thun-nam-logo-tay-lo', 'Áo thun nam basic với logo in độc đáo, vải 100% cotton', 149000, 1, 35, 1),
                ('Áo Phông Nữ Trắng Tay Ngắn', 'ao-phong-nu-trang-tay-ngan', 'Áo phông nữ trắng tay ngắn, vải mượt mềm, dễ phối đồ', 129000, 2, 30, 1),
                ('Áo Sơ Mi Nữ Hồng Pastel', 'ao-so-mi-nu-hong-pastel', 'Áo sơ mi nữ màu hồng pastel dịu dàng, kiểu dáng thanh lịch', 349000, 2, 20, 1),
                ('Quần Jean Nam Xanh Đậm', 'quan-jean-nam-xanh-dam', 'Quần jean nam chất liệu cao cấp, màu xanh đậm sang trọng', 449000, 3, 28, 1),
                ('Quần Tây Nam Đen Công Sở', 'quan-tay-nam-den-cong-so', 'Quần tây nam màu đen, thích hợp cho công sở và sự kiện', 599000, 3, 15, 1),
                ('Quần Jean Nữ Xanh Nhạt', 'quan-jean-nu-xanh-nhat', 'Quần jean nữ xanh nhạt, kiểu dáng skinny ôm vừa vặn', 389000, 4, 32, 1),
                ('Quần Short Đùi Nữ Đen', 'quan-short-dui-nu-den', 'Quần short đùi nữ màu đen, chất vải cotton thoáng mát', 199000, 4, 40, 1),
                ('Váy Chữ A Nữ Xám Cổ Điển', 'vay-chu-a-nu-xam-co-dien', 'Váy chữ A nữ màu xám cổ điển, phù hợp công sở', 599000, 5, 18, 1),
                ('Đầm Midi Nữ Họa Tiết Hoa', 'dam-midi-nu-hoa-tiet-hoa', 'Đầm midi nữ họa tiết hoa, dáng xòe thanh lịch', 699000, 6, 12, 1)";
        $this->conn->query($sql);
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function affectedRows() {
        return $this->conn->affected_rows;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
