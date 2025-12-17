<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'سیستم مدیریت خوابگاه - بانک ملی')</title>
    <style>
        @font-face {
            font-family: 'IRANSans';
            src: url('/fonts/iran_sans.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'IRANSans';
            src: url('/fonts/iran_sans_bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'BYekan';
            src: url('/fonts/BYekan+.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'BYekan';
            src: url('/fonts/BYekan+ Bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IRANSans', 'BYekan', 'Tahoma', 'Arial', sans-serif;
            background: radial-gradient(circle at 30% 50%, rgba(250, 166, 39, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 70%, rgba(237, 26, 36, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 50% 30%, rgba(255, 246, 133, 0.12) 0%, transparent 50%),
                        linear-gradient(135deg, #fff8f5 0%, #ffefeb 50%, #ffe5e0 100%);
            min-height: 100vh;
            color: #333;
            direction: rtl;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #f3debf 0%, #f96c08 50%, #e37415 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(227, 116, 21, 0.3);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .header-logo-circle {
            position: relative;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-circle-glow {
            position: absolute;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(249, 108, 8, 0.6) 0%, rgba(227, 116, 21, 0.3) 50%, transparent 70%);
            border-radius: 50%;
            animation: pulse-glow 2s ease-in-out infinite;
            filter: blur(8px);
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 1; }
        }

        .logo-rotating-ring {
            position: absolute;
            border-radius: 50%;
            border: 3px solid;
            animation: rotate-ring 8s linear infinite;
        }

        .ring-1 {
            width: 95px;
            height: 95px;
            border-color: rgba(249, 108, 8, 0.6);
            border-style: dashed;
            animation-duration: 6s;
        }

        .ring-2 {
            width: 110px;
            height: 110px;
            border-color: rgba(227, 116, 21, 0.4);
            border-style: dotted;
            animation-duration: 10s;
            animation-direction: reverse;
        }

        .ring-3 {
            width: 125px;
            height: 125px;
            border-color: rgba(255, 246, 133, 0.5);
            border-style: solid;
            animation-duration: 14s;
        }

        @keyframes rotate-ring {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-particles {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #f96c08, #faa627);
            border-radius: 50%;
            opacity: 0;
            animation: float-particle 3s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(249, 108, 8, 0.8);
        }

        .particle-1 {
            top: -10px;
            left: 50%;
            animation-delay: 0s;
        }

        .particle-2 {
            top: 50%;
            right: -10px;
            animation-delay: 0.75s;
        }

        .particle-3 {
            bottom: -10px;
            left: 50%;
            animation-delay: 1.5s;
        }

        .particle-4 {
            top: 50%;
            left: -10px;
            animation-delay: 2.25s;
        }

        @keyframes float-particle {
            0%, 100% { opacity: 0; transform: translate(0, 0) scale(0); }
            50% { opacity: 1; transform: translate(0, -25px) scale(1.5); }
        }

        .header-logo-image {
            width: 55px;
            height: 55px;
            object-fit: contain;
            position: relative;
            z-index: 10;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
            animation: logo-float 3s ease-in-out infinite;
            background: linear-gradient(135deg, #f96c08 0%, #e37415 100%);
            border-radius: 50%;
            padding: 8px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-text-bank {
            font-size: 12px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }

        @keyframes logo-float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
        }

        .header-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .header-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .header-nav a:hover {
            background: rgba(255,255,255,0.1);
        }

        .header-nav form {
            margin: 0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f96c08 0%, #e37415 100%);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e37415 0%, #d66512 100%);
            box-shadow: 0 4px 12px rgba(227, 116, 21, 0.3);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-right: 4px solid;
        }

        .alert-success {
            background: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08),
                        0 2px 8px rgba(227, 116, 21, 0.1);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0,0,0,0.12),
                        0 4px 16px rgba(227, 116, 21, 0.15);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid;
            border-image: linear-gradient(90deg, #f96c08, #e37415, transparent) 1;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #f96c08, #faa627);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { width: 60px; opacity: 1; }
            50% { width: 100px; opacity: 0.7; }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        table th,
        table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            background: #f9fafb;
            font-weight: bold;
        }

        table tr:hover {
            background: #f9fafb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
            font-family: 'IRANSans', 'BYekan', 'Tahoma', 'Arial', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #E37415;
            box-shadow: 0 0 0 3px rgba(227, 116, 21, 0.15);
        }

        .form-control:hover {
            border-color: #E37415;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-right: 5px solid;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.3) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }

        .stat-card.available {
            border-color: #10b981;
            background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(255,255,255,0.95) 100%);
        }

        .stat-card.occupied {
            border-color: #ef4444;
            background: linear-gradient(135deg, rgba(239,68,68,0.05) 0%, rgba(255,255,255,0.95) 100%);
        }

        .stat-card.cleaning {
            border-color: #f59e0b;
            background: linear-gradient(135deg, rgba(245,158,11,0.05) 0%, rgba(255,255,255,0.95) 100%);
        }

        .stat-card.maintenance {
            border-color: #6b7280;
            background: linear-gradient(135deg, rgba(107,114,128,0.05) 0%, rgba(255,255,255,0.95) 100%);
        }

        .stat-value {
            font-size: 42px;
            font-weight: bold;
            margin: 15px 0;
            background: linear-gradient(135deg, #f96c08, #e37415);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #6b7280;
            font-size: 15px;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-checked-in {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-checked-out {
            background: #e5e7eb;
            color: #374151;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            text-decoration: none;
            color: #374151;
        }

        .pagination a:hover {
            background: #f9fafb;
        }

        .pagination .active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>
<body>
    @auth
    <div class="header">
        <div class="header-content">
            <div class="header-logo">
                <div class="header-logo-circle">
                    <div class="logo-circle-glow"></div>
                    <div class="logo-rotating-ring ring-1"></div>
                    <div class="logo-rotating-ring ring-2"></div>
                    <div class="logo-rotating-ring ring-3"></div>
                    <div class="logo-particles">
                        <span class="particle particle-1"></span>
                        <span class="particle particle-2"></span>
                        <span class="particle particle-3"></span>
                        <span class="particle particle-4"></span>
                    </div>
                    <img src="{{ asset('logo.png') }}" alt="لوگو بانک ملی" class="header-logo-image">
                </div>
                <div class="logo-text">
                    <h1>سیستم مدیریت خوابگاه</h1>
                    <span class="logo-text-bank">بانک ملی ایران - اداره کل آموزش</span>
                </div>
            </div>
            <div class="header-nav">
                <a href="{{ route('dashboard') }}">داشبورد</a>
                <a href="{{ route('personnel.index') }}">پرسنل</a>
                <a href="{{ route('guests.index') }}">مهمان‌ها</a>
                <a href="{{ route('reservations.index') }}">رزروها</a>
                <a href="{{ route('meals.index') }}">وعده غذایی</a>
                <a href="{{ route('cleaning.index') }}">نظافت</a>
                <a href="{{ route('maintenance.index') }}">تعمیرات</a>
                <a href="{{ route('reports.index') }}">گزارش‌ها</a>
                <span style="color: rgba(255,255,255,0.8);">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">خروج</button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
