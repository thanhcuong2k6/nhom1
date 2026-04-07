@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/admin/products/{{ $product->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Danh mục *</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
                        @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá *</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" step="0.01" required>
                                @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá giảm</label>
                                <input type="number" class="form-control @error('discount_price') is-invalid @enderror" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01">
                                @error('discount_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tồn kho *</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ old('stock', $product->stock) }}" required>
                        @error('stock') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh</label>
                        <div class="mb-2">
                            <small class="text-muted">Hình ảnh hiện tại: {{ $product->images->count() }} ảnh</small>
                            @if($product->images->count() > 0)
                                <div class="row mt-2 mb-3">
                                    @foreach($product->images as $image)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid border" alt="Product image" style="height: 100px; object-fit: cover; width: 100%;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <input type="file" class="form-control{{ $errors->has('images') || $errors->has('images.*') ? ' is-invalid' : '' }}" name="images[]" id="images" multiple accept="image/*">
                        @error('images') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        @error('images.*') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        <small class="form-text text-muted d-block mt-2">Chọn thêm hình ảnh mới hoặc bỏ trống nếu không muốn thay đổi (tối đa 2MB mỗi ảnh)</small>
                        <div id="imagePreview" class="mt-3"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-check-label">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            Sản phẩm nổi bật
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="/admin/products" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = e.target.files;
    if (files.length === 0) return;
    
    preview.innerHTML = '<h6>Preview ảnh mới:</h6>';
    const row = document.createElement('div');
    row.className = 'row';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File ' + file.name + ' vượt quá 2MB');
            continue;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert('File ' + file.name + ' không phải là hình ảnh');
            continue;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = '<img src="' + event.target.result + '" class="img-fluid img-thumbnail" alt="Preview">';
            row.appendChild(col);
        };
        reader.readAsDataURL(file);
    }
    
    preview.appendChild(row);
});
</script>
@endsection
