<?php

namespace App\Exports;

use App\Models\Personnel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonnelExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Personnel::where('is_active', true)->get();
    }

    public function headings(): array
    {
        return [
            'کد پرسنلی',
            'نام',
            'نام خانوادگی',
            'کد ملی',
            'نام پدر',
            'جنسیت',
            'سال تولد',
            'ماه تولد',
            'روز تولد',
            'وضعیت استخدام',
            'ستاد/شعبه',
            'کد دپارتمان',
            'دپارتمان',
            'کد محل خدمت',
            'محل خدمت',
            'نسبت',
            'شماره حساب',
            'فوق العاده',
            'وضعیت استخدام همسر',
        ];
    }

    public function map($personnel): array
    {
        return [
            $personnel->employment_code,
            $personnel->first_name,
            $personnel->last_name,
            $personnel->national_code,
            $personnel->father_name,
            $personnel->gender === 'male' ? 'مرد' : 'زن',
            $personnel->birth_year,
            $personnel->birth_month,
            $personnel->birth_day,
            $personnel->employment_status,
            $personnel->main_or_branch,
            $personnel->department_code,
            $personnel->department,
            $personnel->service_location_code,
            $personnel->service_location,
            $personnel->relation,
            $personnel->account_number,
            $personnel->funkefalat,
            $personnel->partner_employment_status,
        ];
    }
}
