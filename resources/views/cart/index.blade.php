@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <h2>Giỏ hàng của bạn</h2>

        @if($cartItems->isEmpty())
            <div class="alert alert-info">
                Giỏ hàng trống. <a href="/products">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product->getImageUrl() }}"
                                             alt="{{ $item->product->name }}" style="width: 80px; height: 80px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                        <a href="{{ route('products.show', $item->product->slug) }}">
                                            {{ $item->product->name }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    {{ number_format($item->product->discount_price ?? $item->product->price) }}₫
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="{{ $item->quantity }}"
                                           min="1" max="{{ $item->product->stock }}"
                                           onchange="updateCart({{ $item->id }}, this.value)" style="width: 80px;">
                                </td>
                                <td>
                                    {{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity) }}₫
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="removeFromCart({{ $item->id }})">
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="/products" class="btn btn-secondary">Tiếp tục mua sắm</a>
                <button class="btn btn-danger" onclick="clearCart()">Xóa tất cả</button>
            </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tóm tắt đơn hàng</h5>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng tiền hàng:</span>
                    <strong>{{ number_format($total) }}₫</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển:</span>
                    <strong>0₫</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span><strong>Tổng cộng:</strong></span>
                    <strong class="product-price">{{ number_format($total) }}₫</strong>
                </div>

                @if(!$cartItems->isEmpty())
                    @auth
                        <a href="/checkout" class="btn btn-primary w-100">Tiến hành thanh toán</a>
                    @else
                        <a href="/login" class="btn btn-primary w-100">Đăng nhập để thanh toán</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function updateCart(itemId, quantity) {
        fetch('/cart/' + itemId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.error);
            }
        });
    }

    function removeFromCart(itemId) {
        if(confirm('Bạn chắc chứ?')) {
            fetch('/cart/' + itemId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    }

    function clearCart() {
        if(confirm('Xóa tất cả sản phẩm trong giỏ hàng?')) {
            fetch('/cart/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endpush
@endsection
