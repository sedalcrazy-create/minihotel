@extends('layouts.app')

@section('title', 'تخت‌های نیازمند نظافت')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>تخت‌های نیازمند نظافت</h2>
        <a href="{{ route('cleaning.index') }}" class="btn btn-secondary">سوابق نظافت</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if($beds->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>واحد</th>
                        <th>اتاق</th>
                        <th>تخت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beds as $bed)
                        <tr>
                            <td>واحد {{ $bed->room->unit->number }}</td>
                            <td>اتاق {{ $bed->room->number }}</td>
                            <td>تخت {{ $bed->number }}</td>
                            <td>
                                <form action="{{ route('cleaning.store') }}" method="POST" style="display: inline-flex; gap: 10px; align-items: center;">
                                    @csrf
                                    <input type="hidden" name="bed_id" value="{{ $bed->id }}">
                                    <select name="type" class="form-control form-control-sm" style="width: auto;">
                                        <option value="daily">روزانه</option>
                                        <option value="weekly">هفتگی</option>
                                        <option value="deep">عمیق</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success">ثبت نظافت</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="alert alert-info">همه تخت‌ها تمیز هستند!</div>
        @endif
    </div>
</div>
@endsection
