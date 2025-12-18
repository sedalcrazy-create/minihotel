@extends('layouts.app')

@section('title', 'مدیریت مهمان‌ها')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>مدیریت مهمان‌ها</h2>
        <a href="{{ route('guests.create') }}" class="btn btn-primary">+ افزودن مهمان</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('guests.index') }}" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="جستجو (نام، کد ملی، تلفن، سازمان)..." value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-secondary">جستجو</button>
                @if(request('search'))
                    <a href="{{ route('guests.index') }}" class="btn btn-light">پاک کردن</a>
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
                        <th>نام کامل</th>
                        <th>کد ملی</th>
                        <th>تلفن</th>
                        <th>سازمان</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr>
                            <td>{{ $guest->id }}</td>
                            <td>{{ $guest->full_name }}</td>
                            <td>{{ $guest->national_code ?? '-' }}</td>
                            <td>{{ $guest->phone ?? '-' }}</td>
                            <td>{{ $guest->organization ?? '-' }}</td>
                            <td>{{ jdate($guest->created_at)->format('Y/m/d') }}</td>
                            <td>
                                <a href="{{ route('guests.show', $guest) }}" class="btn btn-sm btn-info">مشاهده</a>
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-warning">ویرایش</a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" style="display: inline;" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">مهمانی یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $guests->links() }}
    </div>
</div>
@endsection
