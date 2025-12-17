@extends('layouts.app')

@section('title', 'سوابق نظافت')

@section('content')
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
