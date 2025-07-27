<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgList;
use App\Models\User;
use App\Models\Student;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OssdDashboardController extends Controller
{
public function index()
{
    // Basic Counts
    $totalOrganizations = OrgList::count();
    $totalEvents = Event::count();
    $totalUsers = User::count();
    $totalStudents = Student::count();

    // Events per Organization
    $orgs = OrgList::pluck('org_name');
    $eventsPerOrg = [];

    foreach ($orgs as $orgName) {
        $eventCount = Event::where('org', $orgName)->count();
        $eventsPerOrg[$orgName] = $eventCount;
    }

    // Student distribution by course
    $studentDistribution = Student::select('course', DB::raw('count(*) as total'))
        ->groupBy('course')
        ->pluck('total', 'course');

    // Upcoming Events
 $upcomingEvents = Event::whereDate('event_date', '>=', Carbon::today('Asia/Manila'))
    ->orderBy('event_date')
    ->select('name', 'event_date', 'times')
    ->take(6)
    ->get();

    // ✅ New: Get Registered Students
    $registeredStudents = Student::where('status', 'Registered')->count();

    // ✅ New: Get Latest Organization
    $latestOrg = OrgList::latest()->first();

    return view('ossd-dashboard', compact(
        'totalOrganizations',
        'totalEvents',
        'totalUsers',
        'totalStudents',
        'eventsPerOrg',
        'studentDistribution',
        'upcomingEvents',
        'registeredStudents',
        'latestOrg'
    ));
}
}
