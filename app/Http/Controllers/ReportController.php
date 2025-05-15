<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceData;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    public function generateFinancialReport(Request $request)
    {
        // Start query
        $query = FinanceData::query();

        // Apply filters
        if ($request->filled('organization')) {
            $query->where('org', $request->organization);
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        $financeReports = $query->get();

        // If user wants to export as PDF
        if ($request->export === 'pdf') {
            $pdf = PDF::loadView('report.financial_pdf', compact('financeReports'));
            return $pdf->download('financial_report.pdf');
        }

        // Otherwise, show normal view
        return view('report.financial_results', compact('financeReports'));
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
