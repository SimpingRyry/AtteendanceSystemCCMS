<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Student;
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
}
