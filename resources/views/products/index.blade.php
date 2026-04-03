@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
<div class="row mt-4">
    <!-- Sidebar - Filters -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bộ lọc</h5>
                <form method="get" action="/products">
                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Danh mục</strong></label>
                        <select class="form-select" name="category">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Giá</strong></label>
                        <div class="input-group mb-2">
                            <input type="number" class="form-control" name="min_price"
                                   placeholder="Từ" value="{{ request('min_price') }}">
                        </div>
                        <div class="input-group">
                            <input type="number" class="form-control" name="max_price"
                                   placeholder="Đến" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Sắp xếp</strong></label>
                        <select class="form-select" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                    <a href="/products" class="btn btn-secondary w-100 mt-2">Xóa bộ lọc</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="col-md-9">
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img src="{{ $product->getImageUrl() }}"
                             class="card-img-top product-image" alt="{{ $product->name }}"
                             style="height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-truncate">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    @if($product->discount_price)
                                        <span class="product-price">{{ number_format($product->discount_price) }}₫</span>
                                        <small class="text-muted text-decoration-line-through">{{ number_format($product->price) }}₫</small>
                                    @else
                                        <span class="product-price">{{ number_format($product->price) }}₫</span>
                                    @endif
                                </div>
                            </div>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->getAverageRating())
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                                ({{ $product->getReviewCount() }})
                            </div>
                            <div class="mt-2">
                                @if($product->stock > 0)
                                    <span class="badge bg-success">Còn hàng</span>
                                @else
                                    <span class="badge bg-danger">Hết hàng</span>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary w-100 mt-2">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p class="text-center">Không tìm thấy sản phẩm nào.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="row mt-4">
                <div class="col-md-12 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
