<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            width: 250px;
            overflow-y: auto;
        }
        .admin-sidebar h4 {
            margin-bottom: 20px;
            border-bottom: 2px solid #34495e;
            padding-bottom: 10px;
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
        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background-color: #e74c3c;
        }
        .admin-content {
            flex: 1;
            padding: 30px;
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            border: none;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        .stat-card h3 {
            color: #e74c3c;
            margin-top: 10px;
        }
    </style>
    @stack('css')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h4>Shopdo3 Admin</h4>
            <a href="/admin" class="@if(request()->is('admin')) active @endif">Dashboard</a>
            <a href="/admin/categories" class="@if(request()->is('admin/categories*')) active @endif">Danh mục</a>
            <a href="/admin/products" class="@if(request()->is('admin/products*')) active @endif">Sản phẩm</a>
            <a href="/admin/orders" class="@if(request()->is('admin/orders*')) active @endif">Đơn hàng</a>
            <a href="/admin/users" class="@if(request()->is('admin/users*')) active @endif">Người dùng</a>
            <hr style="border-color: #555;">
            <a href="/">Về trang chủ</a>
            <form method="POST" action="/logout" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Đăng xuất</button>
            </form>
        </div>

        <!-- Content -->
        <div class="admin-content">
            <!-- Navbar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>@yield('title')</h1>
                <span>{{ auth()->user()->name }}</span>
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

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>
