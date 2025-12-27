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
                $employmentCode = $this->getFieldValue($row, ['کد_پرسنلی', 'کد_استخدام', 'employment_code', 'کد پرسنلی']);

                if (empty($employmentCode)) {
                    $this->errors[] = "ردیف " . ($index + 2) . ": کد پرسنلی خالی است";
                    continue;
                }

                $processedCodes[] = $employmentCode;

                // استخراج داده‌ها
                $data = [
                    'employment_code' => $employmentCode,
                    'first_name' => $this->getFieldValue($row, ['نام', 'first_name']),
                    'last_name' => $this->getFieldValue($row, ['نام_خانوادگی', 'نام خانوادگی', 'last_name']),
                    'national_code' => $this->getFieldValue($row, ['کد_ملی', 'کد ملی', 'national_code']),
                    'father_name' => $this->getFieldValue($row, ['نام_پدر', 'نام پدر', 'father_name']),
                    'birth_year' => $this->getFieldValue($row, ['سال_تولد', 'سال تولد', 'birth_year']),
                    'birth_month' => $this->getFieldValue($row, ['ماه_تولد', 'ماه تولد', 'birth_month']),
                    'birth_day' => $this->getFieldValue($row, ['روز_تولد', 'روز تولد', 'birth_day']),
                    'gender' => $this->parseGender($row),
                    'employment_status' => $this->getFieldValue($row, ['وضعیت_استخدام', 'وضعیت استخدام', 'employment_status']),
                    'main_or_branch' => $this->getFieldValue($row, ['ستاد_شعبه', 'ستاد/شعبه', 'main_or_branch']),
                    'department_code' => $this->getFieldValue($row, ['کد_دپارتمان', 'کد دپارتمان', 'department_code']),
                    'department' => $this->getFieldValue($row, ['دپارتمان', 'department', 'اداره']),
                    'service_location_code' => $this->getFieldValue($row, ['کد_محل_خدمت', 'کد محل خدمت', 'service_location_code']),
                    'service_location' => $this->getFieldValue($row, ['محل_خدمت', 'محل خدمت', 'service_location']),
                    'relation' => $this->getFieldValue($row, ['نسبت', 'relation']),
                    'account_number' => $this->getFieldValue($row, ['شماره_حساب', 'شماره حساب', 'account_number']),
                    'funkefalat' => $this->getFieldValue($row, ['فوق_العاده', 'فوق العاده', 'funkefalat']),
                    'partner_employment_status' => $this->getFieldValue($row, ['وضعیت_استخدام_همسر', 'وضعیت استخدام همسر', 'partner_employment_status']),

                    // فیلدهای بیمه
                    'person_type' => $this->parsePersonType($row),
                    'colleague_status' => $this->parseColleagueStatus($row),
                    'last_sync_date' => now()->toDateString(),
                ];

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
        $gender = $this->getFieldValue($row, ['جنسیت', 'gender', 'sex']);

        if (empty($gender)) {
            return 'male';
        }

        $gender = strtolower(trim($gender));

        // نقشه تبدیل
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
        $value = $this->getFieldValue($row, ['وضعیت_خدمت_همکار', 'وضعیت همکار', 'colleague_status', 'وضعیت خدمت']);

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
