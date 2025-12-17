@extends('layouts.app')

@section('title', 'ثبت درخواست تعمیر')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>ثبت درخواست تعمیر جدید</h2>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('maintenance.store') }}">
            @csrf

            <div class="form-group">
                <label>انتخاب تخت یا اتاق:</label>
                <select name="bed_id" class="form-control">
                    <option value="">انتخاب تخت...</option>
                    @foreach($beds as $bed)
                        <option value="{{ $bed->id }}">واحد {{ $bed->room->unit->number }} - اتاق {{ $bed->room->number }} - تخت {{ $bed->number }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>یا انتخاب اتاق (کل اتاق):</label>
                <select name="room_id" class="form-control">
                    <option value="">انتخاب اتاق...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">واحد {{ $room->unit->number }} - اتاق {{ $room->number }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="description">شرح مشکل *</label>
                <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="priority">اولویت *</label>
                <select name="priority" id="priority" class="form-control" required>
                    <option value="low">پایین</option>
                    <option value="normal" selected>معمولی</option>
                    <option value="high">بالا</option>
                    <option value="urgent">فوری</option>
                </select>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">ثبت درخواست</button>
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection
