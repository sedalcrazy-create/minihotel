@extends('layouts.app')

@section('title', 'جزئیات پرسنل')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>جزئیات پرسنل</h2>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('personnel.edit', $personnel) }}" class="btn btn-primary">ویرایش</a>
        <a href="{{ route('personnel.index') }}" class="btn btn-secondary">بازگشت</a>
    </div>
</div>

<div class="card">
    <h3 style="color: #f96c08; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f96c08;">اطلاعات شخصی</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <strong style="color: #6b7280;">کد پرسنلی:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->employment_code }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">کد ملی:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->national_code }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">نام:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->first_name }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">نام خانوادگی:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->last_name }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">نام پدر:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->father_name ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">جنسیت:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->gender === 'male' ? 'مرد' : 'زن' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">تاریخ تولد:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->birth_date }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="color: #f96c08; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f96c08;">اطلاعات استخدامی</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <strong style="color: #6b7280;">وضعیت استخدام:</strong>
            <p style="font-size: 18px; margin: 5px 0;">
                <span class="badge badge-confirmed">{{ $personnel->employment_status }}</span>
            </p>
        </div>

        <div>
            <strong style="color: #6b7280;">ستاد/شعبه:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->main_or_branch ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">کد دپارتمان:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->department_code ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">دپارتمان:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->department ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">کد محل خدمت:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->service_location_code ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">محل خدمت:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->service_location ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="color: #f96c08; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f96c08;">سایر اطلاعات</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <strong style="color: #6b7280;">نسبت:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->relation ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">شماره حساب:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->account_number ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">فوق العاده:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->funkefalat ?? '-' }}</p>
        </div>

        <div>
            <strong style="color: #6b7280;">وضعیت استخدام همسر:</strong>
            <p style="font-size: 18px; margin: 5px 0;">{{ $personnel->partner_employment_status ?? '-' }}</p>
        </div>
    </div>
</div>

@if($personnel->reservations->count() > 0)
<div class="card">
    <h3 style="color: #f96c08; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f96c08;">تاریخچه رزروها</h3>

    <table>
        <thead>
            <tr>
                <th>شماره رزرو</th>
                <th>نوع پذیرش</th>
                <th>تاریخ ورود</th>
                <th>تاریخ خروج</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personnel->reservations as $reservation)
            <tr>
                <td><strong>#{{ $reservation->id }}</strong></td>
                <td>{{ $reservation->admissionType->name_fa ?? '-' }}</td>
                <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($reservation->check_in_date)->format('Y/m/d') }}</td>
                <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($reservation->check_out_date)->format('Y/m/d') }}</td>
                <td>
                    @php
                        $statusMap = [
                            'pending' => ['label' => 'در انتظار', 'class' => 'badge-pending'],
                            'confirmed' => ['label' => 'تایید شده', 'class' => 'badge-confirmed'],
                            'checked_in' => ['label' => 'ورود انجام شده', 'class' => 'badge-success'],
                            'checked_out' => ['label' => 'خروج انجام شده', 'class' => 'badge-secondary'],
                            'cancelled' => ['label' => 'لغو شده', 'class' => 'badge-cancelled'],
                        ];
                        $status = $statusMap[$reservation->status] ?? ['label' => $reservation->status, 'class' => 'badge'];
                    @endphp
                    <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                </td>
                <td>
                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">مشاهده</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="text-align: left; padding-top: 20px;">
    <a href="{{ route('personnel.edit', $personnel) }}" class="btn btn-primary">ویرایش اطلاعات</a>
    <a href="{{ route('personnel.index') }}" class="btn btn-secondary">بازگشت به لیست</a>
</div>
@endsection
