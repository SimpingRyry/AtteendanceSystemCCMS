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
    $authUser = Auth::user();
    $authOrg = $authUser->org;

    // Base query: only Members
    $query = User::where('role', 'Member');

    // Get the authenticated org record with its children
    $org = OrgList::where('org_name', $authOrg)->with('children')->first();
    $childOrgNames = $org?->children->pluck('org_name')->toArray() ?? [];
    $isParentOrg = !empty($childOrgNames);

    if ($isParentOrg) {
        // Parent org → include itself + children
        $query->whereIn('org', array_merge([$authOrg], $childOrgNames));
    } else {
        // Child org → only its own members
        $query->where('org', $authOrg);
    }

    $users = $query->paginate(10);
    $org_list = OrgList::all();

    return view('members', compact('users', 'org_list'));
}

}
