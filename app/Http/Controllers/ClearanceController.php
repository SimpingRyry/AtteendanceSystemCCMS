<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClearanceController extends Controller
{
    public function showFinanceClearance(Request $request)
{
    // Get filter values from request
    $selectedOrg = $request->input('org');
    $selectedProgram = $request->input('program');

    // Fetch unique filter options
    $orgs = DB::table('finance_data')->select('org')->distinct()->pluck('org');
    $programs = DB::table('finance_data')->select('program')->distinct()->pluck('program');

    // Base query
    $query = DB::table('finance_data')
        ->select('student_id', 'org', 'program', DB::raw('SUM(amount) as total_fines'))
        ->groupBy('student_id', 'org', 'program');

    // Apply filters if selected
    if ($selectedOrg) {
        $query->where('org', $selectedOrg);
    }

    if ($selectedProgram) {
        $query->where('program', $selectedProgram);
    }

    $financeSummary = $query->get();

    return view('clearance', compact('financeSummary', 'orgs', 'programs', 'selectedOrg', 'selectedProgram'));
}

public function downloadPDF(Request $request)
{
    $query = DB::table('finance_data')
        ->select('student_id', 'org', 'program', DB::raw('SUM(amount) as total_fines'))
        ->groupBy('student_id', 'org', 'program');

    if ($request->filled('org')) {
        $query->where('org', $request->org);
    }

    if ($request->filled('program')) {
        $query->where('program', $request->program);
    }

    $financeSummary = $query->get();

    $date = now()->format('F j, Y');

    $pdf = Pdf::loadView('report.clearance_report', [
        'financeSummary' => $financeSummary,
        'date' => $date
    ]);

    return $pdf->download('Finance_Clearance_Report_' . now()->format('Ymd_His') . '.pdf');
}
}
