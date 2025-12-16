<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'مدیر سیستم',
                'email' => 'admin@bank.ir',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'اپراتور',
                'email' => 'operator@bank.ir',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'is_active' => true,
            ],
            [
                'name' => 'مدیر خوابگاه',
                'email' => 'manager@bank.ir',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'is_active' => true,
            ],
            [
                'name' => 'مسئول نظافت',
                'email' => 'cleaning@bank.ir',
                'password' => Hash::make('password'),
                'role' => 'cleaning_staff',
                'is_active' => true,
            ],
            [
                'name' => 'مسئول تعمیرات',
                'email' => 'maintenance@bank.ir',
                'password' => Hash::make('password'),
                'role' => 'maintenance_staff',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
