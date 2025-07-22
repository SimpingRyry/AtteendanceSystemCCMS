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
    $user = Auth::user();
    $orgId = $user->org;

    $isCCMS = $orgId === 'CCMS Student Government';

    // Registered students (all orgs if CCMS)
    $totalMembersQuery = User::where('role', 'Member');
    if (!$isCCMS) {
        $totalMembersQuery->where('org', $orgId);
    }
    $totalMembers = $totalMembersQuery->count();
    $registered = $totalMembers;

    // Year level distribution (all orgs if CCMS)
    $yearLevelQuery = DB::table('users')
        ->join('student_list', 'users.student_id', '=', 'student_list.id_number')
        ->where('users.role', 'member');
    if (!$isCCMS) {
        $yearLevelQuery->where('users.org', $orgId);
    }
    $yearLevelData = $yearLevelQuery
        ->selectRaw('student_list.section as label, COUNT(*) as count')
        ->groupBy('student_list.section')
        ->pluck('count', 'label');

    // These stay scoped by org
    $totalFines = Transaction::where('org', $orgId)
        ->where('transaction_type', 'FINE')
        ->sum('fine_amount');

    $totalCollectedFines = Transaction::where('org', $orgId)
        ->where('transaction_type', 'PAYMENT')
        ->sum('fine_amount');

    $finesByMonth = Transaction::where('org', $orgId)
        ->where('transaction_type', 'FINE')
        ->selectRaw('DATE_FORMAT(`date`, "%Y-%m") as month, SUM(fine_amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total', 'month');

    $upcomingEvents = Event::where('org', $orgId)
        ->whereDate('event_date', '>=', Carbon::today('Asia/Manila'))
        ->orderBy('event_date')
        ->select('name', 'event_date')
        ->take(3)
        ->get();

    return view('dashboard_page', compact(
        'totalMembers', 'registered', 'totalCollectedFines', 'totalFines',
        'finesByMonth', 'yearLevelData', 'upcomingEvents'
    ));
}


}
