<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\ActivityLog;
use App\Imports\PersonnelImport;
use App\Exports\PersonnelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PersonnelController extends Controller
{
    public function index()
    {
        $personnel = Personnel::where('is_active', true)
            ->orderBy('first_name')
            ->paginate(20);

        return view('personnel.index', compact('personnel'));
    }

    public function create()
    {
        return view('personnel.create');
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
            'partner_employment_status' => 'nullable|string|max:100',
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
        return view('personnel.edit', compact('personnel'));
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
            'partner_employment_status' => 'nullable|string|max:100',
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

        // راهنمای استفاده - سطر اول
        $sheet->setCellValue('A1', '📋 راهنمای استفاده از فایل ورود اکسل پرسنل - اداره کل آموزش بانک ملی');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFf96c08');
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getRowDimension(1)->setRowHeight(30);

        // توضیحات - سطرهای 2 تا 6
        $instructions = [
            ['⚠️ نکات مهم:', ''],
            ['1️⃣ ستون‌های با علامت * الزامی هستند', ''],
            ['2️⃣ کد پرسنلی و کد ملی باید یکتا باشند', ''],
            ['3️⃣ کد ملی باید دقیقاً 10 رقم باشد', ''],
            ['4️⃣ وضعیت استخدام فقط می‌تواند: رسمی، قراردادی یا موقت باشد', ''],
            ['5️⃣ سطر 8 به بعد را با اطلاعات پرسنل پر کنید', ''],
        ];

        $row = 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction[0]);
            $sheet->mergeCells('A' . $row . ':S' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFF3E0');
            $row++;
        }

        // سربرگ - سطر 8
        $headers = [
            'کد_پرسنلی *',
            'نام *',
            'نام_خانوادگی *',
            'کد_ملی *',
            'نام_پدر',
            'جنسیت *',
            'سال_تولد *',
            'ماه_تولد *',
            'روز_تولد *',
            'وضعیت_استخدام *',
            'ستاد_شعبه',
            'کد_دپارتمان',
            'دپارتمان',
            'کد_محل_خدمت',
            'محل_خدمت',
            'نسبت',
            'شماره_حساب',
            'فوق_العاده',
            'وضعیت_استخدام_همسر',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '8', $header);
            $sheet->getStyle($col . '8')->getFont()->setBold(true);
            $sheet->getStyle($col . '8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFe37415');
            $sheet->getStyle($col . '8')->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getColumnDimension($col)->setWidth(20);
            $col++;
        }

        // نمونه داده - سطر 9
        $sampleData = [
            '12345',
            'علی',
            'احمدی',
            '1234567890',
            'محمد',
            'male',
            '1370',
            '5',
            '15',
            'رسمی',
            'ستاد',
            'EDU01',
            'آموزش',
            'LOC01',
            'مرکز آموزش تهران',
            'خود',
            '1234567890123456',
            'مبلغ نمونه',
            'شاغل',
        ];

        $col = 'A';
        foreach ($sampleData as $data) {
            $sheet->setCellValue($col . '9', $data);
            $sheet->getStyle($col . '9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE8F5E9');
            $col++;
        }

        // تنظیمات کلی
        $sheet->getStyle('A1:S9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A8:S9')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'template-personnel-import.xlsx';
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
}
