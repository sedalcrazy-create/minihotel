@extends('layouts.app')

@section('title', 'گزارش اشغال')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>گزارش اشغال تخت‌ها</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 20px; background: #e8f5e9; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #2e7d32;">{{ $stats['available'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">خالی</p>
            </div>
            <div style="padding: 20px; background: #ffebee; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #c62828;">{{ $stats['occupied'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">اشغال</p>
            </div>
            <div style="padding: 20px; background: #fff8e1; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #f57f17;">{{ $stats['needs_cleaning'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">نیاز به نظافت</p>
            </div>
            <div style="padding: 20px; background: #e3f2fd; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #1565c0;">{{ $stats['under_maintenance'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">در حال تعمیر</p>
            </div>
            <div style="padding: 20px; background: #f3e5f5; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #7b1fa2;">{{ $stats['total_beds'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">کل تخت‌ها</p>
            </div>
        </div>

        <h4>وضعیت واحدها</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>واحد</th>
                        <th>بخش</th>
                        <th>خالی</th>
                        <th>اشغال</th>
                        <th>نظافت</th>
                        <th>تعمیر</th>
                        <th>درصد اشغال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $unit)
                        @php
                            $beds = $unit->rooms->flatMap->beds;
                            $total = $beds->count();
                            $available = $beds->where('status', 'available')->count();
                            $occupied = $beds->where('status', 'occupied')->count();
                            $cleaning = $beds->where('status', 'needs_cleaning')->count();
                            $maintenance = $beds->where('status', 'under_maintenance')->count();
                            $percent = $total > 0 ? round(($occupied / $total) * 100) : 0;
                        @endphp
                        <tr>
                            <td>واحد {{ $unit->number }}</td>
                            <td>{{ $unit->section == 'east' ? 'شرقی' : 'غربی' }}</td>
                            <td style="color: green;">{{ $available }}</td>
                            <td style="color: red;">{{ $occupied }}</td>
                            <td style="color: orange;">{{ $cleaning }}</td>
                            <td style="color: blue;">{{ $maintenance }}</td>
                            <td>
                                <div style="background: #eee; border-radius: 4px; overflow: hidden;">
                                    <div style="background: #f96c08; width: {{ $percent }}%; height: 20px; text-align: center; color: white; font-size: 12px; line-height: 20px;">
                                        {{ $percent }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
