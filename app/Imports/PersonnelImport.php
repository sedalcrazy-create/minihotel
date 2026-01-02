<?php

namespace App\Imports;

use App\Models\Personnel;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PersonnelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [
                'employment_code' => $this->getFieldValue($row, ['کد_پرسنلی', 'employment_code']),
                'first_name' => $this->getFieldValue($row, ['نام', 'first_name']),
                'last_name' => $this->getFieldValue($row, ['نام_خانوادگی', 'نام خانوادگی', 'last_name']),
                'birth_year' => $this->getFieldValue($row, ['سال_تولد', 'سال تولد', 'birth_year']),
                'birth_month' => $this->getFieldValue($row, ['ماه_تولد', 'ماه تولد', 'birth_month']),
                'birth_day' => $this->getFieldValue($row, ['روز_تولد', 'روز تولد', 'birth_day']),
                'national_code' => $this->getFieldValue($row, ['کد_ملی', 'کد ملی', 'national_code']),
                'father_name' => $this->getFieldValue($row, ['نام_پدر', 'نام پدر', 'father_name']),
                'relation' => $this->getFieldValue($row, ['نسبت', 'relation']),
                'account_number' => $this->getFieldValue($row, ['شماره_حساب', 'شماره حساب', 'account_number']),
                'employment_status' => $this->getFieldValue($row, ['وضعیت_استخدام', 'وضعیت استخدام', 'employment_status']),
                'main_or_branch' => $this->getFieldValue($row, ['ستاد_شعبه', 'ستاد/شعبه', 'main_or_branch']),
                'funkefalat' => $this->getFieldValue($row, ['فوق_العاده', 'فوق العاده', 'funkefalat']),
                'gender' => $this->parseGender($row),
            ];

            // Lookup محل خدمت از روی کد
            $serviceLocationCode = $this->getFieldValue($row, ['کد_محل_خدمت', 'کد محل خدمت', 'service_location_code']);
            if ($serviceLocationCode) {
                $data['service_location_code'] = $serviceLocationCode;
                $serviceLocation = DB::table('service_locations')->where('code', $serviceLocationCode)->first();
                if ($serviceLocation) {
                    $data['service_location'] = $serviceLocation->name;
                }
            }

            // Lookup دپارتمان از روی کد
            $departmentCode = $this->getFieldValue($row, ['کد_دپارتمان', 'کد دپارتمان', 'department_code']);
            if ($departmentCode) {
                $data['department_code'] = $departmentCode;
                $department = DB::table('departments')->where('code', $departmentCode)->first();
                if ($department) {
                    $data['department'] = $department->name;
                }
            }

            // حذف مقادیر null
            $data = array_filter($data, function($value) {
                return !is_null($value) && $value !== '';
            });

            // بررسی فیلدهای الزامی
            if (empty($data['employment_code'])) {
                continue; // رد کردن ردیف‌های بدون کد پرسنلی
            }

            // آپدیت یا ایجاد
            Personnel::updateOrCreate(
                ['employment_code' => $data['employment_code']],
                $data
            );
        }
    }

    /**
     * جستجوی مقدار فیلد با نام‌های مختلف
     */
    private function getFieldValue($row, array $possibleNames)
    {
        // ابتدا جستجوی مستقیم
        foreach ($possibleNames as $name) {
            if (isset($row[$name]) && !empty($row[$name])) {
                return trim($row[$name]);
            }
        }

        // جستجو با حذف شماره از ابتدای نام ستون (مثل "2.کد_پرسنلی" -> "کد_پرسنلی")
        foreach ($row as $key => $value) {
            if (!empty($value)) {
                // حذف شماره و نقطه از ابتدای نام ستون
                $cleanKey = preg_replace('/^\d+\./', '', $key);

                if (in_array($cleanKey, $possibleNames)) {
                    return trim($value);
                }
            }
        }

        return null;
    }

    /**
     * تبدیل جنسیت
     */
    private function parseGender($row): string
    {
        $gender = $this->getFieldValue($row, ['جنسیت', 'gender']);

        if (empty($gender)) {
            return 'male';
        }

        $gender = strtolower(trim($gender));

        $map = [
            'مرد' => 'male',
            'آقا' => 'male',
            'male' => 'male',
            'm' => 'male',
            'زن' => 'female',
            'خانم' => 'female',
            'female' => 'female',
            'f' => 'female',
        ];

        return $map[$gender] ?? 'male';
    }
}
