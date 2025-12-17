<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_code' => 'nullable|string|size:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
        ]);

        $guest = Guest::create($validated);

        ActivityLog::log('create', 'Guest', $guest->id, "مهمان جدید ایجاد شد: {$guest->full_name}");

        return redirect()->route('guests.index')
            ->with('success', 'مهمان با موفقیت ایجاد شد.');
    }

    public function show(Guest $guest)
    {
        $guest->load('reservations');
        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_code' => 'nullable|string|size:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'reason' => 'nullable|string',
        ]);

        $guest->update($validated);

        ActivityLog::log('update', 'Guest', $guest->id, "مهمان ویرایش شد: {$guest->full_name}");

        return redirect()->route('guests.index')
            ->with('success', 'مهمان با موفقیت ویرایش شد.');
    }

    public function destroy(Guest $guest)
    {
        $name = $guest->full_name;
        $guest->delete();

        ActivityLog::log('delete', 'Guest', null, "مهمان حذف شد: {$name}");

        return redirect()->route('guests.index')
            ->with('success', 'مهمان با موفقیت حذف شد.');
    }
}
