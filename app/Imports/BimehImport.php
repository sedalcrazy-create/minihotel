<?php

namespace App\Imports;

use App\Models\Personnel;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class BimehImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public int $inserted = 0;
    public int $updated = 0;
    public int $deactivated = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        $existingCodes = Personnel::pluck('id', 'employment_code')->toArray();
        $processedCodes = [];

        foreach ($rows as $index => $row) {
            try {
                // استخراج کد پرسنلی (کلید اصلی)
                $employmentCode = $this->getFieldValue($row, ['astkhdamy', 'استخدامی', 'کد_پرسنلی', 'کد_استخدام', 'employment_code', 'کد پرسنلی', 'id']);

                if (empty($employmentCode)) {
                    $this->errors[] = "ردیف " . ($index + 2) . ": کد پرسنلی خالی است";
                    continue;
                }

                $processedCodes[] = $employmentCode;

                // استخراج داده‌ها
                $data = [
                    'employment_code' => $employmentCode,
                    'first_name' => $this->getFieldValue($row, ['nam', 'نام', 'first_name']),
                    'last_name' => $this->getFieldValue($row, ['nam_khanoadgy', 'نام_خانوادگی', 'نام خانوادگی', 'last_name']),
                    'national_code' => $this->cleanNationalCode($this->getFieldValue($row, ['kd_mly', 'کد_ملی', 'کد ملی', 'national_code'])),
                    'father_name' => $this->getFieldValue($row, ['pdr', 'نام_پدر', 'نام پدر', 'پدر', 'father_name']),
                    'birth_year' => $this->getFieldValue($row, ['sal_told', 'سال_تولد', 'سال تولد', 'birth_year']),
                    'birth_month' => $this->getFieldValue($row, ['mah_told', 'ماه_تولد', 'ماه تولد', 'birth_month']),
                    'birth_day' => $this->getFieldValue($row, ['roz_told', 'روز_تولد', 'روز تولد', 'birth_day']),
                    'gender' => $this->parseGender($row),
                    'employment_status' => $this->getFieldValue($row, ['odaayt_khdmt', 'وضعیت_استخدام', 'وضعیت استخدام', 'وضعیت خدمت', 'employment_status']),
                    'main_or_branch' => $this->getFieldValue($row, ['asly_fraay', 'ستاد_شعبه', 'ستاد/شعبه', 'اصلی-فرعی', 'اصلي-فرعي', 'main_or_branch']),
                    'relation' => $this->getFieldValue($row, ['nsbt', 'نسبت', 'relation']),
                    'account_number' => $this->getFieldValue($row, ['shmarh_hsab', 'شماره_حساب', 'شماره حساب', 'account_number']),
                    'funkefalat' => $this->getFieldValue($row, ['فوق_العاده', 'فوق العاده', 'funkefalat']),
                    'person_type' => $this->parsePersonType($row),
                    'colleague_status' => $this->parseColleagueStatus($row),
                    'last_sync_date' => date('Y-m-d'),
                ];

                // Lookup محل خدمت از روی کد
                $serviceLocationCode = $this->getFieldValue($row, ['kd_mhl_khdmt', 'کد_محل_خدمت', 'کد محل خدمت', 'service_location_code']);
                if ($serviceLocationCode) {
                    $data['service_location_code'] = $serviceLocationCode;
                    $serviceLocation = DB::table('service_locations')->where('code', $serviceLocationCode)->first();
                    if ($serviceLocation) {
                        $data['service_location'] = $serviceLocation->name;
                    }
                }

                // Lookup دپارتمان از روی کد
                $departmentCode = $this->getFieldValue($row, ['kd_adarh_amor', 'کد_دپارتمان', 'کد دپارتمان', 'کد اداره امور', 'کد_اداره', 'department_code']);
                if ($departmentCode) {
                    $data['department_code'] = $departmentCode;
                    $department = DB::table('departments')->where('code', $departmentCode)->first();
                    if ($department) {
                        $data['department'] = $department->name;
                    }
                }

                // حذف مقادیر خالی
                $data = array_filter($data, function($value) {
                    return !is_null($value) && $value !== '';
                });

                // بررسی وجود پرسنل
                if (isset($existingCodes[$employmentCode])) {
                    // آپدیت
                    $personnel = Personnel::find($existingCodes[$employmentCode]);
                    $personnel->update($data);
                    $this->updated++;
                } else {
                    // افزودن جدید
                    Personnel::create($data);
                    $this->inserted++;
                }

            } catch (\Exception $e) {
                $this->errors[] = "ردیف " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        // غیرفعال کردن پرسنلی که در فایل جدید نیست
        if (!empty($processedCodes)) {
            $deactivatedCount = Personnel::whereNotIn('employment_code', $processedCodes)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'last_sync_date' => now()->toDateString(),
                ]);
            $this->deactivated = $deactivatedCount;
        }

        // ثبت لاگ
        ActivityLog::log('sync', 'Personnel', null, sprintf(
            'همگام‌سازی بیمه: %d افزودن، %d آپدیت، %d غیرفعال',
            $this->inserted,
            $this->updated,
            $this->deactivated
        ));
    }

    /**
     * پاک‌سازی کد ملی (حذف خط تیره و فضای خالی)
     */
    private function cleanNationalCode($nationalCode): ?string
    {
        if (empty($nationalCode)) {
            return null;
        }

        // حذف خط تیره، فاصله و کاراکترهای غیرضروری
        $cleaned = preg_replace('/[^0-9]/', '', $nationalCode);

        // فقط 10 رقم اول
        return substr($cleaned, 0, 10);
    }

    /**
     * جستجوی مقدار فیلد با نام‌های مختلف
     */
    private function getFieldValue($row, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            if (isset($row[$name]) && !empty($row[$name])) {
                return trim($row[$name]);
            }
        }
        return null;
    }

    /**
     * تبدیل جنسیت
     */
    private function parseGender($row): string
    {
        $gender = $this->getFieldValue($row, ['gnsyt', 'جنسیت', 'gender', 'sex']);

        if (empty($gender)) {
            return 'male';
        }

        $gender = strtolower(trim($gender));

        // نقشه تبدیل
        $map = [
            'مرد' => 'male',
            'مذکر' => 'male',
            'آقا' => 'male',
            'male' => 'male',
            'm' => 'male',
            'زن' => 'female',
            'مونث' => 'female',
            'خانم' => 'female',
            'female' => 'female',
            'f' => 'female',
        ];

        return $map[$gender] ?? 'male';
    }

    /**
     * تبدیل نوع فرد (اصلی/غیراصلی)
     */
    private function parsePersonType($row): string
    {
        $value = $this->getFieldValue($row, ['نوع_فرد', 'افراد', 'person_type', 'نوع فرد']);

        if (empty($value)) {
            return 'اصلی';
        }

        $value = trim($value);

        if (str_contains($value, 'غیر') || $value === 'غیراصلی') {
            return 'غیراصلی';
        }

        return 'اصلی';
    }

    /**
     * تبدیل وضعیت همکار
     */
    private function parseColleagueStatus($row): string
    {
        $value = $this->getFieldValue($row, ['odaayt_khdmt_hmkar', 'وضعیت_خدمت_همکار', 'وضعیت همکار', 'colleague_status', 'وضعیت خدمت']);

        if (empty($value)) {
            return 'شاغل';
        }

        return trim($value);
    }

    /**
     * تعداد ردیف‌های هر chunk
     */
    public function chunkSize(): int
    {
        return 500; // پردازش 500 ردیف در هر بار (برای کاهش مصرف حافظه)
    }

    /**
     * تعداد ردیف‌های هر batch insert
     */
    public function batchSize(): int
    {
        return 100; // insert به صورت batch 100 تایی
    }

    /**
     * شماره ردیف شروع (برای skip کردن header)
     */
    public function headingRow(): int
    {
        return 1;
    }
}
