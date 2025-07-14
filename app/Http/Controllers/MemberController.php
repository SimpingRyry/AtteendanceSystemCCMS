<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
public function showMembers(Request $request)
{
    $user = Auth::user();

    $query = User::where('role', 'Member');

    // Restrict based on user's org if not CCMS Student Government
    if ($user->org !== 'CCMS Student Government') {
        $query->where('org', $user->org);
    }

    // Apply org filter if from CCMS SG and filter is selected
    if ($request->filled('org') && $user->org === 'CCMS Student Government') {
        $query->where('org', $request->org);
    }

    $users = $query->paginate(10);
    $org_list = OrgList::all();

    return view('members', compact('users', 'org_list'));
}
}
