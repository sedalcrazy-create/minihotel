@extends('layouts.app')

@section('title', 'ورود به سیستم')

@section('content')
<style>
    body { background: linear-gradient(135deg, #f3debf 0%, #f96c08 50%, #e37415 100%) !important; }
    .login-box {
        max-width: 400px;
        margin: 100px auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        overflow: hidden;
    }
    .login-logo {
        width: 80px;
        height: 80px;
        margin: 25px auto 15px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .login-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
<div class="login-box">
    <div style="padding: 0 30px;">
        <div class="login-logo">
            <img src="http://localhost:8081/logo.png" alt="بانک ملی ایران">
        </div>
        <div class="card-header text-center" style="border: none; padding: 0; margin-bottom: 30px;">
            <h2 style="font-size: 18px; margin-bottom: 8px;">ورود به سیستم</h2>
            <p style="color: #6b7280; font-size: 14px; margin-top: 5px;">اداره کل آموزش بانک ملی</p>
        </div>

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
                <label for="email">ایمیل</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="example@bank.ir"
                >
            </div>

            <div class="form-group">
                <label for="password">رمز عبور</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    placeholder="••••••••"
                >
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; font-weight: normal;">
                    <input type="checkbox" name="remember" style="margin-left: 8px;">
                    مرا به خاطر بسپار
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                ورود
            </button>
        </form>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; padding-bottom: 30px;">
            <p>کاربران پیش‌فرض برای تست:</p>
            <p style="margin-top: 5px;">admin@bank.ir / password</p>
        </div>
    </div>
</div>
@endsection
