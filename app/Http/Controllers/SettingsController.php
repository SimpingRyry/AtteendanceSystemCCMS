<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // Display the settings page with current values
    public function index()
    {
        $fineSettings = DB::table('fine_settings')->first();
        $academicSettings = DB::table('academic_settings')->first();

        return view('config', compact('fineSettings', 'academicSettings'));
    }

    // Update fine settings
    public function updateFines(Request $request)
    {
        $request->validate([
            'absent_member' => 'required|numeric|min:0',
            'absent_officer' => 'required|numeric|min:0',
            'late_member' => 'required|numeric|min:0',
            'late_officer' => 'required|numeric|min:0',
        ]);

        DB::table('fine_settings')->update([
            'absent_member' => $request->absent_member,
            'absent_officer' => $request->absent_officer,
            'late_member' => $request->late_member,
            'late_officer' => $request->late_officer,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Fine settings updated successfully.');
    }

    // Update academic year and term
    public function updateAcademic(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:20',
            'term' => 'required|string|max:20',
        ]);

        DB::table('academic_settings')->update([
            'academic_year' => $request->academic_year,
            'term' => $request->term,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Academic settings updated successfully.');
    }
}
