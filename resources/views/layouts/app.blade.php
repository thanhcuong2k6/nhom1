<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Shopdo3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: #e74c3c !important;
        }
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .product-card {
            height: 100%;
        }
        .product-image {
            height: 250px;
            object-fit: cover;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.3rem;
        }
        .btn-primary {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        .admin-sidebar {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }
        .admin-sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: background-color 0.3s ease;
        }
        .admin-sidebar a:hover {
            background-color: #34495e;
        }
        .rating {
            color: #f39c12;
        }
    </style>
    @stack('css')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Shopdo3</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="bi bi-cart"></i> Giỏ hàng
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="/admin">Quản trị</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="/orders">Đơn hàng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/logout" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Đăng ký</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Search Bar -->
        <div class="row mt-3 mb-3">
            <div class="col-md-6 offset-md-3">
                <form action="/search" method="get" class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Tìm sản phẩm...">
                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Có lỗi xảy ra!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="container-fluid">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Về Shopdo3</h5>
                    <p>Cửa hàng bán hàng online uy tín, cung cấp các sản phẩm chất lượng cao.</p>
                </div>
                <div class="col-md-3">
                    <h5>Thông tin</h5>
                    <ul style="list-style: none; padding-left: 0;">
                        <li><a href="#" style="color: white; text-decoration: none;">Về chúng tôi</a></li>
                        <li><a href="#" style="color: white; text-decoration: none;">Chính sách</a></li>
                        <li><a href="#" style="color: white; text-decoration: none;">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Hỗ trợ</h5>
                    <ul style="list-style: none; padding-left: 0;">
                        <li><a href="#" style="color: white; text-decoration: none;">Trợ giúp</a></li>
                        <li><a href="#" style="color: white; text-decoration: none;">Câu hỏi thường gặp</a></li>
                        <li><a href="#" style="color: white; text-decoration: none;">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Theo dõi</h5>
                    <a href="#" style="color: white; margin-right: 10px;">Facebook</a>
                    <a href="#" style="color: white;">Twitter</a>
                </div>
            </div>
            <hr style="border-color: #555;">
            <div class="text-center">
                <p>&copy; 2024 Shopdo3. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>
