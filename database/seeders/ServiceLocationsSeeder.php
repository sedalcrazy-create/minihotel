<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $serviceLocations = [
            ['code' => '1', 'name' => 'اداره کل حوزه مديريت'],
            ['code' => '17', 'name' => 'اداره کل سازمان و روشها'],
            ['code' => '23', 'name' => 'بيمارستان'],
            ['code' => '18', 'name' => 'اداره کل حفاظت فيزيکي و فني'],
            ['code' => '5', 'name' => 'اداره کل بازرسي'],
            ['code' => '9945', 'name' => 'اداره کل خارجه'],
            ['code' => '2227', 'name' => 'اداره کل توسعه سيستمها'],
            ['code' => '2228', 'name' => 'اداره کل اطلاعات بانکي'],
            ['code' => '9957', 'name' => 'اداره کل مبارزه با پولشويي و تامين مالي تروريسم'],
            ['code' => '9130', 'name' => 'اداره کل حسابداري مديريت و بودجه'],
            ['code' => '11', 'name' => 'اداره کل مهندسي و املاک'],
            ['code' => '3', 'name' => 'اداره کل سرمايه انساني'],
            ['code' => '26', 'name' => 'اداره کل پيگيري و وصول مطالبات'],
            ['code' => '9137', 'name' => 'اداره کل رعايت قوانين و مقررات'],
            ['code' => '14', 'name' => 'اداره کل رفاه و درمان'],
            ['code' => '9119', 'name' => 'اداره کل حسابرسي داخلي'],
            ['code' => '9975', 'name' => 'متفرقه کارگزيني'],
            ['code' => '13', 'name' => 'اداره کل کارپردازي'],
            ['code' => '9936', 'name' => 'مديريت امور شعب منطقه يک کشور'],
            ['code' => '10', 'name' => 'اداره کل حسابداري مالي'],
            ['code' => '9955', 'name' => 'اداره کل بررسي طرحها'],
            ['code' => '9', 'name' => 'اداره کل نظام هاي پرداخت'],
            ['code' => '9954', 'name' => 'اداره کل گزينش'],
            ['code' => '21', 'name' => 'اداره کل آموزش'],
            ['code' => '290', 'name' => 'ادارات مرکزي'],
        ];

        foreach ($serviceLocations as $location) {
            DB::table('service_locations')->updateOrInsert(
                ['code' => $location['code']],
                $location
            );
        }
    }
}
