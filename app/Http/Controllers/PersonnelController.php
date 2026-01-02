<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\ActivityLog;
use App\Imports\PersonnelImport;
use App\Imports\BimehImport;
use App\Exports\PersonnelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $query = Personnel::where('is_active', true);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('employment_code', 'LIKE', "%{$search}%")
                  ->orWhere('national_code', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $personnel = $query->orderBy('first_name')->paginate(20);

        return view('personnel.index', compact('personnel'));
    }

    public function create()
    {
        $serviceLocations = DB::table('service_locations')->orderBy('name')->get();
        $departments = DB::table('departments')->orderBy('name')->get();

        return view('personnel.create', compact('serviceLocations', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employment_code' => 'required|string|max:50|unique:personnel,employment_code',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'national_code' => 'required|string|size:10|unique:personnel,national_code',
            'father_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_year' => 'required|integer|min:1300|max:1400',
            'birth_month' => 'required|integer|min:1|max:12',
            'birth_day' => 'required|integer|min:1|max:31',
            'employment_status' => 'required|string|max:100',
            'main_or_branch' => 'nullable|string|max:50',
            'department_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'service_location_code' => 'nullable|string|max:50',
            'service_location' => 'nullable|string',
            'relation' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'funkefalat' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $personnel = Personnel::create($validated);

            ActivityLog::log('create', 'Personnel', $personnel->id, 'پرسنل جدید ثبت شد');

            DB::commit();

            return redirect()->route('personnel.index')
                ->with('success', 'پرسنل با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در ثبت پرسنل: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Personnel $personnel)
    {
        $personnel->load('reservations.room.unit');
        return view('personnel.show', compact('personnel'));
    }

    public function edit(Personnel $personnel)
    {
        $serviceLocations = DB::table('service_locations')->orderBy('name')->get();
        $departments = DB::table('departments')->orderBy('name')->get();

        return view('personnel.edit', compact('personnel', 'serviceLocations', 'departments'));
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'employment_code' => 'required|string|max:50|unique:personnel,employment_code,' . $personnel->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'national_code' => 'required|string|size:10|unique:personnel,national_code,' . $personnel->id,
            'father_name' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_year' => 'required|integer|min:1300|max:1400',
            'birth_month' => 'required|integer|min:1|max:12',
            'birth_day' => 'required|integer|min:1|max:31',
            'employment_status' => 'required|string|max:100',
            'main_or_branch' => 'nullable|string|max:50',
            'department_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'service_location_code' => 'nullable|string|max:50',
            'service_location' => 'nullable|string',
            'relation' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'funkefalat' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $personnel->update($validated);

            ActivityLog::log('update', 'Personnel', $personnel->id, 'اطلاعات پرسنل بروزرسانی شد');

            DB::commit();

            return redirect()->route('personnel.show', $personnel)
                ->with('success', 'اطلاعات پرسنل با موفقیت بروزرسانی شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در بروزرسانی: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Personnel $personnel)
    {
        DB::beginTransaction();
        try {
            // Soft delete by marking as inactive
            $personnel->update(['is_active' => false]);

            ActivityLog::log('delete', 'Personnel', $personnel->id, 'پرسنل غیرفعال شد');

            DB::commit();

            return redirect()->route('personnel.index')
                ->with('success', 'پرسنل با موفقیت غیرفعال شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در حذف: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new PersonnelExport, 'personnel-' . date('Y-m-d') . '.xlsx');
    }

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // سربرگ - سطر 1
        $headers = [
            'کد_پرسنلی',
            'نام',
            'نام_خانوادگی',
            'کد_ملی',
            'نام_پدر',
            'جنسیت',
            'سال_تولد',
            'ماه_تولد',
            'روز_تولد',
            'وضعیت_استخدام',
            'ستاد_شعبه',
            'کد_دپارتمان',
            'کد_محل_خدمت',
            'نسبت',
            'شماره_حساب',
            'فوق_العاده',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFf96c08');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getColumnDimension($col)->setWidth(18);
            $col++;
        }

        // نمونه داده - سطرهای 2 و 3
        $sampleData = [
            ['12345', 'علی', 'احمدی', '1234567890', 'محمد', 'male', '1370', '5', '15', 'رسمی', 'ستاد', '290', '1', 'کارمند', '1234567890123456', ''],
            ['12346', 'فاطمه', 'محمدی', '0987654321', 'حسن', 'female', '1375', '3', '20', 'پیمانی', 'شعبه', '100', '10', 'کارمند', '', ''],
        ];

        $rowNum = 2;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $rowNum, $value);
                $sheet->getStyle($col . $rowNum)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE8F5E9');
                $col++;
            }
            $rowNum++;
        }

        // تنظیمات کلی
        $sheet->getStyle('A1:P3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A1:P3')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'template-personnel.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function updateTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // سربرگ
        $headers = [
            'کد_پرسنلی',
            'نام',
            'نام_خانوادگی',
            'کد_ملی',
            'نام_پدر',
            'جنسیت',
            'سال_تولد',
            'ماه_تولد',
            'روز_تولد',
            'وضعیت_استخدام',
            'ستاد_شعبه',
            'کد_دپارتمان',
            'کد_محل_خدمت',
            'نسبت',
            'شماره_حساب',
            'فوق_العاده',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFf96c08');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getColumnDimension($col)->setWidth(18);
            $col++;
        }

        // داده‌های پرسنل فعلی
        $personnel = Personnel::where('is_active', true)->orderBy('employment_code')->get();

        $rowNum = 2;
        foreach ($personnel as $person) {
            $sheet->setCellValue('A' . $rowNum, $person->employment_code);
            $sheet->setCellValue('B' . $rowNum, $person->first_name);
            $sheet->setCellValue('C' . $rowNum, $person->last_name);
            $sheet->setCellValue('D' . $rowNum, $person->national_code);
            $sheet->setCellValue('E' . $rowNum, $person->father_name);
            $sheet->setCellValue('F' . $rowNum, $person->gender);
            $sheet->setCellValue('G' . $rowNum, $person->birth_year);
            $sheet->setCellValue('H' . $rowNum, $person->birth_month);
            $sheet->setCellValue('I' . $rowNum, $person->birth_day);
            $sheet->setCellValue('J' . $rowNum, $person->employment_status);
            $sheet->setCellValue('K' . $rowNum, $person->main_or_branch);
            $sheet->setCellValue('L' . $rowNum, $person->department_code);
            $sheet->setCellValue('M' . $rowNum, $person->service_location_code);
            $sheet->setCellValue('N' . $rowNum, $person->relation);
            $sheet->setCellValue('O' . $rowNum, $person->account_number);
            $sheet->setCellValue('P' . $rowNum, $person->funkefalat);
            $rowNum++;
        }

        // تنظیمات کلی
        $lastRow = $rowNum - 1;
        $sheet->getStyle('A1:P' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A1:P' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'personnel-update-' . date('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new PersonnelImport, $request->file('file'));

            ActivityLog::log('import', 'Personnel', null, 'فایل اکسل پرسنل وارد شد');

            return redirect()->route('personnel.index')
                ->with('success', 'فایل اکسل با موفقیت وارد شد.');

        } catch (\Exception $e) {
            return back()->with('error', 'خطا در وارد کردن فایل: ' . $e->getMessage());
        }
    }

    public function syncFromBimeh(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:51200', // 50MB
        ]);

        try {
            ini_set('memory_limit', '1024M'); // 1GB برای فایل‌های بزرگ
            set_time_limit(600); // 10 دقیقه برای فایل‌های بزرگ

            $import = new BimehImport();
            Excel::import($import, $request->file('file'));

            $message = sprintf(
                '✅ همگام‌سازی بیمه انجام شد: %d افزودن، %d آپدیت، %d غیرفعال',
                $import->inserted,
                $import->updated,
                $import->deactivated
            );

            // اگر خطا داشتیم، به پیام اضافه کنیم
            if (!empty($import->errors)) {
                $errorCount = count($import->errors);
                $message .= sprintf(' | ⚠️ %d خطا', $errorCount);
            }

            return redirect()->route('personnel.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'خطا در همگام‌سازی بیمه: ' . $e->getMessage());
        }
    }
}
