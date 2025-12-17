<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Reservation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index(Request $request)
    {
        $query = Meal::with(['reservation.personnel', 'reservation.guest']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', now()->toDateString());
        }

        $meals = $query->orderBy('date', 'desc')->paginate(20);

        $activeReservations = Reservation::where('status', 'checked_in')
            ->with(['personnel', 'guest'])
            ->get();

        return view('meals.index', compact('meals', 'activeReservations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'date' => 'required|date',
            'breakfast' => 'boolean',
            'lunch' => 'boolean',
            'dinner' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['breakfast'] = $request->has('breakfast');
        $validated['lunch'] = $request->has('lunch');
        $validated['dinner'] = $request->has('dinner');

        $meal = Meal::updateOrCreate(
            ['reservation_id' => $validated['reservation_id'], 'date' => $validated['date']],
            $validated
        );

        ActivityLog::log('create', 'Meal', $meal->id, 'وعده غذایی ثبت شد');

        return redirect()->route('meals.index')
            ->with('success', 'وعده غذایی با موفقیت ثبت شد.');
    }

    public function update(Request $request, Meal $meal)
    {
        $validated = $request->validate([
            'breakfast' => 'boolean',
            'lunch' => 'boolean',
            'dinner' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['breakfast'] = $request->has('breakfast');
        $validated['lunch'] = $request->has('lunch');
        $validated['dinner'] = $request->has('dinner');

        $meal->update($validated);

        ActivityLog::log('update', 'Meal', $meal->id, 'وعده غذایی ویرایش شد');

        return redirect()->route('meals.index')
            ->with('success', 'وعده غذایی با موفقیت ویرایش شد.');
    }

    public function destroy(Meal $meal)
    {
        $meal->delete();

        ActivityLog::log('delete', 'Meal', null, 'وعده غذایی حذف شد');

        return redirect()->route('meals.index')
            ->with('success', 'وعده غذایی با موفقیت حذف شد.');
    }
}
