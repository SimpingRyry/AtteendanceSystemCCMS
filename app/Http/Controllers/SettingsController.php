<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FineHistory;
use App\Models\Setting;
class SettingsController extends Controller
{
    // Display the settings page with current values
public function index()
{
    $fines = DB::table('fine_settings')->first();
    $history = FineHistory::orderBy('changed_at', 'desc')->get();
    $academicTerm = Setting::where('key', 'academic_term')->value('value');

    return view('config', compact('fines', 'history', 'academicTerm'));

    $settings = new \stdClass();
    $settings->academic_term = $academicTerm;

    return view('settings.index', ['academic_term' => $academicTerm]);
}

    

public function updateFines(Request $request)
{
    $request->validate([
    'absent_member' => 'required|numeric|min:0',
    'absent_officer' => 'required|numeric|min:0',
    'late_member' => 'required|numeric|min:0',
    'late_officer' => 'required|numeric|min:0',
    'grace_period_minutes' => 'required|integer|min:0',
]);

DB::table('fine_settings')->update([
    'absent_member' => $request->absent_member,
    'absent_officer' => $request->absent_officer,
    'late_member' => $request->late_member,
    'late_officer' => $request->late_officer,
    'grace_period_minutes' => $request->grace_period_minutes,
    'updated_at' => now(),
]);

    // Store each change in the fine history table
    $fineTypes = [
        'Absent Fine (Member)' => $request->absent_member,
        'Absent Fine (Officer)' => $request->absent_officer,
        'Late Fine (Member)' => $request->late_member,
        'Late Fine (Officer)' => $request->late_officer,
    ];

    foreach ($fineTypes as $type => $amount) {
        FineHistory::create([
            'type' => $type,
            'amount' => $amount,
            'updated_by' => auth()->id(),  // Make sure user is authenticated
            'changed_at' => now(),
        ]);
    }

    return redirect()->back()->with('success', 'Fine settings updated successfully.');
}

public function updateAcademic(Request $request)
{
    $request->validate([
        'academic_term' => 'required|string|max:255',
    ]);

    Setting::updateOrCreate(
        ['key' => 'academic_term'],
        ['value' => $request->academic_term]
    );

    return redirect()->back()->with('success', 'Academic term updated successfully!');
}
}
