<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrgDashboardController extends Controller
{
   public function dashboard()
{
    $orgId = Auth::user()->org;

    // Total members = users in same org with role 'member'
    $totalMembers = User::where('org', $orgId)
        ->where('role', 'Member')
        ->count();

    // Since members are registered, registered = totalMembers
    $registered = $totalMembers;

    // Transactions from same org
    $totalFines = Transaction::where('org', $orgId)
        ->where('transaction_type', 'FINE')
        ->sum('fine_amount');

    $totalCollectedFines = Transaction::where('org', $orgId)
        ->where('transaction_type', 'PAYMENT')
        ->sum('fine_amount');

    // Fines by month for charts
$finesByMonth = Transaction::where('org', $orgId)
    ->where('transaction_type', 'FINE')
    ->selectRaw('DATE_FORMAT(`date`, "%Y-%m") as month, SUM(fine_amount) as total')
    ->groupBy('month')
    ->orderBy('month')
    ->get()
    ->pluck('total', 'month');

    // Year level distribution from users table (if applicable)
 $yearLevelData = DB::table('users')
    ->join('student_list', 'users.student_id', '=', 'student_list.id_number')
    ->where('users.org', $orgId)
    ->where('users.role', 'member')
    ->selectRaw('student_list.section as label, COUNT(*) as count')
    ->groupBy('student_list.section')
    ->pluck('count', 'label');

    // Recent payments


    $upcomingEvents = Event::where('org', $orgId)
    ->whereDate('event_date', '>=', Carbon::today())
    ->orderBy('event_date')
    ->select('name', 'event_date') // Assuming these are the correct column names
    ->take(3)
    ->get();
 

    return view('dashboard_page', compact(
        'totalMembers', 'registered', 'totalCollectedFines', 'totalFines',
        'finesByMonth', 'yearLevelData','upcomingEvents'
    ));
}
}
