@extends('layouts.app')

@section('title', 'سوابق نظافت')

@section('content')

@if(isset($selectedBed) && $selectedBed)
<div class="card" style="margin-bottom: 20px; border: 2px solid #10b981;">
    <div class="card-header" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
        <h3 style="margin: 0;">ثبت نظافت برای تخت انتخاب شده</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div style="background: #d1fae5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>تخت:</strong> واحد {{ $selectedBed->room->unit->number }} - اتاق {{ $selectedBed->room->number }} - تخت {{ $selectedBed->number }}
            <span style="margin-right: 20px; padding: 4px 12px; border-radius: 12px; font-size: 12px; background:
                {{ $selectedBed->status == 'available' ? '#10b981' : ($selectedBed->status == 'needs_cleaning' ? '#f59e0b' : '#6b7280') }}; color: white;">
                {{ $selectedBed->status == 'available' ? 'آزاد' : ($selectedBed->status == 'needs_cleaning' ? 'نیاز به نظافت' : $selectedBed->status) }}
            </span>
        </div>

        <form method="POST" action="{{ route('cleaning.store') }}">
            @csrf
            <input type="hidden" name="bed_id" value="{{ $selectedBed->id }}">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="type">نوع نظافت *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="daily">روزانه</option>
                        <option value="weekly">هفتگی</option>
                        <option value="deep">عمیق</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">یادداشت</label>
                    <input type="text" name="notes" id="notes" class="form-control" placeholder="توضیحات...">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit" class="btn btn-success">✓ ثبت نظافت</button>
                <a href="{{ route('cleaning.index') }}" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>سوابق نظافت</h2>
        <a href="{{ route('cleaning.pending') }}" class="btn btn-warning">تخت‌های نیازمند نظافت</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('cleaning.index') }}" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label>تاریخ:</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}" style="max-width: 200px;">
                <button type="submit" class="btn btn-secondary">فیلتر</button>
                @if(request('date'))
                    <a href="{{ route('cleaning.index') }}" class="btn btn-light">پاک کردن</a>
                @endif
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>واحد/اتاق/تخت</th>
                        <th>نوع نظافت</th>
                        <th>تاریخ و ساعت</th>
                        <th>انجام‌دهنده</th>
                        <th>یادداشت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->bed)
                                    واحد {{ $log->bed->room->unit->number }} - اتاق {{ $log->bed->room->number }} - تخت {{ $log->bed->number }}
                                @elseif($log->room)
                                    واحد {{ $log->room->unit->number }} - اتاق {{ $log->room->number }} (کل اتاق)
                                @endif
                            </td>
                            <td>
                                @php
                                    $types = ['daily' => 'روزانه', 'weekly' => 'هفتگی', 'deep' => 'عمیق'];
                                @endphp
                                {{ $types[$log->type] ?? $log->type }}
                            </td>
                            <td>{{ jdate($log->cleaned_at)->format('Y/m/d H:i') }}</td>
                            <td>{{ $log->cleanedBy->name ?? '-' }}</td>
                            <td>{{ $log->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">سابقه‌ای یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $logs->links() }}
    </div>
</div>
@endsection
