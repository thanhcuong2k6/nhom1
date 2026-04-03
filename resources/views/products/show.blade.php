@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="row mt-4">
    <!-- Product Images -->
    <div class="col-md-5">
        <img src="{{ $product->getImageUrl() }}"
             class="img-fluid" alt="{{ $product->name }}" id="mainImage"
             style="max-height: 500px; object-fit: cover; width: 100%;">

        @if($product->images->count() > 0)
            <div class="row mt-3">
                @foreach($product->images as $image)
                    <div class="col-md-3 mb-2">
                        <img src="{{ asset('storage/' . $image->image) }}"
                             class="img-fluid border" alt="{{ $product->name }}"
                             onclick="document.getElementById('mainImage').src = this.src"
                             style="cursor: pointer; height: 80px; object-fit: cover; width: 100%;">
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="col-md-7">
        <h1>{{ $product->name }}</h1>

        <!-- Rating -->
        <div class="rating mb-3">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= $product->getAverageRating())
                    ★
                @else
                    ☆
                @endif
            @endfor
            <span class="ms-2">({{ $product->getReviewCount() }} đánh giá)</span>
        </div>

        <!-- Price -->
        <div class="mb-3">
            @if($product->discount_price)
                <span class="product-price">{{ number_format($product->discount_price) }}₫</span>
                <small class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price) }}₫</small>
                <span class="badge bg-danger ms-2">
                    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                </span>
            @else
                <span class="product-price">{{ number_format($product->price) }}₫</span>
            @endif
        </div>

        <!-- Stock -->
        <div class="mb-3">
            @if($product->stock > 0)
                <span class="badge bg-success">Còn {{ $product->stock }} sản phẩm</span>
            @else
                <span class="badge bg-danger">Hết hàng</span>
            @endif
        </div>

        <!-- Category -->
        <div class="mb-3">
            <strong>Danh mục:</strong>
            <a href="/products?category={{ $product->category->id }}">{{ $product->category->name }}</a>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <h5>Mô tả sản phẩm</h5>
            <p>{{ $product->description }}</p>
        </div>

        <!-- Add to Cart -->
        @if($product->stock > 0)
            <div class="mb-4">
                <div class="input-group mb-3" style="max-width: 200px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">-</button>
                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="{{ $product->stock }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">+</button>
                </div>
                <button class="btn btn-primary btn-lg w-100" onclick="addToCart()">
                    Thêm vào giỏ hàng
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-md-12">
            <h3>Sản phẩm liên quan</h3>
            <div class="row">
                @foreach($relatedProducts as $related)
                    <div class="col-md-3 mb-4">
                        <div class="card product-card">
                            <img src="{{ $related->getImageUrl() }}"
                                 class="card-img-top product-image" alt="{{ $related->name }}"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $related->name }}</h5>
                                <div class="product-price">{{ number_format($related->price) }}₫</div>
                                <a href="{{ route('products.show', $related->slug) }}" class="btn btn-primary w-100 mt-2">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Reviews -->
<div class="row mt-5">
    <div class="col-md-12">
        <h3>Đánh giá sản phẩm</h3>

        @auth
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Viết đánh giá</h5>
                    <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Đánh giá</label>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}">
                                        <label class="form-check-label" for="star{{ $i }}">
                                            @for($j = 1; $j <= $i; $j++)
                                                ★
                                            @endfor
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bình luận</label>
                            <textarea class="form-control" name="comment" rows="4" placeholder="Chia sẻ ý kiến của bạn..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <a href="/login">Đăng nhập</a> để viết đánh giá
            </div>
        @endauth

        <!-- Reviews List -->
        <div>
            @forelse($reviews as $review)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $review->user->name }}</strong>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>
                </div>
            @empty
                <p>Chưa có đánh giá nào.</p>
            @endforelse

            @if($reviews->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
    function addToCart() {
        const quantity = document.getElementById('quantity').value;
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.success);
                window.location.href = '/cart';
            } else {
                alert(data.error);
            }
        });
    }

    function increaseQty() {
        const qty = document.getElementById('quantity');
        const max = parseInt(qty.max);
        if(parseInt(qty.value) < max) {
            qty.value = parseInt(qty.value) + 1;
        }
    }

    function decreaseQty() {
        const qty = document.getElementById('quantity');
        if(parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    }
</script>
@endpush
@endsection
