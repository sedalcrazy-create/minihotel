<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConferenceController extends Controller
{
    public function index()
    {
        $conferences = Conference::with('creator')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('conferences.index', compact('conferences'));
    }

    public function create()
    {
        return view('conferences.create');
    }

    public function store(Request $request)
    {
        // تبدیل تاریخ شمسی به میلادی
        if ($request->start_date) {
            $startDate = $this->convertPersianToEnglish($request->start_date);
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $startDate);
            $request->merge(['start_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }
        if ($request->end_date) {
            $endDate = $this->convertPersianToEnglish($request->end_date);
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $endDate);
            $request->merge(['end_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:conferences',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'organizer' => 'nullable|string|max:200',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        $conference = Conference::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Conference',
            'model_id' => $conference->id,
            'description' => "ایجاد همایش: {$conference->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('conferences.index')
            ->with('success', 'همایش با موفقیت ایجاد شد.');
    }

    public function show(Conference $conference)
    {
        $conference->load(['creator', 'reservations.personnel', 'reservations.guest']);
        return view('conferences.show', compact('conference'));
    }

    public function edit(Conference $conference)
    {
        return view('conferences.edit', compact('conference'));
    }

    public function update(Request $request, Conference $conference)
    {
        // تبدیل تاریخ شمسی به میلادی
        if ($request->start_date) {
            $startDate = $this->convertPersianToEnglish($request->start_date);
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $startDate);
            $request->merge(['start_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }
        if ($request->end_date) {
            $endDate = $this->convertPersianToEnglish($request->end_date);
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $endDate);
            $request->merge(['end_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:conferences,code,' . $conference->id,
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'organizer' => 'nullable|string|max:200',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $conference->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Conference',
            'model_id' => $conference->id,
            'description' => "ویرایش همایش: {$conference->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('conferences.index')
            ->with('success', 'همایش با موفقیت ویرایش شد.');
    }

    public function destroy(Conference $conference)
    {
        $name = $conference->name;
        $conference->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Conference',
            'model_id' => $conference->id,
            'description' => "حذف همایش: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('conferences.index')
            ->with('success', 'همایش با موفقیت حذف شد.');
    }

    /**
     * همایش‌های قابل انتخاب برای پذیرش (45 روز آینده)
     */
    public function upcoming()
    {
        $conferences = Conference::active()
            ->upcoming()
            ->orderBy('start_date')
            ->get();

        return response()->json($conferences);
    }

    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    private function convertPersianToEnglish($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($persian, $english, $string);
    }
}
