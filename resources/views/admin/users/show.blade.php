@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Thông tin cá nhân</h5>
            </div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Điện thoại:</strong> {{ $user->phone ?? 'N/A' }}</p>
                <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Orders -->
        <div class="card">
            <div class="card-header">
                <h5>Đơn hàng</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>
                                    @if($order->status == 'delivered')
                                        <span class="badge bg-success">Đã giao</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($order->total) }}₫</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Chưa có đơn hàng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Role Management -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Quyền hạn</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update-role', $user->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chọn quyền hạn</label>
                        <select class="form-select" name="role" required>
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                </form>
            </div>
        </div>

        <!-- Status Management -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Trạng thái tài khoản</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    Trạng thái hiện tại:
                    @if($user->is_active)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-warning">Khóa</span>
                    @endif
                </p>
                <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}">
                    @csrf
                    @if($user->is_active)
                        <button type="submit" class="btn btn-danger w-100">Khóa tài khoản</button>
                    @else
                        <button type="submit" class="btn btn-success w-100">Kích hoạt tài khoản</button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h5>Thống kê</h5>
            </div>
            <div class="card-body">
                <p class="d-flex justify-content-between">
                    <span>Tổng đơn hàng:</span>
                    <strong>{{ $user->orders->count() }}</strong>
                </p>
                <p class="d-flex justify-content-between">
                    <span>Tổng chi tiêu:</span>
                    <strong>{{ number_format($user->orders->sum('total')) }}₫</strong>
                </p>
                <p class="d-flex justify-content-between">
                    <span>Địa chỉ:</span>
                    <strong>{{ $user->addresses->count() }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
