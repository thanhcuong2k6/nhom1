@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <h2>Chi tiết đơn hàng {{ $order->order_number }}</h2>

        <div class="row mt-4">
            <!-- Order Status -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Trạng thái đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="status-step @if(in_array($order->status, ['pending', 'processing', 'shipped', 'delivered'])) active @endif">
                                    <div class="step-circle">1</div>
                                    <p>Chờ xử lý</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="status-step @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) active @endif">
                                    <div class="step-circle">2</div>
                                    <p>Đang xử lý</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="status-step @if(in_array($order->status, ['shipped', 'delivered'])) active @endif">
                                    <div class="step-circle">3</div>
                                    <p>Đang giao</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="status-step @if($order->status == 'delivered') active @endif">
                                    <div class="step-circle">4</div>
                                    <p>Đã giao</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Thông tin sản phẩm</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
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

                <!-- Shipping Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Người nhận:</strong> {{ $order->full_name }}</p>
                        <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}, {{ $order->province }}, {{ $order->postal_code }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền hàng:</span>
                            <strong>{{ number_format($order->subtotal) }}₫</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <strong>{{ number_format($order->shipping_fee) }}₫</strong>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá:</span>
                                <strong class="text-danger">-{{ number_format($order->discount_amount) }}₫</strong>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span><strong>Tổng cộng:</strong></span>
                            <strong class="product-price">{{ number_format($order->total) }}₫</strong>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Phương thức:</strong>
                            @if($order->payment_method == 'cod')
                                Thanh toán khi nhận hàng
                            @elseif($order->payment_method == 'bank_transfer')
                                Chuyển khoản ngân hàng
                            @else
                                Ví điện tử
                            @endif
                        </p>
                        <p><strong>Trạng thái:</strong>
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-danger">Chưa thanh toán</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                @if(in_array($order->status, ['pending', 'processing']))
                    <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Hủy đơn hàng?')">
                            Hủy đơn hàng
                        </button>
                    </form>
                @endif

                <a href="/orders" class="btn btn-secondary w-100 mt-2">Quay lại</a>
            </div>
        </div>
    </div>
</div>

<style>
.status-step {
    opacity: 0.5;
}
.status-step.active {
    opacity: 1;
}
.step-circle {
    width: 50px;
    height: 50px;
    background-color: #ddd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 24px;
    font-weight: bold;
}
.status-step.active .step-circle {
    background-color: #e74c3c;
    color: white;
}
</style>
@endsection
