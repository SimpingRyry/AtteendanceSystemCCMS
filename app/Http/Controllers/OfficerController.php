<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\OfficerRole;
use App\Models\OrgList;

class OfficerController extends Controller
{
public function showOfficerPage(Request $request)
{
    $currentUser = Auth::user();
    $orgId = $currentUser->org;
    $role = $currentUser->role;

    // Get academic term from settings
    $term = Setting::where('key', 'academic_term')->value('value');
    preg_match('/\d{4}-\d{4}/', $term, $matches);
    $yearOnly = $matches[0] ?? 'N/A';

    // Start query
    $query = User::activeOfficers();

    // If NOT OSSD or Super Admin, limit to own org
    if (!in_array($role, ['OSSD', 'Super Admin'])) {
        $query->where('org', $orgId);
    }

    // Filter by academic term
    if ($request->filled('term')) {
        preg_match('/\d{4}-\d{4}/', $request->term, $searchMatch);

        if (!empty($searchMatch)) {
            $query->where('term', 'LIKE', '%' . $searchMatch[0] . '%');
        } else {
            $query->where('term', 'LIKE', '%' . $request->term . '%');
        }
    }

    // Optional: Filter by org via dropdown (for OSSD/Super Admin)
    if ($request->filled('org')) {
        $query->where('org', $request->org);
    }

    // Ensure it finishes one org before moving to next
    $query->orderBy('org');

    // Paginate the result
    $users = $query->paginate(10);

    // Officer roles (based on current user's org)
    $officerRoles = OfficerRole::where('org', $orgId)->get();

    // Fetch all orgs for dropdown
    $orgs = OrgList::orderBy('org_name')->get();

    return view('officers', compact('users', 'yearOnly', 'officerRoles', 'orgs'));
}


public function add(Request $request)
{
    $request->validate([
        'user_id' => 'required|string',
        'officerPosition' => 'required|string',
    ]);

    $existingUser = User::where('id', $request->user_id)->firstOrFail();
    $currentTerm = Setting::where('key', 'academic_term')->value('value');

    // Check if officer with same role already exists
    $alreadyExists = User::where('email', $existingUser->email)
                         ->where('role', $request->officerPosition)
                         ->exists();

    if ($alreadyExists) {
        return back()->with('error', 'This user already has that officer role.');
    }

    // Create new user record with officer role
    $user = new User();
    $user->name = $existingUser->name;
    $user->email = $existingUser->email;
    $user->password = Hash::make($existingUser->email); // or use student_id
    $user->picture = $existingUser->picture;
    $user->role = $request->officerPosition . ' - Officer';
    $user->student_id = $existingUser->student_id;
    $user->email_verified_at = $existingUser->email_verified_at ?? now();
    $user->term = $currentTerm;

    // 👇 Use auth user's org instead of member's org
    $user->org = Auth::user()->org;

    $user->save();

    return back()->with('success', 'Officer added successfully.');
}

}
