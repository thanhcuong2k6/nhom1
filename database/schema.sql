-- FashionHub Database Schema
-- ============================================

-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS `shopdo_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `shopdo_db`;

-- Bảng danh mục sản phẩm
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `image` VARCHAR(255),
  `active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng sản phẩm
CREATE TABLE IF NOT EXISTS `products` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng người dùng
CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đơn hàng
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đánh giá sản phẩm
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `title` VARCHAR(255),
  `comment` TEXT,
  `approved` TINYINT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng giỏ hàng
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `session_id` VARCHAR(255),
  `product_id` INT NOT NULL,
  `quantity` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng thanh toán
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `method` VARCHAR(50),
  `transaction_id` VARCHAR(255),
  `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng mã giảm giá / Voucher
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `min_amount` DECIMAL(10, 2) DEFAULT 0,
  `max_uses` INT DEFAULT NULL,
  `used_count` INT DEFAULT 0,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chỉ mục tìm kiếm
CREATE INDEX idx_category_id ON products(category_id);
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_order_user_id ON orders(user_id);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_product_slug ON products(slug);
CREATE INDEX idx_user_created ON users(created_at);
CREATE INDEX idx_order_created ON orders(created_at);
CREATE INDEX idx_voucher_code ON vouchers(code);

-- Insert dữ liệu mẫu
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES 
('Điện thoại', 'dien-thoai', 'Các điện thoại thông minh mới nhất'),
('Máy tính bảng', 'may-tinh-bang', 'Máy tính bảng cao cấp'),
('Laptop', 'laptop', 'Laptop và ultrabook'),
('Phụ kiện', 'phu-kien', 'Phụ kiện điện thoại và máy tính'),
('Đồng hồ thông minh', 'dong-ho-thong-minh', 'Đồng hồ và vòng đeo');

INSERT INTO `products` (`name`, `slug`, `description`, `price`, `category_id`, `stock`, `active`) VALUES 
('iPhone 15 Pro', 'iphone-15-pro', 'Điện thoại iPhone 15 Pro với camera 48MP và chip A17 Pro', 29999000, 1, 10, 1),
('Samsung Galaxy S24', 'samsung-galaxy-s24', 'Điện thoại Samsung Galaxy S24 với màn hình AMOLED 6.2 inch', 22999000, 1, 15, 1),
('iPad Air', 'ipad-air', 'Máy tính bảng Apple iPad Air với chip M1', 15999000, 2, 8, 1),
('MacBook Pro 14', 'macbook-pro-14', 'Laptop MacBook Pro 14 inch với chip M3 Max', 32999000, 3, 5, 1),
('AirPods Pro', 'airpods-pro', 'Tai nghe không dây AirPods Pro gen 2', 6999000, 4, 20, 1);

-- Tài khoản admin mẫu (password: admin123)
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `active`) VALUES 
('Admin', 'admin@fashionhub.local', '0123456789', '$2y$10$dXJ3U.rPjM7V3jWZwIZUVeZ3L8f5N.tS8x3pZqU1K0qH0Y9Z0Y0Zm', 'admin', 1),
('Khách hàng', 'customer@example.com', '0987654321', '$2y$10$dXJ3U.rPjM7V3jWZwIZUVeZ3L8f5N.tS8x3pZqU1K0qH0Y9Z0Y0Zm', 'customer', 1);
