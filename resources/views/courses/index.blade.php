@extends('layouts.app')

@section('title', 'دوره‌ها')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>📚 مدیریت دوره‌ها</h4>
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            ➕ دوره جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>کد</th>
                            <th>نام دوره</th>
                            <th>تاریخ شروع</th>
                            <th>تاریخ پایان</th>
                            <th>مدت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $course->code }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ jdate($course->start_date)->format('Y/m/d') }}</td>
                                <td>{{ jdate($course->end_date)->format('Y/m/d') }}</td>
                                <td>{{ $course->duration }} روز</td>
                                <td>
                                    @if($course->status === 'ongoing')
                                        <span class="badge bg-success">در حال برگزاری</span>
                                    @elseif($course->status === 'upcoming')
                                        <span class="badge bg-info">آینده</span>
                                    @else
                                        <span class="badge bg-secondary">پایان یافته</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info">مشاهده</a>
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-warning">ویرایش</a>
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">دوره‌ای یافت نشد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $courses->links() }}
        </div>
    </div>
</div>
@endsection
