<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\AdmissionType;
use App\Models\Personnel;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Course;
use App\Models\Conference;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['admissionType', 'personnel', 'guest', 'room.unit', 'creator'])
            ->latest()
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $admissionTypes = AdmissionType::where('is_active', true)->get();
        $personnel = Personnel::where('is_active', true)->orderBy('first_name')->get();
        $rooms = Room::with(['unit', 'beds'])->where('is_active', true)->get();

        // دوره‌ها و همایش‌های 45 روز آینده
        $today = Carbon::today();
        $futureLimit = Carbon::today()->addDays(45);

        $courses = Course::where('is_active', true)
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', $futureLimit)
            ->orderBy('start_date')
            ->get();

        $conferences = Conference::where('is_active', true)
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', $futureLimit)
            ->orderBy('start_date')
            ->get();

        // Pre-selected bed and room from dashboard
        $selectedBedId = $request->get('bed_id');
        $selectedRoomId = $request->get('room_id');

        // If bed_id is provided but room_id is not, find the room
        if ($selectedBedId && !$selectedRoomId) {
            $bed = Bed::find($selectedBedId);
            $selectedRoomId = $bed ? $bed->room_id : null;
        }

        return view('reservations.create', compact('admissionTypes', 'personnel', 'rooms', 'courses', 'conferences', 'selectedBedId', 'selectedRoomId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_type_id' => 'required|exists:admission_types,id',
            'course_id' => 'nullable|exists:courses,id',
            'conference_id' => 'nullable|exists:conferences,id',
            'personnel_id' => 'nullable|exists:personnel,id',
            'guest_name' => 'nullable|required_without:personnel_id|string',
            'guest_phone' => 'nullable|string',
            'guest_gender' => 'nullable|required_without:personnel_id|in:male,female',
            'room_id' => 'required|exists:rooms,id',
            'bed_ids' => 'required|array|min:1',
            'bed_ids.*' => 'exists:beds,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
        ]);

        // بررسی حداقل یکی از پرسنل یا مهمان انتخاب شده باشد
        if (empty($request->personnel_id) && empty($request->guest_name)) {
            return back()->withErrors([
                'personnel_id' => 'باید پرسنل یا مهمان خارجی انتخاب شود.'
            ])->withInput();
        }

        // Validation: بررسی ارتباط نوع پذیرش با دوره/همایش
        $admissionType = AdmissionType::find($validated['admission_type_id']);

        if ($admissionType && str_contains($admissionType->name, 'دوره')) {
            if (empty($request->course_id)) {
                return back()->withErrors(['course_id' => 'انتخاب دوره برای این نوع پذیرش الزامی است.'])->withInput();
            }

            // بررسی تاریخ رزرو با تاریخ دوره
            $course = Course::find($request->course_id);
            if ($course) {
                $checkIn = Carbon::parse($request->check_in_date);
                $checkOut = Carbon::parse($request->check_out_date);
                $courseStart = Carbon::parse($course->start_date);
                $courseEnd = Carbon::parse($course->end_date);

                if ($checkIn->lt($courseStart) || $checkOut->gt($courseEnd)) {
                    return back()->withErrors([
                        'check_in_date' => 'تاریخ رزرو باید در بازه تاریخ دوره (' . $course->start_date . ' تا ' . $course->end_date . ') باشد.'
                    ])->withInput();
                }
            }
        }

        if ($admissionType && str_contains($admissionType->name, 'همایش')) {
            if (empty($request->conference_id)) {
                return back()->withErrors(['conference_id' => 'انتخاب همایش برای این نوع پذیرش الزامی است.'])->withInput();
            }

            // بررسی تاریخ رزرو با تاریخ همایش
            $conference = Conference::find($request->conference_id);
            if ($conference) {
                $checkIn = Carbon::parse($request->check_in_date);
                $checkOut = Carbon::parse($request->check_out_date);
                $confStart = Carbon::parse($conference->start_date);
                $confEnd = Carbon::parse($conference->end_date);

                if ($checkIn->lt($confStart) || $checkOut->gt($confEnd)) {
                    return back()->withErrors([
                        'check_in_date' => 'تاریخ رزرو باید در بازه تاریخ همایش (' . $conference->start_date . ' تا ' . $conference->end_date . ') باشد.'
                    ])->withInput();
                }
            }
        }

        // Validation: جلوگیری از اختلاط جنسیتی در یک اتاق
        $newGuestGender = null;
        if ($request->personnel_id) {
            $personnel = Personnel::find($request->personnel_id);
            $newGuestGender = $personnel ? $personnel->gender : null;
        } else {
            $newGuestGender = $request->guest_gender;
        }

        // بررسی رزروهای فعال در همین اتاق
        $existingReservations = Reservation::where('room_id', $validated['room_id'])
            ->whereIn('status', ['pending', 'confirmed', 'reserved', 'checked_in'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('check_in_date', [$validated['check_in_date'], $validated['check_out_date']])
                      ->orWhereBetween('check_out_date', [$validated['check_in_date'], $validated['check_out_date']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('check_in_date', '<=', $validated['check_in_date'])
                            ->where('check_out_date', '>=', $validated['check_out_date']);
                      });
            })
            ->with(['personnel', 'guest'])
            ->get();

        foreach ($existingReservations as $existing) {
            $existingGender = null;
            if ($existing->personnel) {
                $existingGender = $existing->personnel->gender;
            } elseif ($existing->guest) {
                $existingGender = $existing->guest->gender;
            }

            if ($existingGender && $newGuestGender && $existingGender !== $newGuestGender) {
                return back()->withErrors([
                    'room_id' => 'خطا: در این اتاق مهمان ' . ($existingGender === 'female' ? 'خانم' : 'آقا') . ' وجود دارد. اختلاط جنسیتی مجاز نیست.'
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Create guest if needed
            if (!$request->personnel_id && $request->guest_name) {
                $guest = Guest::create([
                    'full_name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                    'gender' => $request->guest_gender,
                ]);
                $validated['guest_id'] = $guest->id;
            }

            // Create reservation
            $reservation = Reservation::create([
                'admission_type_id' => $validated['admission_type_id'],
                'course_id' => $validated['course_id'] ?? null,
                'conference_id' => $validated['conference_id'] ?? null,
                'personnel_id' => $validated['personnel_id'] ?? null,
                'guest_id' => $validated['guest_id'] ?? null,
                'room_id' => $validated['room_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Attach beds
            $reservation->beds()->attach($validated['bed_ids']);

            ActivityLog::log('create', 'Reservation', $reservation->id, 'رزرو جدید ایجاد شد');

            DB::commit();

            return redirect()->route('reservations.show', $reservation)
                ->with('success', 'رزرو با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در ثبت رزرو: ' . $e->getMessage());
        }
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['admissionType', 'personnel', 'guest', 'room.unit', 'beds', 'meals', 'creator']);

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $admissionTypes = AdmissionType::where('is_active', true)->get();
        $personnel = Personnel::where('is_active', true)->orderBy('first_name')->get();
        $rooms = Room::with('unit')->where('is_active', true)->get();
        $reservation->load(['beds', 'room.beds']);

        return view('reservations.edit', compact('reservation', 'admissionTypes', 'personnel', 'rooms'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'admission_type_id' => 'required|exists:admission_types,id',
            'personnel_id' => 'nullable|exists:personnel,id',
            'guest_name' => 'required_without:personnel_id|string',
            'guest_phone' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'bed_ids' => 'required|array|min:1',
            'bed_ids.*' => 'exists:beds,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update or create guest if needed
            if (!$request->personnel_id && $request->guest_name) {
                if ($reservation->guest_id) {
                    $reservation->guest->update([
                        'full_name' => $request->guest_name,
                        'phone' => $request->guest_phone,
                    ]);
                } else {
                    $guest = Guest::create([
                        'full_name' => $request->guest_name,
                        'phone' => $request->guest_phone,
                    ]);
                    $validated['guest_id'] = $guest->id;
                }
            }

            // Update reservation
            $reservation->update([
                'admission_type_id' => $validated['admission_type_id'],
                'personnel_id' => $validated['personnel_id'] ?? null,
                'guest_id' => $validated['guest_id'] ?? $reservation->guest_id,
                'room_id' => $validated['room_id'],
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Sync beds
            $reservation->beds()->sync($validated['bed_ids']);

            ActivityLog::log('update', 'Reservation', $reservation->id, 'رزرو ویرایش شد');

            DB::commit();

            return redirect()->route('reservations.show', $reservation)
                ->with('success', 'رزرو با موفقیت ویرایش شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در ویرایش رزرو: ' . $e->getMessage());
        }
    }

    public function checkIn(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed', 'reserved'])) {
            return back()->with('error', 'این رزرو قابل چک‌این نیست.');
        }

        // Validation: جلوگیری از اختلاط جنسیتی در یک اتاق
        $newGuestGender = null;
        if ($reservation->personnel) {
            $newGuestGender = $reservation->personnel->gender;
        } elseif ($reservation->guest) {
            $newGuestGender = $reservation->guest->gender;
        }

        // بررسی رزروهای فعال در همین اتاق (به جز این رزرو)
        $existingReservations = Reservation::where('room_id', $reservation->room_id)
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['checked_in'])
            ->with(['personnel', 'guest'])
            ->get();

        foreach ($existingReservations as $existing) {
            $existingGender = null;
            if ($existing->personnel) {
                $existingGender = $existing->personnel->gender;
            } elseif ($existing->guest) {
                $existingGender = $existing->guest->gender;
            }

            if ($existingGender && $newGuestGender && $existingGender !== $newGuestGender) {
                return back()->with('error', 'خطا: در این اتاق مهمان ' . ($existingGender === 'female' ? 'خانم' : 'آقا') . ' چک‌این شده است. اختلاط جنسیتی مجاز نیست.');
            }
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'checked_in',
                'actual_check_in' => now(),
            ]);

            // Update bed statuses to occupied
            foreach ($reservation->beds as $bed) {
                $bed->update(['status' => 'occupied']);
            }

            ActivityLog::log('check_in', 'Reservation', $reservation->id, 'چک‌این انجام شد');

            DB::commit();

            return back()->with('success', 'چک‌این با موفقیت انجام شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در چک‌این: ' . $e->getMessage());
        }
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'این رزرو قابل چک‌اوت نیست.');
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'checked_out',
                'actual_check_out' => now(),
            ]);

            // Update bed statuses to available (no cleaning needed)
            foreach ($reservation->beds as $bed) {
                $bed->update(['status' => 'available']);
            }

            ActivityLog::log('check_out', 'Reservation', $reservation->id, 'چک‌اوت انجام شد');

            DB::commit();

            return back()->with('success', 'چک‌اوت با موفقیت انجام شد.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطا در چک‌اوت: ' . $e->getMessage());
        }
    }
}
