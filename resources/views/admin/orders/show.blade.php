@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Thông tin đơn hàng {{ $order->order_number }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Khách hàng:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
                <p><strong>Người nhận:</strong> {{ $order->full_name }}</p>
                <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}, {{ $order->province }}, {{ $order->postal_code }}</p>
                <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5>Sản phẩm</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->price) }}₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price * $item->quantity) }}₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Status -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Trạng thái</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                    @csrf
                    <select class="form-select mb-2" name="status">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                </form>
            </div>
        </div>

        <!-- Payment -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Thanh toán</h5>
            </div>
            <div class="card-body">
                <p><strong>Phương thức:</strong> {{ $order->payment_method }}</p>
                <form method="POST" action="{{ route('admin.orders.update-payment', $order->id) }}">
                    @csrf
                    <select class="form-select mb-2" name="payment_status">
                        <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="card">
            <div class="card-header">
                <h5>Tóm tắt</h5>
            </div>
            <div class="card-body">
                <p class="d-flex justify-content-between">
                    <span>Tổng tiền hàng:</span>
                    <strong>{{ number_format($order->subtotal) }}₫</strong>
                </p>
                <p class="d-flex justify-content-between">
                    <span>Phí vận chuyển:</span>
                    <strong>{{ number_format($order->shipping_fee) }}₫</strong>
                </p>
                @if($order->discount_amount > 0)
                    <p class="d-flex justify-content-between">
                        <span>Giảm giá:</span>
                        <strong class="text-danger">-{{ number_format($order->discount_amount) }}₫</strong>
                    </p>
                @endif
                <hr>
                <p class="d-flex justify-content-between">
                    <strong>Tổng cộng:</strong>
                    <strong style="color: #e74c3c;">{{ number_format($order->total) }}₫</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
