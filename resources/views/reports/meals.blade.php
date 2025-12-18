@extends('layouts.app')

@section('title', 'گزارش وعده‌های غذایی')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>گزارش وعده‌های غذایی</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.meals') }}" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>از تاریخ:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>تا تاریخ:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <button type="submit" class="btn btn-primary">فیلتر</button>
            </div>
        </form>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <div style="padding: 20px; background: #fff3e0; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #e65100;">{{ $stats['total_breakfast'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">صبحانه</p>
            </div>
            <div style="padding: 20px; background: #e8f5e9; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #2e7d32;">{{ $stats['total_lunch'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">ناهار</p>
            </div>
            <div style="padding: 20px; background: #e3f2fd; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0; color: #1565c0;">{{ $stats['total_dinner'] }}</h3>
                <p style="margin: 5px 0 0; color: #666;">شام</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>مهمان</th>
                        <th>صبحانه</th>
                        <th>ناهار</th>
                        <th>شام</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meals as $meal)
                        <tr>
                            <td>{{ jdate($meal->date)->format('Y/m/d') }}</td>
                            <td>
                                @if($meal->reservation->personnel)
                                    {{ $meal->reservation->personnel->first_name }} {{ $meal->reservation->personnel->last_name }}
                                @elseif($meal->reservation->guest)
                                    {{ $meal->reservation->guest->full_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{!! $meal->breakfast ? '<span style="color: green;">&#10004;</span>' : '-' !!}</td>
                            <td>{!! $meal->lunch ? '<span style="color: green;">&#10004;</span>' : '-' !!}</td>
                            <td>{!! $meal->dinner ? '<span style="color: green;">&#10004;</span>' : '-' !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">وعده‌ای یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
