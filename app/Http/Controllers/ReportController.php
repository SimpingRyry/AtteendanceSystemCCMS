<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FinanceData;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


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
}
