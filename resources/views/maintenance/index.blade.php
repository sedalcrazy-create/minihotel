@extends('layouts.app')

@section('title', 'مدیریت تعمیرات')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>مدیریت تعمیرات</h2>
        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">+ ثبت درخواست تعمیر</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('maintenance.index') }}" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label>وضعیت:</label>
                <select name="status" class="form-control" style="max-width: 200px;">
                    <option value="">همه</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>در حال انجام</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                </select>
                <button type="submit" class="btn btn-secondary">فیلتر</button>
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
                        <th>محل</th>
                        <th>شرح مشکل</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>گزارش‌دهنده</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->id }}</td>
                            <td>
                                @if($req->bed)
                                    واحد {{ $req->bed->room->unit->number }} - تخت {{ $req->bed->number }}
                                @elseif($req->room)
                                    واحد {{ $req->room->unit->number }} - اتاق {{ $req->room->number }}
                                @endif
                            </td>
                            <td>{{ Str::limit($req->description, 50) }}</td>
                            <td>
                                @php
                                    $priorities = ['low' => 'پایین', 'normal' => 'معمولی', 'high' => 'بالا', 'urgent' => 'فوری'];
                                    $priorityColors = ['low' => '#6c757d', 'normal' => '#17a2b8', 'high' => '#ffc107', 'urgent' => '#dc3545'];
                                @endphp
                                <span style="color: {{ $priorityColors[$req->priority] ?? '#000' }}; font-weight: bold;">
                                    {{ $priorities[$req->priority] ?? $req->priority }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statuses = ['pending' => 'در انتظار', 'in_progress' => 'در حال انجام', 'completed' => 'تکمیل شده', 'cancelled' => 'لغو شده'];
                                    $statusColors = ['pending' => '#ffc107', 'in_progress' => '#17a2b8', 'completed' => '#28a745', 'cancelled' => '#6c757d'];
                                @endphp
                                <span style="background: {{ $statusColors[$req->status] ?? '#000' }}; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                    {{ $statuses[$req->status] ?? $req->status }}
                                </span>
                            </td>
                            <td>{{ $req->reportedBy->name ?? '-' }}</td>
                            <td>{{ jdate($req->created_at)->format('Y/m/d') }}</td>
                            <td>
                                <a href="{{ route('maintenance.show', $req) }}" class="btn btn-sm btn-info">مشاهده</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">درخواستی یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </div>
</div>
@endsection
