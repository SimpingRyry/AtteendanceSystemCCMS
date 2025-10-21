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

$upcomingEvents = Event::whereDate('event_date', '>=', Carbon::today('Asia/Manila'))
    ->orderBy('event_date')
    ->select('name', 'event_date', 'times')
    ->take(3)
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
    $studentId = $user->student_id;

    // Completed events
    $completedEvents = Attendance::where('student_id', $studentId)->count();

    // Upcoming events count
$upcomingEventsCount = Event::where('event_date', '>', now())
    ->select('name', 'venue', 'org')
    ->distinct()
    ->count();
    // Attendance breakdown
    $attended = Attendance::where('student_id', $studentId)
        ->where(function ($q) {
            $q->whereIn('status', ['On Time', 'Late'])
              ->orWhereIn('status_morning', ['On Time', 'Late'])
              ->orWhereIn('status_afternoon', ['On Time', 'Late']);
        })->count();

    $absent = Attendance::where('student_id', $studentId)
        ->where(function ($q) {
            $q->where('status', 'Absent')
              ->orWhere('status_morning', 'Absent')
              ->orWhere('status_afternoon', 'Absent');
        })->count();

    $attendancePercentage = $completedEvents > 0
        ? round(($attended / $completedEvents) * 100)
        : 0;

    // Total fines calculation
    $fines = Transaction::where('student_id', $studentId)
        ->where('transaction_type', 'FINE')
        ->sum('fine_amount');

    $payments = Transaction::where('student_id', $studentId)
        ->where('transaction_type', 'PAYMENT')
        ->sum('fine_amount');

    $totalFines = $fines - $payments;

    // Recent payments
    $recentPayments = Transaction::where('student_id', $studentId)
        ->where('transaction_type', 'PAYMENT')
        ->orderByDesc('date')
        ->take(5)
        ->get();

    $now = Carbon::now();

    // Monthly fines
    $monthlyFines = collect(range(0, 3))->mapWithKeys(function ($i) use ($now, $studentId) {
        $month = $now->copy()->subMonths($i)->format('Y-m');
        $fines = \App\Models\Transaction::where('student_id', $studentId)
            ->where('transaction_type', 'FINE')
            ->where('date', 'like', "$month%")
            ->sum('fine_amount');
        return [$month => $fines];
    })->reverse();

    // Upcoming events list
    $upcomingEvents = Event::where('event_date', '>=', Carbon::today('Asia/Manila'))
        ->orderBy('event_date')
        ->take(5)
        ->get();

    /**
     * EVALUATION ASSIGNMENTS SECTION
     */
    // Get attended event IDs
    $attendedEventIds = DB::table('attendances')
        ->where('student_id', $studentId)
        ->pluck('event_id');

    // Get latest EvaluationAssignment
    $latestAssignment = \App\Models\EvaluationAssignment::with(['evaluation', 'event'])
        ->whereIn('event_id', $attendedEventIds)
        ->whereHas('event')
        ->get()
        ->sortByDesc(fn ($assignment) => $assignment->event->event_date)
        ->first();

    // Attach attendance
    if ($latestAssignment) {
        $attendance = Attendance::where('student_id', $studentId)
            ->where('event_id', $latestAssignment->event_id)
            ->first();
        $latestAssignment->attendance = $attendance;
    }

    $assignments = $latestAssignment ? [$latestAssignment] : [];

    return view('student_dashboard', compact(
        'completedEvents',
        'upcomingEventsCount',
        'attendancePercentage',
        'totalFines',
        'recentPayments',
        'upcomingEvents',
        'attended',
        'absent',
        'monthlyFines',
        'assignments' // added here for the Evaluation section
    ));
}

}
