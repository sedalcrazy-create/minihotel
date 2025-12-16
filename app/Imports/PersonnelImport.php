<?php

namespace App\Imports;

use App\Models\Personnel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PersonnelImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Personnel([
            'employment_code' => $row['کد_پرسنلی'] ?? $row['employment_code'],
            'first_name' => $row['نام'] ?? $row['first_name'],
            'last_name' => $row['نام_خانوادگی'] ?? $row['last_name'],
            'birth_year' => $row['سال_تولد'] ?? $row['birth_year'],
            'birth_month' => $row['ماه_تولد'] ?? $row['birth_month'],
            'birth_day' => $row['روز_تولد'] ?? $row['birth_day'],
            'national_code' => $row['کد_ملی'] ?? $row['national_code'],
            'father_name' => $row['نام_پدر'] ?? $row['father_name'] ?? null,
            'relation' => $row['نسبت'] ?? $row['relation'] ?? null,
            'account_number' => $row['شماره_حساب'] ?? $row['account_number'] ?? null,
            'service_location_code' => $row['کد_محل_خدمت'] ?? $row['service_location_code'] ?? null,
            'service_location' => $row['محل_خدمت'] ?? $row['service_location'] ?? null,
            'department_code' => $row['کد_دپارتمان'] ?? $row['department_code'] ?? null,
            'department' => $row['دپارتمان'] ?? $row['department'] ?? null,
            'employment_status' => $row['وضعیت_استخدام'] ?? $row['employment_status'],
            'main_or_branch' => $row['ستاد_شعبه'] ?? $row['main_or_branch'] ?? null,
            'funkefalat' => $row['فوق_العاده'] ?? $row['funkefalat'] ?? null,
            'partner_employment_status' => $row['وضعیت_استخدام_همسر'] ?? $row['partner_employment_status'] ?? null,
            'gender' => $row['جنسیت'] ?? $row['gender'] ?? 'male',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.کد_پرسنلی' => 'required',
            '*.نام' => 'required',
            '*.نام_خانوادگی' => 'required',
            '*.سال_تولد' => 'required|integer',
            '*.ماه_تولد' => 'required|integer|between:1,12',
            '*.روز_تولد' => 'required|integer|between:1,31',
            '*.کد_ملی' => 'required|size:10',
            '*.وضعیت_استخدام' => 'required',
        ];
    }
}
