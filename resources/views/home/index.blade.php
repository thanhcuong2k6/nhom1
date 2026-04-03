@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="row mt-5">
    <!-- Banner -->
    <div class="col-md-12 mb-4">
        <div class="card bg-danger text-white" style="height: 300px; display: flex; align-items: center; justify-content: center;">
            <div class="card-body text-center">
                <h1 class="card-title">Chào mừng đến với Shopdo3</h1>
                <p class="card-text">Khám phá các sản phẩm chất lượng cao với giá cực tốt</p>
                <a href="/products" class="btn btn-light">Mua ngay</a>
            </div>
        </div>
    </div>
</div>

<!-- Danh mục -->
<div class="row mt-4">
    <div class="col-md-12">
        <h2>Danh mục sản phẩm</h2>
        <div class="row">
            @forelse($categories as $category)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <a href="/products?category={{ $category->id }}" class="btn btn-outline-primary">
                                Xem danh mục
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Chưa có danh mục nào.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Sản phẩm nổi bật -->
<div class="row mt-4">
    <div class="col-md-12">
        <h2>Sản phẩm nổi bật</h2>
        <div class="row">
            @forelse($featuredProducts as $product)
                <div class="col-md-3 mb-4">
                    <div class="card product-card">
                        <img src="{{ $product->getImageUrl() }}"
                             class="card-img-top product-image" alt="{{ $product->name }}"
                             style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-truncate">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->discount_price)
                                        <span class="product-price">{{ number_format($product->discount_price) }}₫</span>
                                        <small class="text-muted text-decoration-line-through">{{ number_format($product->price) }}₫</small>
                                    @else
                                        <span class="product-price">{{ number_format($product->price) }}₫</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rating mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->getAverageRating())
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                                ({{ $product->getReviewCount() }})
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary w-100 mt-2">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Chưa có sản phẩm nổi bật.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Sản phẩm mới -->
<div class="row mt-4">
    <div class="col-md-12">
        <h2>Sản phẩm mới nhất</h2>
        <div class="row">
            @forelse($newProducts as $product)
                <div class="col-md-3 mb-4">
                    <div class="card product-card">
                        <img src="{{ $product->getImageUrl() }}"
                             class="card-img-top product-image" alt="{{ $product->name }}"
                             style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-truncate">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->discount_price)
                                        <span class="product-price">{{ number_format($product->discount_price) }}₫</span>
                                        <small class="text-muted text-decoration-line-through">{{ number_format($product->price) }}₫</small>
                                    @else
                                        <span class="product-price">{{ number_format($product->price) }}₫</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rating mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->getAverageRating())
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                                ({{ $product->getReviewCount() }})
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary w-100 mt-2">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Chưa có sản phẩm nào.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
