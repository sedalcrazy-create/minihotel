@extends('layouts.app')

@section('title', 'گزارش‌ها')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>گزارش‌ها</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <a href="{{ route('reports.reservations') }}" class="card" style="text-decoration: none; padding: 30px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px;">
                <h3 style="margin: 0;">گزارش رزروها</h3>
                <p style="margin-top: 10px; opacity: 0.9;">لیست کامل رزروها با امکان فیلتر و خروجی Excel</p>
            </a>

            <a href="{{ route('reports.occupancy') }}" class="card" style="text-decoration: none; padding: 30px; text-align: center; background: linear-gradient(135deg, #f96c08 0%, #e37415 100%); color: white; border-radius: 12px;">
                <h3 style="margin: 0;">گزارش اشغال</h3>
                <p style="margin-top: 10px; opacity: 0.9;">وضعیت فعلی تخت‌ها و اتاق‌ها</p>
            </a>

            <a href="{{ route('reports.meals') }}" class="card" style="text-decoration: none; padding: 30px; text-align: center; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 12px;">
                <h3 style="margin: 0;">گزارش وعده‌های غذایی</h3>
                <p style="margin-top: 10px; opacity: 0.9;">آمار وعده‌های غذایی</p>
            </a>
        </div>
    </div>
</div>
@endsection
