<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>FashionHub - Shop Thời Trang Online</title>
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="<?php echo Config::get('APP_URL'); ?>" class="logo">
                    <i class="fas fa-shirt"></i> FashionHub
                </a>
            </div>
            
            <div class="navbar-menu">
                <ul class="navbar-items">
                    <li><a href="<?php echo Config::get('APP_URL'); ?>">Trang chủ</a></li>
                    <li><a href="<?php echo Config::get('APP_URL'); ?>pages/products.php">Sản phẩm</a></li>
                    <li><a href="<?php echo Config::get('APP_URL'); ?>pages/about.php">Về chúng tôi</a></li>
                    <li><a href="<?php echo Config::get('APP_URL'); ?>pages/contact.php">Liên hệ</a></li>
                </ul>
            </div>

            <div class="navbar-right">
                <div class="search-box">
                    <form method="GET" action="<?php echo Config::get('APP_URL'); ?>/pages/search.php">
                        <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." required>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <a href="<?php echo Config::get('APP_URL'); ?>/pages/cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php 
                        echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; 
                    ?></span>
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-menu">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <?php echo isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Tài khoản'; ?>
                        </a>
                        <div class="dropdown">
                            <a href="<?php echo Config::get('APP_URL'); ?>/pages/profile.php">Hồ sơ</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>/pages/orders.php">Đơn hàng</a>
                            <a href="<?php echo Config::get('APP_URL'); ?>/auth/logout.php">Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo Config::get('APP_URL'); ?>/pages/login.php" class="btn-login">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
