<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\FineHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    // Display the settings page with current values
public function index()
{
    $authOrg = Auth::user()->org;

    // Get fine settings for the authenticated user's org
    $fines = DB::table('fine_settings')
        ->where('org', $authOrg)
        ->first();

    // Get fine history for the authenticated user's org
    $history = FineHistory::where('org', $authOrg)
        ->orderBy('changed_at', 'desc')
        ->get();

    $academicTerm = Setting::where('key', 'academic_term')->value('value');

    return view('config', compact('fines', 'history', 'academicTerm'));
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

    $orgName = auth()->user()->org; // Get the org name from the authenticated user

    $data = [
        'absent_member' => $request->absent_member,
        'absent_officer' => $request->absent_officer,
        'late_member' => $request->late_member,
        'late_officer' => $request->late_officer,
        'grace_period_minutes' => $request->grace_period_minutes,
        'updated_at' => now(),
    ];

    $existing = DB::table('fine_settings')->where('org', $orgName)->first();

    if ($existing) {
        DB::table('fine_settings')->where('org', $orgName)->update($data);
    } else {
        $data['org'] = $orgName;
        $data['created_at'] = now();
        DB::table('fine_settings')->insert($data);
    }

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
            'updated_by' => auth()->id(),
            'org' => $orgName,
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

    $academicTerm = $request->academic_term;

    // Get code part before any space or extra characters (e.g., "24-1" from "24-1 First Semester")
    $acadCode = explode(' ', $academicTerm)[0];

    Setting::updateOrCreate(
        ['id' => 1], // or any unique identifier
        [
            'academic_term' => $academicTerm,
            'acad_code' => $acadCode,
        ]
    );

    return redirect()->back()->with('success', 'Academic term updated successfully!');
}
}
