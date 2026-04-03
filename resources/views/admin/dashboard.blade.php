@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="fs-3">💰</div>
            <h3>{{ number_format($totalRevenue) }}₫</h3>
            <p class="text-muted">Tổng doanh thu</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="fs-3">📦</div>
            <h3>{{ $totalOrders }}</h3>
            <p class="text-muted">Tổng đơn hàng</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="fs-3">🛍️</div>
            <h3>{{ $totalProducts }}</h3>
            <p class="text-muted">Tổng sản phẩm</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="fs-3">👥</div>
            <h3>{{ $totalUsers }}</h3>
            <p class="text-muted">Tổng người dùng</p>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Đơn hàng gần đây</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-primary">Đang giao</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-success">Đã giao</span>
                                    @endif
                                </td>
                                <td>{{ number_format($order->total) }}₫</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Không có đơn hàng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sản phẩm bán chạy</h5>
            </div>
            <div class="list-group list-group-flush">
                @forelse($topProducts as $product)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $product->name }}</strong>
                            <span class="badge bg-primary">{{ $product->order_items_count }}</span>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted">
                        Chưa có sản phẩm nào
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
