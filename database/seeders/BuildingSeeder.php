<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Unit;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main building
        $building = Building::create([
            'name' => 'خوابگاه اصلی بانک ملی',
            'code' => 'MAIN',
            'description' => 'خوابگاه اصلی اداره آموزش بانک ملی - 22 واحد، 132 تخت',
            'is_active' => true,
        ]);

        // Create 22 units
        for ($unitNumber = 1; $unitNumber <= 22; $unitNumber++) {
            $section = $unitNumber <= 12 ? 'east' : 'west';

            $unit = Unit::create([
                'building_id' => $building->id,
                'number' => $unitNumber,
                'section' => $section,
                'is_active' => true,
            ]);

            // Each unit has 1 room with 6 beds
            $room = Room::create([
                'unit_id' => $unit->id,
                'number' => 1,
                'capacity' => 6,
                'is_active' => true,
            ]);

            // Create 6 beds for each room
            for ($bedNumber = 1; $bedNumber <= 6; $bedNumber++) {
                Bed::create([
                    'room_id' => $room->id,
                    'number' => $bedNumber,
                    'status' => 'available',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✅ ساختمان اصلی با 22 واحد و 132 تخت ایجاد شد');
    }
}
