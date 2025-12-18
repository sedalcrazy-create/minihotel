@extends('layouts.app')

@section('title', 'جزئیات درخواست تعمیر')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>درخواست تعمیر #{{ $maintenance->id }}</h2>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 150px;">محل:</th>
                        <td>
                            @if($maintenance->bed)
                                واحد {{ $maintenance->bed->room->unit->number }} - اتاق {{ $maintenance->bed->room->number }} - تخت {{ $maintenance->bed->number }}
                            @elseif($maintenance->room)
                                واحد {{ $maintenance->room->unit->number }} - اتاق {{ $maintenance->room->number }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>شرح مشکل:</th>
                        <td>{{ $maintenance->description }}</td>
                    </tr>
                    <tr>
                        <th>اولویت:</th>
                        <td>
                            @php
                                $priorities = ['low' => 'پایین', 'normal' => 'معمولی', 'high' => 'بالا', 'urgent' => 'فوری'];
                            @endphp
                            {{ $priorities[$maintenance->priority] ?? $maintenance->priority }}
                        </td>
                    </tr>
                    <tr>
                        <th>وضعیت:</th>
                        <td>
                            @php
                                $statuses = ['pending' => 'در انتظار', 'in_progress' => 'در حال انجام', 'completed' => 'تکمیل شده', 'cancelled' => 'لغو شده'];
                            @endphp
                            {{ $statuses[$maintenance->status] ?? $maintenance->status }}
                        </td>
                    </tr>
                    <tr>
                        <th>گزارش‌دهنده:</th>
                        <td>{{ $maintenance->reportedBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>مسئول تعمیر:</th>
                        <td>{{ $maintenance->assignedTo->name ?? 'تخصیص داده نشده' }}</td>
                    </tr>
                    <tr>
                        <th>تاریخ ثبت:</th>
                        <td>{{ jdate($maintenance->created_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                    @if($maintenance->started_at)
                    <tr>
                        <th>تاریخ شروع:</th>
                        <td>{{ jdate($maintenance->started_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                    @endif
                    @if($maintenance->completed_at)
                    <tr>
                        <th>تاریخ اتمام:</th>
                        <td>{{ jdate($maintenance->completed_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <hr>

        <h4>تغییر وضعیت</h4>
        <form method="POST" action="{{ route('maintenance.update', $maintenance) }}" style="display: inline-flex; gap: 10px; align-items: center;">
            @csrf
            @method('PUT')
            <select name="status" class="form-control" style="width: auto;">
                <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>در انتظار</option>
                <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>در حال انجام</option>
                <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
            </select>
            <button type="submit" class="btn btn-primary">ذخیره</button>
        </form>
    </div>
</div>
@endsection
