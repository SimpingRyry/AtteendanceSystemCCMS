<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceData;
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
}

