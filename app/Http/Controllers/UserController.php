<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgList;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
public function getEligibleMembers()
{
    $authUser = Auth::user();
    $authOrg = $authUser->org;

    $currentTerm = Setting::where('key', 'academic_term')->value('value');

    // Get the authenticated org record with its children
    $org = OrgList::where('org_name', $authOrg)->with('children')->first();
    $childOrgNames = $org?->children->pluck('org_name')->toArray() ?? [];
    $isParentOrg = !empty($childOrgNames);

    // Step 1: Get members under the auth org (and children if parent)
    $membersQuery = User::where('role', 'Member');

    if ($isParentOrg) {
        $membersQuery->whereIn('org', array_merge([$authOrg], $childOrgNames));
    } else {
        $membersQuery->where('org', $authOrg);
    }

    $members = $membersQuery->get();

    // Step 2: Extract all student_ids
    $studentIds = $members->pluck('student_id');

    // Step 3: Find users with same student_id who are officers this term
    $officersThisTerm = User::whereIn('student_id', $studentIds)
        ->where('role', 'like', '%Officer%')
        ->where('term', $currentTerm)
        ->pluck('student_id');

    // Step 4: Reject members who are also officers this term
    $eligibleMembers = $members->reject(function ($user) use ($officersThisTerm) {
        return $officersThisTerm->contains($user->student_id);
    });

    // Step 5: Return filtered data
    return response()->json(
        $eligibleMembers->values()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'picture' => $user->picture,
            ];
        })
    );
}

public function updatePassword(Request $request, $id)
{
    $request->validate([
        'password' => 'required|string|min:3|confirmed',
    ]);

    $user = User::findOrFail($id);
    $user->password = bcrypt($request->password);
    $user->save();

    return back()->with('success', 'Password updated successfully.');
}
}
