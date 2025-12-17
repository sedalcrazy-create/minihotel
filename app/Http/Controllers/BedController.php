<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function updateStatus(Request $request, Bed $bed)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,needs_cleaning,under_maintenance'
        ]);

        $bed->update([
            'status' => $request->status
        ]);

        $statusLabels = [
            'available' => 'آزاد',
            'occupied' => 'اشغال',
            'needs_cleaning' => 'نیاز به نظافت',
            'under_maintenance' => 'در تعمیر'
        ];

        return redirect()->route('dashboard')->with('success', "وضعیت تخت {$bed->identifier} به \"{$statusLabels[$request->status]}\" تغییر کرد.");
    }
}
