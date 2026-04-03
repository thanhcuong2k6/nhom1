@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Danh Mục')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Chỉnh Sửa Danh Mục</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/admin/categories/{{ $category->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $category->name) }}"
                            placeholder="Nhập tên danh mục" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" value="{{ $category->slug }}"
                            placeholder="Slug sẽ được tạo tự động" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô Tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="5"
                            placeholder="Nhập mô tả danh mục">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} />
                            <label class="form-check-label" for="is_active">
                                Hoạt động
                            </label>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p class="text-muted">
                                <small><strong>Số sản phẩm:</strong> {{ $category->products()->count() }}</small>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Cập Nhật') }}
                        </button>
                        <a href="/admin/categories" class="btn btn-secondary">
                            {{ __('Hủy') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
