<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'سیستم مدیریت خوابگاه - بانک ملی')</title>
    <!-- فونت وزیرمتن از CDN -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', 'Tahoma', 'Arial', sans-serif;
            background: linear-gradient(180deg, #fff8f5 0%, #ffefeb 50%, #ffe5e0 100%);
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

        /* ========== HEADER STYLES ========== */
        .header {
            background: linear-gradient(135deg, #f96c08 0%, #e37415 50%, #d66512 100%);
            padding: 0;
            box-shadow: 0 8px 40px rgba(227, 116, 21, 0.4);
            position: relative;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(255,200,100,0.1) 0%, transparent 50%);
        }

        .header-content-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 40px;
            min-height: 200px;
        }

        /* متن وسط */
        .header-text-content {
            text-align: center;
            z-index: 10;
        }

        .header-main-title {
            font-size: 32px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 12px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
            letter-spacing: -0.5px;
        }

        .header-main-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 15px;
        }

        .header-tagline-box {
            display: inline-block;
            background: linear-gradient(135deg, #fff685 0%, #ffd54f 100%);
            padding: 12px 30px;
            border-radius: 30px;
            box-shadow: 0 4px 25px rgba(255, 246, 133, 0.6), 0 0 50px rgba(255, 213, 79, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.9);
            position: relative;
            overflow: hidden;
        }

        .header-tagline-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            transform: rotate(45deg);
            animation: tagline-shine 3s infinite;
        }

        @keyframes tagline-shine {
            0% { left: -100%; }
            50%, 100% { left: 100%; }
        }

        .header-tagline-text {
            font-size: 16px;
            font-weight: bold;
            color: #8B4513;
            position: relative;
            z-index: 1;
        }

        /* لوگو سمت چپ */
        .header-logo-section {
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .header-logo-circle {
            position: relative;
            width: 180px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-bank-name {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            background: rgba(0,0,0,0.2);
            padding: 6px 16px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
            white-space: nowrap;
        }

        .logo-circle-glow {
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255, 246, 133, 0.4) 0%, rgba(249, 108, 8, 0.2) 50%, transparent 70%);
            border-radius: 50%;
            animation: pulse-glow 2.5s ease-in-out infinite;
            filter: blur(10px);
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.2); opacity: 1; }
        }

        .logo-rotating-ring {
            position: absolute;
            border-radius: 50%;
            border: 3px solid;
            animation: rotate-ring 8s linear infinite;
        }

        .ring-1 {
            width: 200px;
            height: 200px;
            border-color: rgba(255, 246, 133, 0.8);
            border-style: dashed;
            animation-duration: 6s;
        }

        .ring-2 {
            width: 230px;
            height: 230px;
            border-color: rgba(255, 255, 255, 0.5);
            border-style: dotted;
            animation-duration: 10s;
            animation-direction: reverse;
        }

        .ring-3 {
            width: 260px;
            height: 260px;
            border-color: rgba(249, 108, 8, 0.4);
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
            width: 10px;
            height: 10px;
            background: linear-gradient(135deg, #fff685, #ffd54f);
            border-radius: 50%;
            opacity: 0;
            animation: float-particle 3s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(255, 246, 133, 0.8);
        }

        .particle-1 { top: -15px; left: 50%; animation-delay: 0s; }
        .particle-2 { top: 50%; right: -15px; animation-delay: 0.75s; }
        .particle-3 { bottom: -15px; left: 50%; animation-delay: 1.5s; }
        .particle-4 { top: 50%; left: -15px; animation-delay: 2.25s; }

        @keyframes float-particle {
            0%, 100% { opacity: 0; transform: translate(0, 0) scale(0); }
            50% { opacity: 1; transform: translate(0, -30px) scale(1.5); }
        }

        .header-logo-image {
            width: 140px;
            height: 140px;
            object-fit: contain;
            position: relative;
            z-index: 10;
            filter: drop-shadow(0 8px 25px rgba(0,0,0,0.3));
            animation: logo-float 3s ease-in-out infinite;
            background: transparent;
            border-radius: 50%;
            padding: 0;
            border: none;
        }

        @keyframes logo-float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-8px) scale(1.02); }
        }

        /* نوار ناوبری */
        .header-nav-bar {
            background: rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            padding: 12px 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .header-nav-bar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .header-nav-bar a:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .header-nav-bar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-right: auto;
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.8) !important;
            border-color: rgba(239, 68, 68, 0.8) !important;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 1) !important;
        }

        /* ========== REST OF STYLES ========== */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f96c08 0%, #e37415 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e37415 0%, #d66512 100%);
            box-shadow: 0 4px 15px rgba(227, 116, 21, 0.4);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-right: 5px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
            color: #065f46;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
            color: #991b1b;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(227, 116, 21, 0.1);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0,0,0,0.12), 0 4px 16px rgba(227, 116, 21, 0.15);
        }

        .card-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid;
            border-image: linear-gradient(90deg, #f96c08, #e37415, transparent) 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        table th, table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
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
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #f96c08;
            box-shadow: 0 0 0 4px rgba(249, 108, 8, 0.15);
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
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }

        .stat-card.available { border-color: #10b981; }
        .stat-card.occupied { border-color: #ef4444; }
        .stat-card.cleaning { border-color: #f59e0b; }
        .stat-card.maintenance { border-color: #6b7280; }

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

        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-checked-in { background: #d1fae5; color: #065f46; }
        .badge-checked-out { background: #e5e7eb; color: #374151; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background: #f96c08;
            color: white;
            border-color: #f96c08;
        }

        .pagination .active {
            background: #f96c08;
            color: white;
            border-color: #f96c08;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-logo-section {
                position: relative;
                left: auto;
                top: auto;
                transform: none;
                margin-bottom: 15px;
            }

            .header-logo-circle {
                width: 100px;
                height: 100px;
            }

            .header-content-wrapper {
                flex-direction: column;
                padding: 25px 20px;
            }

            .header-main-title {
                font-size: 22px;
            }

            .ring-1 { width: 110px; height: 110px; }
            .ring-2 { width: 125px; height: 125px; }
            .ring-3 { width: 140px; height: 140px; }

            .header-logo-image {
                width: 70px;
                height: 70px;
            }

            .logo-bank-name {
                font-size: 12px;
            }

            .header-nav-bar {
                gap: 6px;
            }

            .header-nav-bar a {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    @auth
    <div class="header">
        <div class="header-content-wrapper">
            <!-- متن وسط -->
            <div class="header-text-content">
                <h1 class="header-main-title">سیستم مدیریت خوابگاه</h1>
                <p class="header-main-subtitle">بانک ملی ایران - اداره کل آموزش</p>
                <div class="header-tagline-box">
                    <span class="header-tagline-text">مدیریت هوشمند اقامت</span>
                </div>
            </div>

            <!-- لوگو سمت چپ -->
            <div class="header-logo-section">
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
                <div class="logo-bank-name">بانک ملی ایران</div>
            </div>
        </div>

        <!-- نوار ناوبری -->
        <div class="header-nav-bar">
            <a href="{{ route('dashboard') }}">🏠 داشبورد</a>
            <a href="{{ route('personnel.index') }}">👥 پرسنل</a>
            <a href="{{ route('guests.index') }}">🧑‍💼 مهمان‌ها</a>
            <a href="{{ route('reservations.index') }}">📅 رزروها</a>
            <a href="{{ route('meals.index') }}">🍽️ وعده غذایی</a>
            <a href="{{ route('cleaning.index') }}">🧹 نظافت</a>
            <a href="{{ route('maintenance.index') }}">🔧 تعمیرات</a>
            <a href="{{ route('reports.index') }}">📊 گزارش‌ها</a>
            <div class="user-info">
                <span>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-logout" style="padding: 6px 14px; font-size: 13px;">خروج</button>
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
