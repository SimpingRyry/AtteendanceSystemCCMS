<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
public function index()
{
    // Basic Counts
    $totalOrganizations = OrgList::count();
    $totalEvents = Event::count();
    $totalUsers = User::count();
    $totalStudents = Student::count();

    // Events per Organization
    $orgs = OrgList::pluck('org_name'); // get all org names
    $eventsPerOrg = [];

    foreach ($orgs as $orgName) {
        $eventCount = Event::where('org', $orgName)->count();
        $eventsPerOrg[$orgName] = $eventCount;
    }

    // Student distribution by program/course
    $studentDistribution = Student::select('course', DB::raw('count(*) as total'))
        ->groupBy('course')
        ->pluck('total', 'course'); // returns ['BSIT' => 750, 'BSIS' => 484]

        $upcomingEvents = DB::table('events')
        ->select('name', 'event_date')
        ->whereDate('event_date', '>=', Carbon::now())
        ->orderBy('event_date')
        ->limit(3)
        ->get();

    return view('super_dashboard', compact(
        'totalOrganizations',
        'totalEvents',
        'totalUsers',
        'totalStudents',
        'eventsPerOrg',
        'studentDistribution',
        'upcomingEvents'
    ));
}

public function studentindex()
{
    $user = auth()->user();

    // Completed events = all attendance records
    $completedEvents = Attendance::where('student_id', $user->student_id)->count();

    // Upcoming events
    $upcomingEventsCount = Event::where('event_date', '>', now())->count();

    // Attendance Breakdown
    $attended = Attendance::where('student_id', $user->student_id)
        ->where(function ($q) {
            $q->where('status', 'On Time')
              ->orWhere('status_morning', 'On Time')
              ->orWhere('status_afternoon', 'On Time');
        })->count();

    $absent = Attendance::where('student_id', $user->student_id)
        ->where(function ($q) {
            $q->where('status', 'Absent')
              ->orWhere('status_morning', 'Absent')
              ->orWhere('status_afternoon', 'Absent');
        })->count();

    $attendancePercentage = $completedEvents > 0 ? round(($attended / $completedEvents) * 100) : 0;

    // Total fines calculation
    $fines = Transaction::where('student_id', $user->student_id)
        ->where('transaction_type', 'FINE')
        ->sum('fine_amount');

    $payments = Transaction::where('student_id', $user->student_id)
        ->where('transaction_type', 'PAYMENT')
        ->sum('fine_amount');

    $totalFines = $fines - $payments;

    // Recent Payments
    $recentPayments = Transaction::where('student_id', $user->student_id)
        ->where('transaction_type', 'PAYMENT')
        ->orderByDesc('date')
        ->take(5)
        ->get();

    $now = Carbon::now();
$studentId = auth()->user()->student_id;

$monthlyFines = collect(range(0, 3))->mapWithKeys(function ($i) use ($now, $studentId) {
    $month = $now->copy()->subMonths($i)->format('Y-m');
    $fines = \App\Models\Transaction::where('student_id', $studentId)
        ->where('transaction_type', 'FINE')
        ->where('date', 'like', "$month%")
        ->sum('fine_amount');
    return [$month => $fines];
})->reverse(); // from oldest to latest

    // Upcoming Events
    $upcomingEvents = Event::where('event_date', '>', now())
        ->orderBy('event_date')
        ->take(5)
        ->get();

    return view('student_dashboard', compact(
        'completedEvents',
        'upcomingEventsCount',
        'attendancePercentage',
        'totalFines',
        'recentPayments',
        'upcomingEvents',
        'attended',
        'absent',
        'monthlyFines'
    ));
}
}
