<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    // 1. Total Students
    $totalStudents = DB::table('student_list')->count();

    // 2. Registered Students
    $registeredCount = DB::table('student_list')->where('status', 'Registered')->count();

    // 3. Unregistered Students
    $unregisteredCount = DB::table('student_list')->where('status', 'Unregistered')->count();

    // 4. Total Fines
    $totalFines = DB::table('finance_data')->sum('amount');

    // 5. Fines by Month (last 6 months)
    $finesByMonth = DB::table('finance_data')
        ->select(
            DB::raw("DATE_FORMAT(date_issued, '%Y-%m') as month"),
            DB::raw("SUM(amount) as total")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(6)
        ->get();

    // 6. Upcoming Events
    $upcomingEvents = DB::table('events')
        ->select('name', 'event_date')
        ->whereDate('event_date', '>=', Carbon::now())
        ->orderBy('event_date')
        ->limit(5)
        ->get();

    return view('dashboard_page', compact(
        'totalStudents',
        'registeredCount',
        'unregisteredCount',
        'totalFines',
        'finesByMonth',
        'upcomingEvents'
    ));
}
}
