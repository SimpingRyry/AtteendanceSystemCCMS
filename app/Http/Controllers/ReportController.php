<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Student;
use App\Models\FinanceData;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\OfficerRole;
use App\Models\Logs;


class ReportController extends Controller
{
public function generateFinancialReport(Request $request)
{
    $month = $request->month;
    $year = $request->year;
    $user = auth()->user();

    // Determine the organization based on role
    $org = $user->role === 'Super Admin' ? $request->organization : $user->org;

   $query = DB::table('transaction')
    ->select('event', 'transaction_type', DB::raw('SUM(fine_amount) as total_amount'))
    ->when($month !== 'All', fn($q) => $q->whereMonth('date', date('m', strtotime($month))))
    ->when($year, fn($q) => $q->whereYear('date', $year))
    ->when($org, fn($q) => $q->where('org', $org))
    ->groupBy('event', 'transaction_type');

    $rawData = $query->get();

    $grouped = collect($rawData)->groupBy('event')->map(function ($rows) {
        $fines = $rows->firstWhere('transaction_type', 'FINE')->total_amount ?? 0;
        $payments = $rows->firstWhere('transaction_type', 'PAYMENT')->total_amount ?? 0;
        return [
            'fines' => $fines,
            'payments' => $payments,
            'balance' => $fines - $payments
        ];
    });

    $summary = [
        'total_fines' => $grouped->sum('fines'),
        'total_payments' => $grouped->sum('payments'),
        'balance' => $grouped->sum('balance')
    ];

    // Set logo based on organization
    $logo = public_path('images/org_ccms_logo.png');
    if ($org === 'Information Technology Society') {
        $logo = public_path('images/ITS_LOGO.png');
    } elseif ($org === 'PRAXIS') {
        $logo = public_path('images/praxis_logo.png');
    }

    PDF::setOptions(['defaultFont' => 'DejaVu Sans']);
    $pdf = Pdf::loadView('report.financial_pdf', compact('grouped', 'summary', 'month', 'year', 'org', 'logo'));

    return $pdf->stream('financial-report.pdf');
}



public function exportFinancialReport(Request $request)
{
    // Extract data from request
    $tableData = json_decode($request->input('table_data'), true);
    $event = $request->input('event');
    $cashOnHand = floatval($request->input('cash_on_hand'));
    
    $org = auth()->user()->org ?? 'default'; // fallback to 'default' if org is null
    $logo = public_path("images/org_list/{$org}_logo.png");

    // ✅ Log the raw table data
    Log::debug('Export Financial Report - Table Data:', $tableData);

    // Compute expenses
    $expenses = collect($tableData)->sum('total');
    $balance = $cashOnHand - $expenses;

    // Get academic term and extract year range (e.g., "2025-2026")
    $fullTerm = Setting::where('key', 'academic_term')->first()->value ?? 'Unknown Term';
    preg_match('/\d{4}-\d{4}/', $fullTerm, $matches);
    $termYear = $matches[0] ?? 'Unknown Year';

    // Officers using extracted year from term
    $presidentUser = User::where('role', 'LIKE', '%President%')
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    $financialUser = User::where(function($q) {
            $q->where('role', 'LIKE', '%Financial Affairs%')
              ->orWhere('role', 'LIKE', '%Treasurer%');
        })
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    $auditorUser = User::where(function($q) {
            $q->where('role', 'LIKE', '%Auditor%')
              ->orWhere('role', 'LIKE', '%Auditing Officer%');
        })
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    $adviserUser = User::where('role', 'LIKE', '%Adviser%')
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    // Generate PDF
    $pdf = Pdf::loadView('report.financial_pdf2', [
        'tableData' => $tableData,
        'event' => $event,
        'cashOnHand' => number_format($cashOnHand, 2),
        'expenses' => number_format($expenses, 2),
        'balance' => number_format($balance, 2),
        'logo' => $logo,
        'org' => $org,
        'financialUser' => $financialUser,
        'presidentUser' => $presidentUser,
        'auditorUser' => $auditorUser,
        'adviserUser' => $adviserUser,
    ]);
    Logs::create([
    'action' => 'Export',
    'description' => 'Exported financial report for event "' . $event . '" (Cash on Hand: ₱' . number_format($cashOnHand, 2) . ')',
    'user' => auth()->user()->name ?? 'System',
    'date_time' => now('Asia/Manila'),
    'type' => 'Others',
]);

    return $pdf->stream('financial_report.pdf');
}








    public function generateStudentRoster(Request $request)
{
    $query = Student::query();

    // Filter if Roster Type is filtered
    if ($request->roster_type === 'filtered') {
        if ($request->filled('organization')) {
            $query->where('f_id', $request->organization); // assuming `f_id` maps to organization
        }
        if ($request->filled('program')) {
            $query->where('course', $request->program); // 'course' is your program column
        }
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }
    }

    $students = $query->orderBy('name')->get();

    // Generate PDF
    $pdf = PDF::loadView('report.student_roster_pdf', compact('students'));

    return $pdf->download('student_roster.pdf');
}

public function generatePdf() 
{
    $org = Auth::user()->org;
    
    // Get academic term and extract year range (e.g., "2025-2026")
    $fullTerm = Setting::where('key', 'academic_term')->first()->value ?? 'Unknown Term';
    preg_match('/\d{4}-\d{4}/', $fullTerm, $matches);
    $termYear = $matches[0] ?? 'Unknown Year';

    // Fetch officer roles dynamically from the DB
    $baseRoles = OfficerRole::where('org', $org)
        ->orderBy('id')
        ->pluck('title')
        ->toArray();

    $officers = [];

    foreach ($baseRoles as $baseRole) {
        $fullRole = $baseRole . ' - Officer';

        $user = User::whereRaw("TRIM(role) = ?", [$fullRole])
            ->where('org', $org)
            ->where('term', 'LIKE', "%$termYear%")
            ->first();

        if ($user) {
            $student = Student::where('id_number', $user->student_id)->first();
            $photoPath = public_path("uploads/{$user->picture}");
            $photo = file_exists($photoPath) ? $photoPath : public_path("images/default.png");

            $officers[] = [
                'position' => $baseRole,
                'name' => $user->name,
                'photo' => $photo,
                'birth_date' => $student->birth_date ?? 'Not Set',
                'address' => $student->address ?? 'Not Set',
                'course' => $student->course ?? 'N/A',
                'year' => $student->year ?? null,
            ];
        } else {
            $officers[] = [
                'position' => $baseRole,
                'name' => 'Not Appointed',
                'photo' => public_path("images/default.png"),
                'birth_date' => '-',
                'address' => '-',
                'course' => '-',
                'year' => '-',
            ];
        }
    }

    // President
    $presidentUser = User::whereRaw("TRIM(role) = ?", ['President - Officer'])
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    $presidentName = $presidentUser ? $presidentUser->name : 'Not Appointed';

    // Adviser
    $adviserUser = User::where('role', 'Adviser')
        ->where('org', $org)
        ->where('term', 'LIKE', "%$termYear%")
        ->first();

    $adviserName = $adviserUser ? $adviserUser->name : 'Not Set';

    // Generate PDF
    $pdf = Pdf::loadView('report.roster', compact('officers', 'org', 'presidentName', 'adviserName'))
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isPhpEnabled', true);

        Logs::create([
    'action' => 'Export',
    'description' => 'Exported officer roster PDF for org "' . $org . '" (' . $termYear . ')',
    'user' => auth()->user()->name ?? 'System',
    'date_time' => now('Asia/Manila'),
    'type' => 'Others',
]);

    return $pdf->stream('officer_roster.pdf');
}




}
