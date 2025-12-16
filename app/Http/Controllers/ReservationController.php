<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\AdmissionType;
use App\Models\Personnel;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Bed;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['admissionType', 'personnel', 'guest', 'room.unit', 'creator'])
            ->latest()
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $admissionTypes = AdmissionType::where('is_active', true)->get();
        $personnel = Personnel::where('is_active', true)->orderBy('first_name')->get();
        $rooms = Room::with('unit')->where('is_active', true)->get();

        return view('reservations.create', compact('admissionTypes', 'personnel', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_type_id' => 'required|exists:admission_types,id',
            'personnel_id' => 'required_without:guest_id|exists:personnel,id',
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
            // Create guest if needed
            if (!$request->personnel_id && $request->guest_name) {
                $guest = Guest::create([
                    'full_name' => $request->guest_name,
                    'phone' => $request->guest_phone,
                ]);
                $validated['guest_id'] = $guest->id;
            }

            // Create reservation
            $reservation = Reservation::create([
                'admission_type_id' => $validated['admission_type_id'],
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

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'pending' && $reservation->status !== 'confirmed') {
            return back()->with('error', 'این رزرو قابل چک‌این نیست.');
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

            // Update bed statuses to needs_cleaning
            foreach ($reservation->beds as $bed) {
                $bed->update(['status' => 'needs_cleaning']);
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
