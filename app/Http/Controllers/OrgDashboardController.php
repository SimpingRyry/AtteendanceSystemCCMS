<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrgDashboardController extends Controller
{
public function dashboard()
{
    $authUser = Auth::user();
    $authOrg = $authUser->org;

    // Get the authenticated org record with its children
    $org = OrgList::where('org_name', $authOrg)->with('children')->first();
    $childOrgNames = $org?->children->pluck('org_name')->toArray() ?? [];
    $isParentOrg = !empty($childOrgNames);

    // Registered members count
    $totalMembersQuery = User::where('role', 'Member');
    if ($isParentOrg) {
        $totalMembersQuery->whereIn('org', array_merge([$authOrg], $childOrgNames));
    } else {
        $totalMembersQuery->where('org', $authOrg);
    }
    $totalMembers = $totalMembersQuery->count();
    $registered = $totalMembers;

    // Year level distribution
    $yearLevelQuery = DB::table('users')
        ->join('student_list', 'users.student_id', '=', 'student_list.id_number')
        ->where('users.role', 'Member');

    if ($isParentOrg) {
        $yearLevelQuery->whereIn('users.org', array_merge([$authOrg], $childOrgNames));
    } else {
        $yearLevelQuery->where('users.org', $authOrg);
    }

    $yearLevelData = $yearLevelQuery
        ->selectRaw('student_list.section as label, COUNT(*) as count')
        ->groupBy('student_list.section')
        ->pluck('count', 'label');

    // Transactions and events stay strictly scoped to auth org only
    $totalFines = Transaction::where('org', $authOrg)
        ->where('transaction_type', 'FINE')
        ->sum('fine_amount');

    $totalCollectedFines = Transaction::where('org', $authOrg)
        ->where('transaction_type', 'PAYMENT')
        ->sum('fine_amount');

    $finesByMonth = Transaction::where('org', $authOrg)
        ->where('transaction_type', 'FINE')
        ->selectRaw('DATE_FORMAT(`date`, "%Y-%m") as month, SUM(fine_amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total', 'month');

    $upcomingEvents = Event::where('org', $authOrg)
        ->whereDate('event_date', '>=', Carbon::today('Asia/Manila'))
        ->orderBy('event_date')
        ->select('name', 'event_date', 'times')
        ->take(3)
        ->get();

    $recentTransactions = Transaction::where('org', $authOrg)
        ->where('transaction_type', 'PAYMENT')
        ->orderBy('date', 'desc')
        ->take(2)
        ->get();

    return view('dashboard_page', compact(
        'totalMembers', 'registered', 'totalCollectedFines', 'totalFines',
        'finesByMonth', 'yearLevelData', 'upcomingEvents', 'recentTransactions'
    ));
}



}
