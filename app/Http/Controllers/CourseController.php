<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('creator')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        // تبدیل تاریخ شمسی به میلادی
        if ($request->start_date) {
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $request->start_date);
            $request->merge(['start_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }
        if ($request->end_date) {
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $request->end_date);
            $request->merge(['end_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:200',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;

        $course = Course::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Course',
            'model_id' => $course->id,
            'description' => "ایجاد دوره: {$course->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'دوره با موفقیت ایجاد شد.');
    }

    public function show(Course $course)
    {
        $course->load(['creator', 'reservations.personnel', 'reservations.guest']);
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        // تبدیل تاریخ شمسی به میلادی
        if ($request->start_date) {
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $request->start_date);
            $request->merge(['start_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }
        if ($request->end_date) {
            $jalali = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $request->end_date);
            $request->merge(['end_date' => $jalali->toCarbon()->format('Y-m-d')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:200',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Course',
            'model_id' => $course->id,
            'description' => "ویرایش دوره: {$course->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'دوره با موفقیت ویرایش شد.');
    }

    public function destroy(Course $course)
    {
        $name = $course->name;
        $course->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Course',
            'model_id' => $course->id,
            'description' => "حذف دوره: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'دوره با موفقیت حذف شد.');
    }

    /**
     * دوره‌های قابل انتخاب برای پذیرش (45 روز آینده)
     */
    public function upcoming()
    {
        $courses = Course::active()
            ->upcoming()
            ->orderBy('start_date')
            ->get();

        return response()->json($courses);
    }
}
