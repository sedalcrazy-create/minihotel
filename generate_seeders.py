#!/usr/bin/env python3
import openpyxl
from collections import OrderedDict

print("📖 در حال خواندن فایل...")
wb = openpyxl.load_workbook('Bimeh_14040307 (1).xlsx', read_only=True, data_only=True)
sheet = wb.active

headers = [cell.value for cell in sheet[1]]

dept_code_col = None
dept_name_col = None
service_code_col = None
service_name_col = None

for i, h in enumerate(headers):
    h_str = str(h).strip() if h else ''
    if 'کد' in h_str and ('دپارتمان' in h_str or 'اداره' in h_str):
        dept_code_col = i
    if ('دپارتمان' in h_str or 'اداره' in h_str) and 'کد' not in h_str:
        dept_name_col = i
    if 'کد' in h_str and 'محل' in h_str:
        service_code_col = i
    if 'محل' in h_str and 'کد' not in h_str and 'خدمت' in h_str:
        service_name_col = i

departments = OrderedDict()
service_locations = OrderedDict()

print("📊 در حال پردازش...")
for row in sheet.iter_rows(min_row=2, values_only=True):
    if dept_code_col is not None and dept_name_col is not None:
        code = str(row[dept_code_col]).strip() if row[dept_code_col] else None
        name = str(row[dept_name_col]).strip() if row[dept_name_col] else None
        if code and name and code != 'None' and name != 'None':
            departments[code] = name

    if service_code_col is not None and service_name_col is not None:
        code = str(row[service_code_col]).strip() if row[service_code_col] else None
        name = str(row[service_name_col]).strip() if row[service_name_col] else None
        if code and name and code != 'None' and name != 'None':
            service_locations[code] = name

print(f"✅ {len(departments)} دپارتمان و {len(service_locations)} محل خدمت پیدا شد")

# Generate Departments Seeder
print("✍️  در حال نوشتن DepartmentsSeeder.php...")
with open('database/seeders/DepartmentsSeeder.php', 'w', encoding='utf-8') as f:
    f.write("""<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
""")
    for code, name in sorted(departments.items()):
        f.write(f"            ['code' => '{code}', 'name' => '{name}'],\n")

    f.write("""        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert(
                ['code' => $department['code']],
                $department
            );
        }
    }
}
""")

# Generate Service Locations Seeder
print("✍️  در حال نوشتن ServiceLocationsSeeder.php...")
with open('database/seeders/ServiceLocationsSeeder.php', 'w', encoding='utf-8') as f:
    f.write("""<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class ServiceLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $serviceLocations = [
""")
    for code, name in sorted(service_locations.items()):
        # Escape single quotes
        name_escaped = name.replace("'", "\\'")
        f.write(f"            ['code' => '{code}', 'name' => '{name_escaped}'],\n")

    f.write("""        ];

        foreach ($serviceLocations as $location) {
            DB::table('service_locations')->updateOrInsert(
                ['code' => $location['code']],
                $location
            );
        }
    }
}
""")

print("✅ Seeder ها ساخته شدند!")
print(f"   - database/seeders/DepartmentsSeeder.php ({len(departments)} دپارتمان)")
print(f"   - database/seeders/ServiceLocationsSeeder.php ({len(service_locations)} محل خدمت)")
