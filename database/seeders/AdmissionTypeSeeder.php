<?php

namespace Database\Seeders;

use App\Models\AdmissionType;
use Illuminate\Database\Seeder;

class AdmissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'دوره کلاسی',
                'code' => 'class',
                'has_reservation' => false,
                'reservation_days_before' => null,
                'description' => 'پذیرش برای دوره‌های آموزشی - بدون نیاز به رزرو قبلی',
                'is_active' => true,
            ],
            [
                'name' => 'همایش',
                'code' => 'conference',
                'has_reservation' => false,
                'reservation_days_before' => null,
                'description' => 'پذیرش برای همایش‌ها و رویدادها - بدون نیاز به رزرو قبلی',
                'is_active' => true,
            ],
            [
                'name' => 'ماموریت اداری / متفرقه',
                'code' => 'mission',
                'has_reservation' => true,
                'reservation_days_before' => 3,
                'description' => 'پذیرش برای ماموریت‌های اداری - نیاز به نامه رسمی 1-3 روز قبل',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            AdmissionType::create($type);
        }
    }
}
