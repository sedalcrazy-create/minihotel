<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Bed;
use App\Models\Meal;
use App\Models\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function reservations(Request $request)
    {
        $query = Reservation::with(['personnel', 'guest', 'admissionType', 'room.unit', 'beds']);

        if ($request->filled('from_date')) {
            $query->where('check_in_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('check_out_date', '<=', $request->to_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('admission_type_id')) {
            $query->where('admission_type_id', $request->admission_type_id);
        }

        $reservations = $query->orderBy('check_in_date', 'desc')->get();

        if ($request->filled('export') && $request->export === 'excel') {
            return $this->exportReservationsExcel($reservations);
        }

        $admissionTypes = \App\Models\AdmissionType::all();

        return view('reports.reservations', compact('reservations', 'admissionTypes'));
    }

    public function occupancy(Request $request)
    {
        $units = Unit::with(['rooms.beds'])->get();

        $stats = [
            'total_beds' => Bed::count(),
            'available' => Bed::where('status', 'available')->count(),
            'occupied' => Bed::where('status', 'occupied')->count(),
            'needs_cleaning' => Bed::where('status', 'needs_cleaning')->count(),
            'under_maintenance' => Bed::where('status', 'under_maintenance')->count(),
        ];

        return view('reports.occupancy', compact('units', 'stats'));
    }

    public function meals(Request $request)
    {
        $query = Meal::with(['reservation.personnel', 'reservation.guest']);

        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $meals = $query->orderBy('date', 'desc')->get();

        $stats = [
            'total_breakfast' => $meals->where('breakfast', true)->count(),
            'total_lunch' => $meals->where('lunch', true)->count(),
            'total_dinner' => $meals->where('dinner', true)->count(),
        ];

        return view('reports.meals', compact('meals', 'stats'));
    }

    private function exportReservationsExcel($reservations)
    {
        $data = [];
        $data[] = ['شماره', 'مهمان', 'نوع پذیرش', 'واحد', 'اتاق', 'تعداد تخت', 'تاریخ ورود', 'تاریخ خروج', 'وضعیت'];

        foreach ($reservations as $r) {
            $guestName = $r->personnel ? $r->personnel->first_name . ' ' . $r->personnel->last_name : ($r->guest ? $r->guest->full_name : '-');
            $data[] = [
                $r->id,
                $guestName,
                $r->admissionType->name ?? '-',
                $r->room->unit->number ?? '-',
                $r->room->number ?? '-',
                $r->beds->count(),
                jdate($r->check_in_date)->format('Y/m/d'),
                jdate($r->check_out_date)->format('Y/m/d'),
                $this->getStatusLabel($r->status),
            ];
        }

        $filename = 'reservations_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'reserved' => 'رزرو شده',
            'checked_in' => 'ورود',
            'checked_out' => 'خروج',
            'cancelled' => 'لغو شده',
        ];
        return $labels[$status] ?? $status;
    }
}
