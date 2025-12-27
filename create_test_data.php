<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Course;
use App\Models\Conference;

// Get admin user ID
$adminUser = \App\Models\User::where('email', 'admin@bank.ir')->first();
if (!$adminUser) {
    echo "❌ Admin user not found\n";
    exit(1);
}

// Calculate future dates
$today = \Carbon\Carbon::today();
$courseStart = $today->copy()->addDays(7)->format('Y-m-d');
$courseEnd = $today->copy()->addDays(14)->format('Y-m-d');
$confStart = $today->copy()->addDays(20)->format('Y-m-d');
$confEnd = $today->copy()->addDays(22)->format('Y-m-d');

echo "📝 Creating test course ($courseStart to $courseEnd)...\n";
$course = Course::updateOrCreate(
    ['code' => 'TEST-COURSE-001'],
    [
        'name' => 'دوره تست Playwright',
        'start_date' => $courseStart,
        'end_date' => $courseEnd,
        'capacity' => 30,
        'location' => 'سالن اصلی',
        'description' => 'دوره تست برای Playwright',
        'is_active' => true,
        'created_by' => $adminUser->id
    ]
);
echo "✅ Course created: ID {$course->id}\n\n";

echo "📝 Creating test conference ($confStart to $confEnd)...\n";
$conf = Conference::updateOrCreate(
    ['code' => 'TEST-CONF-001'],
    [
        'name' => 'همایش تست Playwright',
        'start_date' => $confStart,
        'end_date' => $confEnd,
        'organizer' => 'تیم تست',
        'capacity' => 50,
        'description' => 'همایش تست برای Playwright',
        'is_active' => true,
        'created_by' => $adminUser->id
    ]
);
echo "✅ Conference created: ID {$conf->id}\n\n";

echo "🎉 Test data created successfully!\n";
