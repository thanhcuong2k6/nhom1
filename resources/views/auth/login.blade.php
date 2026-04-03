@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="row mt-5">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Đăng nhập</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/login">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-check-label">
                            <input type="checkbox" name="remember" class="form-check-input">
                            Ghi nhớ tôi
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>

                    <div class="mt-3 text-center">
                        <p>Chưa có tài khoản? <a href="/register">Đăng ký ngay</a></p>
                    </div>
                </form>

                <!-- Demo Info -->
                <div class="alert alert-info mt-4">
                    <strong>Tài khoản Demo:</strong><br>
                    <strong>Admin:</strong> admin@shopdo3.com / password<br>
                    <strong>User:</strong> user@shopdo3.com / password
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
