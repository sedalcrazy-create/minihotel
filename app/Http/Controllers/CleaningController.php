<?php

namespace App\Http\Controllers;

use App\Models\CleaningLog;
use App\Models\Bed;
use App\Models\Room;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CleaningController extends Controller
{
    public function index(Request $request)
    {
        $query = CleaningLog::with(['room.unit', 'bed.room.unit', 'cleanedBy']);

        if ($request->filled('date')) {
            $query->whereDate('cleaned_at', $request->date);
        }

        $logs = $query->orderBy('cleaned_at', 'desc')->paginate(20);

        // Pre-selected bed from dashboard
        $selectedBedId = $request->get('bed_id');
        $selectedBed = null;
        if ($selectedBedId) {
            $selectedBed = Bed::with('room.unit')->find($selectedBedId);
        }

        return view('cleaning.index', compact('logs', 'selectedBed', 'selectedBedId'));
    }

    public function pending()
    {
        $beds = Bed::where('status', 'needs_cleaning')
            ->with(['room.unit'])
            ->get();

        return view('cleaning.pending', compact('beds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bed_id' => 'nullable|exists:beds,id',
            'room_id' => 'nullable|exists:rooms,id',
            'type' => 'required|in:daily,weekly,deep',
            'notes' => 'nullable|string',
        ]);

        if (!$validated['bed_id'] && !$validated['room_id']) {
            return back()->withErrors(['error' => 'باید تخت یا اتاق انتخاب شود.']);
        }

        $log = CleaningLog::create([
            'bed_id' => $validated['bed_id'],
            'room_id' => $validated['room_id'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
            'cleaned_by' => auth()->id(),
            'cleaned_at' => now(),
        ]);

        // Update bed status
        if ($validated['bed_id']) {
            Bed::where('id', $validated['bed_id'])->update(['status' => 'available']);
        }

        // If room cleaning, update all beds
        if ($validated['room_id']) {
            Bed::where('room_id', $validated['room_id'])
                ->where('status', 'needs_cleaning')
                ->update(['status' => 'available']);
        }

        try {
            ActivityLog::log('create', 'CleaningLog', $log->id, 'نظافت ثبت شد');
        } catch (\Exception $e) {}

        return redirect()->route('cleaning.index')
            ->with('success', 'نظافت با موفقیت ثبت شد.');
    }
}
