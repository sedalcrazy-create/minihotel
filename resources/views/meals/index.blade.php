@extends('layouts.app')

@section('title', 'مدیریت وعده‌های غذایی')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>مدیریت وعده‌های غذایی</h2>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('meals.index') }}" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <label>تاریخ:</label>
                <input type="date" name="date" class="form-control" value="{{ request('date', now()->toDateString()) }}" style="max-width: 200px;">
                <button type="submit" class="btn btn-secondary">فیلتر</button>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h4>ثبت وعده جدید</h4>
        <form method="POST" action="{{ route('meals.store') }}" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            @csrf
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>رزرو (مهمان فعال):</label>
                    <select name="reservation_id" class="form-control" required>
                        <option value="">انتخاب کنید...</option>
                        @foreach($activeReservations as $res)
                            <option value="{{ $res->id }}">
                                {{ $res->personnel ? $res->personnel->first_name . ' ' . $res->personnel->last_name : ($res->guest ? $res->guest->full_name : '-') }}
                                - واحد {{ $res->room->unit->number ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>تاریخ:</label>
                    <input type="date" name="date" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="breakfast" value="1"> صبحانه
                    </label>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="lunch" value="1"> ناهار
                    </label>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="dinner" value="1"> شام
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">ثبت</button>
            </div>
        </form>

        <h4>لیست وعده‌ها</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>مهمان</th>
                        <th>تاریخ</th>
                        <th>صبحانه</th>
                        <th>ناهار</th>
                        <th>شام</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meals as $meal)
                        <tr>
                            <td>
                                @if($meal->reservation->personnel)
                                    {{ $meal->reservation->personnel->first_name }} {{ $meal->reservation->personnel->last_name }}
                                @elseif($meal->reservation->guest)
                                    {{ $meal->reservation->guest->full_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ jdate($meal->date)->format('Y/m/d') }}</td>
                            <td>{!! $meal->breakfast ? '<span style="color: green;">&#10004;</span>' : '<span style="color: #ccc;">-</span>' !!}</td>
                            <td>{!! $meal->lunch ? '<span style="color: green;">&#10004;</span>' : '<span style="color: #ccc;">-</span>' !!}</td>
                            <td>{!! $meal->dinner ? '<span style="color: green;">&#10004;</span>' : '<span style="color: #ccc;">-</span>' !!}</td>
                            <td>
                                <form action="{{ route('meals.destroy', $meal) }}" method="POST" style="display: inline;" onsubmit="return confirm('حذف شود؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">وعده‌ای ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $meals->links() }}
    </div>
</div>
@endsection
