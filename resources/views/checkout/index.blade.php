@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <h2>Thanh toán</h2>

        <form method="POST" action="/checkout">
            @csrf

            <!-- Shipping Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                   name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                            @error('full_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ *</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                               name="address" value="{{ old('address') }}" required>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thành phố *</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   name="city" value="{{ old('city') }}" required>
                            @error('city')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tỉnh/Thành phố *</label>
                            <input type="text" class="form-control @error('province') is-invalid @enderror"
                                   name="province" value="{{ old('province') }}" required>
                            @error('province')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mã bưu điện *</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                   name="postal_code" value="{{ old('postal_code') }}" required>
                            @error('postal_code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Phương thức thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-label" for="cod">
                            Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank_transfer">
                        <label class="form-check-label" for="bank">
                            Chuyển khoản ngân hàng
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="ewallet" value="ewallet">
                        <label class="form-check-label" for="ewallet">
                            Ví điện tử
                        </label>
                    </div>
                </div>
            </div>

            <!-- Discount Code -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Mã giảm giá</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" name="discount_code" placeholder="Nhập mã giảm giá" id="discountCode">
                        <button class="btn btn-outline-secondary" type="button" onclick="applyDiscount()">Áp dụng</button>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="/cart" class="btn btn-secondary">Quay lại giỏ hàng</a>
                <button type="submit" class="btn btn-primary float-end">Xác nhận đơn hàng</button>
            </div>
        </form>
    </div>

    <!-- Order Summary -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-body">
                <h5 class="card-title">Tóm tắt đơn hàng</h5>
                <hr>

                @foreach($cartItems as $item)
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.9rem;">
                        <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                        <span>{{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity) }}₫</span>
                    </div>
                @endforeach

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng tiền hàng:</span>
                    <strong>{{ number_format($subtotal) }}₫</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển:</span>
                    <strong>0₫</strong>
                </div>
                <div class="d-flex justify-content-between mb-3" id="discountRow" style="display: none;">
                    <span>Giảm giá:</span>
                    <strong id="discountAmount" class="text-danger">0₫</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <span><strong>Tổng cộng:</strong></span>
                    <strong class="product-price" id="totalAmount">{{ number_format($subtotal) }}₫</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function applyDiscount() {
        const code = document.getElementById('discountCode').value;
        if(!code) {
            alert('Vui lòng nhập mã giảm giá');
            return;
        }

        fetch('/checkout/apply-discount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
            } else {
                const subtotal = {{ $subtotal }};
                let discount = 0;

                if(data.discount_type == 1) { // percentage
                    discount = (subtotal * data.discount_value) / 100;
                    if(data.max_discount) {
                        discount = Math.min(discount, data.max_discount);
                    }
                } else { // fixed
                    discount = data.discount_value;
                }

                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmount').textContent = number_format(discount) + '₫';
                document.getElementById('totalAmount').textContent = number_format(subtotal - discount) + '₫';
                alert('Áp dụng mã giảm giá thành công!');
            }
        });
    }

    function number_format(number) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(number));
    }
</script>
@endpush
@endsection
