<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Bed;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // پرسنل نمونه
        $personnelData = [
            [
                'employment_code' => '۱۲۳۴۵',
                'first_name' => 'علی',
                'last_name' => 'محمدی',
                'birth_year' => 1360,
                'birth_month' => 5,
                'birth_day' => 15,
                'national_code' => '0012345678',
                'father_name' => 'حسین',
                'service_location' => 'شعبه مرکزی تهران',
                'service_location_code' => '1001',
                'department' => 'امور مالی',
                'department_code' => '101',
                'employment_status' => 'شاغل',
                'gender' => 'male',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۴۶',
                'first_name' => 'فاطمه',
                'last_name' => 'احمدی',
                'birth_year' => 1365,
                'birth_month' => 8,
                'birth_day' => 22,
                'national_code' => '0023456789',
                'father_name' => 'محمد',
                'service_location' => 'شعبه ونک',
                'service_location_code' => '1002',
                'department' => 'امور اداری',
                'department_code' => '102',
                'employment_status' => 'شاغل',
                'gender' => 'female',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۴۷',
                'first_name' => 'رضا',
                'last_name' => 'کریمی',
                'birth_year' => 1358,
                'birth_month' => 3,
                'birth_day' => 10,
                'national_code' => '0034567890',
                'father_name' => 'علی',
                'service_location' => 'شعبه کرج',
                'service_location_code' => '2001',
                'department' => 'فناوری اطلاعات',
                'department_code' => '103',
                'employment_status' => 'شاغل',
                'gender' => 'male',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۴۸',
                'first_name' => 'زهرا',
                'last_name' => 'حسینی',
                'birth_year' => 1370,
                'birth_month' => 11,
                'birth_day' => 5,
                'national_code' => '0045678901',
                'father_name' => 'رضا',
                'service_location' => 'شعبه اصفهان',
                'service_location_code' => '3001',
                'department' => 'منابع انسانی',
                'department_code' => '104',
                'employment_status' => 'شاغل',
                'gender' => 'female',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۴۹',
                'first_name' => 'محمد',
                'last_name' => 'رضایی',
                'birth_year' => 1362,
                'birth_month' => 7,
                'birth_day' => 18,
                'national_code' => '0056789012',
                'father_name' => 'احمد',
                'service_location' => 'شعبه شیراز',
                'service_location_code' => '4001',
                'department' => 'اعتبارات',
                'department_code' => '105',
                'employment_status' => 'شاغل',
                'gender' => 'male',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۵۰',
                'first_name' => 'مریم',
                'last_name' => 'صادقی',
                'birth_year' => 1368,
                'birth_month' => 2,
                'birth_day' => 28,
                'national_code' => '0067890123',
                'father_name' => 'جواد',
                'service_location' => 'شعبه مشهد',
                'service_location_code' => '5001',
                'department' => 'حسابداری',
                'department_code' => '106',
                'employment_status' => 'شاغل',
                'gender' => 'female',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۵۱',
                'first_name' => 'امیر',
                'last_name' => 'نوروزی',
                'birth_year' => 1355,
                'birth_month' => 12,
                'birth_day' => 1,
                'national_code' => '0078901234',
                'father_name' => 'کریم',
                'service_location' => 'شعبه تبریز',
                'service_location_code' => '6001',
                'department' => 'بازرسی',
                'department_code' => '107',
                'employment_status' => 'شاغل',
                'gender' => 'male',
                'is_active' => true,
            ],
            [
                'employment_code' => '۱۲۳۵۲',
                'first_name' => 'سارا',
                'last_name' => 'موسوی',
                'birth_year' => 1372,
                'birth_month' => 6,
                'birth_day' => 14,
                'national_code' => '0089012345',
                'father_name' => 'حسن',
                'service_location' => 'شعبه رشت',
                'service_location_code' => '7001',
                'department' => 'روابط عمومی',
                'department_code' => '108',
                'employment_status' => 'شاغل',
                'gender' => 'female',
                'is_active' => true,
            ],
        ];

        foreach ($personnelData as $data) {
            Personnel::firstOrCreate(
                ['national_code' => $data['national_code']],
                $data
            );
        }

        // مهمانان نمونه
        $guestData = [
            [
                'full_name' => 'احسان جعفری',
                'national_code' => '1234567890',
                'phone' => '09121234567',
                'email' => 'ehsan.jafari@email.com',
                'reason' => 'شرکت در دوره آموزشی',
                'organization' => 'بانک ملی - شعبه اهواز',
            ],
            [
                'full_name' => 'نسرین پورمند',
                'national_code' => '2345678901',
                'phone' => '09132345678',
                'email' => 'nasrin.pourmand@email.com',
                'reason' => 'همایش سالانه',
                'organization' => 'بانک ملی - شعبه یزد',
            ],
            [
                'full_name' => 'حمید اکبری',
                'national_code' => '3456789012',
                'phone' => '09143456789',
                'email' => 'hamid.akbari@email.com',
                'reason' => 'ماموریت اداری',
                'organization' => 'بانک ملی - شعبه قم',
            ],
            [
                'full_name' => 'لیلا خسروی',
                'national_code' => '4567890123',
                'phone' => '09154567890',
                'email' => 'leila.khosravi@email.com',
                'reason' => 'جلسه هماهنگی',
                'organization' => 'بانک ملی - شعبه کرمان',
            ],
            [
                'full_name' => 'سعید باقری',
                'national_code' => '5678901234',
                'phone' => '09165678901',
                'email' => 'saeed.bagheri@email.com',
                'reason' => 'بازدید از مرکز',
                'organization' => 'بانک ملی - اداره کل',
            ],
        ];

        foreach ($guestData as $data) {
            Guest::firstOrCreate(
                ['national_code' => $data['national_code']],
                $data
            );
        }

        // رزروهای نمونه
        // غیرفعال کردن foreign key برای SQLite
        DB::statement('PRAGMA foreign_keys = OFF;');

        $today = Carbon::today();

        $reservations = [
            // رزرو فعال - چک‌این شده (دوره کلاسی)
            [
                'admission_type_id' => 1, // دوره کلاسی
                'personnel_id' => 1,
                'guest_id' => null,
                'room_id' => 1,
                'check_in_date' => $today->copy()->subDays(2),
                'check_out_date' => $today->copy()->addDays(3),
                'actual_check_in' => $today->copy()->subDays(2)->setHour(14),
                'status' => 'checked_in',
                'notes' => 'دوره آموزشی مدیریت مالی',
                'created_by' => 1,
                'beds' => [1],
            ],
            // رزرو فعال - چک‌این شده (همایش)
            [
                'admission_type_id' => 2, // همایش
                'personnel_id' => null,
                'guest_id' => 1,
                'room_id' => 1,
                'check_in_date' => $today->copy()->subDays(1),
                'check_out_date' => $today->copy()->addDays(2),
                'actual_check_in' => $today->copy()->subDays(1)->setHour(16),
                'status' => 'checked_in',
                'notes' => 'همایش بانکداری دیجیتال',
                'created_by' => 1,
                'beds' => [2],
            ],
            // رزرو آینده - رزرو شده
            [
                'admission_type_id' => 1, // دوره کلاسی
                'personnel_id' => 2,
                'guest_id' => null,
                'room_id' => 2,
                'check_in_date' => $today->copy()->addDays(2),
                'check_out_date' => $today->copy()->addDays(5),
                'status' => 'confirmed',
                'notes' => 'دوره آموزشی منابع انسانی',
                'created_by' => 1,
                'beds' => [7],
            ],
            // رزرو آینده - رزرو شده (ماموریت)
            [
                'admission_type_id' => 3, // ماموریت اداری
                'personnel_id' => 3,
                'guest_id' => null,
                'room_id' => 2,
                'check_in_date' => $today->copy()->addDays(1),
                'check_out_date' => $today->copy()->addDays(3),
                'status' => 'confirmed',
                'notes' => 'ماموریت بازرسی شعب',
                'created_by' => 1,
                'beds' => [8],
            ],
            // رزرو تکمیل شده
            [
                'admission_type_id' => 2, // همایش
                'personnel_id' => null,
                'guest_id' => 2,
                'room_id' => 3,
                'check_in_date' => $today->copy()->subDays(7),
                'check_out_date' => $today->copy()->subDays(4),
                'actual_check_in' => $today->copy()->subDays(7)->setHour(15),
                'actual_check_out' => $today->copy()->subDays(4)->setHour(11),
                'status' => 'checked_out',
                'notes' => 'همایش مدیران استانی',
                'created_by' => 1,
                'beds' => [13],
            ],
            // رزرو فعال دیگر
            [
                'admission_type_id' => 1, // دوره کلاسی
                'personnel_id' => 4,
                'guest_id' => null,
                'room_id' => 3,
                'check_in_date' => $today->copy(),
                'check_out_date' => $today->copy()->addDays(4),
                'actual_check_in' => $today->copy()->setHour(10),
                'status' => 'checked_in',
                'notes' => 'دوره فناوری اطلاعات',
                'created_by' => 1,
                'beds' => [14],
            ],
        ];

        foreach ($reservations as $resData) {
            $beds = $resData['beds'];
            unset($resData['beds']);

            $reservation = Reservation::firstOrCreate(
                [
                    'personnel_id' => $resData['personnel_id'],
                    'guest_id' => $resData['guest_id'],
                    'check_in_date' => $resData['check_in_date'],
                ],
                $resData
            );

            // اتصال تخت‌ها به رزرو
            $reservation->beds()->syncWithoutDetaching($beds);

            // به‌روزرسانی وضعیت تخت‌ها برای رزروهای فعال
            if ($resData['status'] === 'checked_in') {
                Bed::whereIn('id', $beds)->update(['status' => 'occupied']);
            }
        }

        // فعال کردن مجدد foreign key
        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info('✅ داده‌های دمو با موفقیت ایجاد شد!');
        $this->command->info('   - 8 پرسنل');
        $this->command->info('   - 5 مهمان');
        $this->command->info('   - 6 رزرو');
    }
}
