@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản Lý Danh Mục</h1>
    <a href="/admin/categories/create" class="btn btn-primary">
        {{ __('Thêm Danh Mục') }}
    </a>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên Danh Mục</th>
                    <th>Slug</th>
                    <th>Số Sản Phẩm</th>
                    <th>Mô Tả</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td>
                            <code>{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $category->products()->count() }}</span>
                        </td>
                        <td>
                            {{ Str::limit($category->description, 50) }}
                        </td>
                        <td>
                            @if ($category->is_active)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="/admin/categories/{{ $category->id }}/edit" class="btn btn-sm btn-warning">
                                Sửa
                            </a>
                            <form action="/admin/categories/{{ $category->id }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <p class="text-muted">Không có danh mục nào</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $categories->links() }}
</div>
@endsection
