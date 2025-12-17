@extends('layouts.app')

@section('title', 'گزارش رزروها')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>گزارش رزروها</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.reservations') }}" style="margin-bottom: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>از تاریخ:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>تا تاریخ:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>وضعیت:</label>
                    <select name="status" class="form-control">
                        <option value="">همه</option>
                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>رزرو شده</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>ورود</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>خروج</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>نوع پذیرش:</label>
                    <select name="admission_type_id" class="form-control">
                        <option value="">همه</option>
                        @foreach($admissionTypes as $type)
                            <option value="{{ $type->id }}" {{ request('admission_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">فیلتر</button>
                <a href="{{ route('reports.reservations', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">خروجی Excel</a>
            </div>
        </form>

        <div class="alert alert-info">
            تعداد کل: <strong>{{ $reservations->count() }}</strong> رزرو
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مهمان</th>
                        <th>نوع پذیرش</th>
                        <th>واحد</th>
                        <th>تعداد تخت</th>
                        <th>تاریخ ورود</th>
                        <th>تاریخ خروج</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                @if($r->personnel)
                                    {{ $r->personnel->first_name }} {{ $r->personnel->last_name }}
                                @elseif($r->guest)
                                    {{ $r->guest->full_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $r->admissionType->name ?? '-' }}</td>
                            <td>{{ $r->room->unit->number ?? '-' }}</td>
                            <td>{{ $r->beds->count() }}</td>
                            <td>{{ jdate($r->check_in_date)->format('Y/m/d') }}</td>
                            <td>{{ jdate($r->check_out_date)->format('Y/m/d') }}</td>
                            <td>
                                @php
                                    $statuses = ['reserved' => 'رزرو شده', 'checked_in' => 'ورود', 'checked_out' => 'خروج', 'cancelled' => 'لغو شده'];
                                @endphp
                                {{ $statuses[$r->status] ?? $r->status }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">رزروی یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
