<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Unit;
use App\Models\Reservation;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // احصائیات کلی
        $totalBeds = Bed::count();
        $availableBeds = Bed::where('status', 'available')->count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $cleaningBeds = Bed::where('status', 'needs_cleaning')->count();
        $maintenanceBeds = Bed::where('status', 'under_maintenance')->count();

        // واحدها با اتاق‌ها و تخت‌ها
        $units = Unit::with(['rooms.beds'])
            ->where('is_active', true)
            ->orderBy('number')
            ->get();

        // رزروهای فعال
        $activeReservations = Reservation::with(['personnel', 'guest', 'room.unit'])
            ->where('status', 'checked_in')
            ->latest()
            ->take(10)
            ->get();

        // تعمیرات در انتظار
        $pendingMaintenance = MaintenanceRequest::with(['bed.room.unit', 'reporter'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBeds',
            'availableBeds',
            'occupiedBeds',
            'cleaningBeds',
            'maintenanceBeds',
            'units',
            'activeReservations',
            'pendingMaintenance'
        ));
    }
}
