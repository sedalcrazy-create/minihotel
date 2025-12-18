<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Bed;
use App\Models\Room;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['room.unit', 'bed.room.unit', 'reportedBy', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('maintenance.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $rooms = Room::with('unit')->get();
        $beds = Bed::with('room.unit')->get();
        $selectedBedId = $request->get('bed_id');

        return view('maintenance.create', compact('rooms', 'beds', 'selectedBedId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bed_id' => 'nullable|exists:beds,id',
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        if (!$validated['bed_id'] && !$validated['room_id']) {
            return back()->withErrors(['error' => 'باید تخت یا اتاق انتخاب شود.']);
        }

        $maintenance = MaintenanceRequest::create([
            'bed_id' => $validated['bed_id'],
            'room_id' => $validated['room_id'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
            'reported_by' => auth()->id(),
        ]);

        // Update bed status
        if ($validated['bed_id']) {
            Bed::where('id', $validated['bed_id'])->update(['status' => 'under_maintenance']);
        }

        try {
            ActivityLog::log('create', 'MaintenanceRequest', $maintenance->id, 'درخواست تعمیر ثبت شد');
        } catch (\Exception $e) {}

        return redirect()->route('maintenance.index')
            ->with('success', 'درخواست تعمیر با موفقیت ثبت شد.');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $maintenance->load(['room.unit', 'bed.room.unit', 'reportedBy', 'assignedTo']);

        return view('maintenance.show', compact('maintenance'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $maintenance->status;
        $maintenance->update($validated);

        // Update timestamps based on status
        if ($validated['status'] === 'in_progress' && $oldStatus !== 'in_progress') {
            $maintenance->update(['started_at' => now()]);
        }

        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $maintenance->update(['completed_at' => now()]);

            // Change bed status to needs_cleaning
            if ($maintenance->bed_id) {
                Bed::where('id', $maintenance->bed_id)->update(['status' => 'needs_cleaning']);
            }
        }

        try {
            ActivityLog::log('update', 'MaintenanceRequest', $maintenance->id, 'وضعیت تعمیر تغییر کرد');
        } catch (\Exception $e) {}

        return redirect()->route('maintenance.index')
            ->with('success', 'وضعیت تعمیر با موفقیت به‌روزرسانی شد.');
    }

    public function assign(Request $request, MaintenanceRequest $maintenance)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $maintenance->update(['assigned_to' => $validated['assigned_to']]);

        try {
            ActivityLog::log('update', 'MaintenanceRequest', $maintenance->id, 'تعمیرکار تخصیص داده شد');
        } catch (\Exception $e) {}

        return redirect()->route('maintenance.index')
            ->with('success', 'تعمیرکار با موفقیت تخصیص داده شد.');
    }
}
