<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OfficerController extends Controller
{public function showOfficerPage(Request $request)
{
    $currentUser = Auth::user();
    $orgId = $currentUser->org;

    // Get academic term from settings
    $term = Setting::where('key', 'academic_term')->value('value');
    preg_match('/\d{4}-\d{4}/', $term, $matches);
    $yearOnly = $matches[0] ?? 'N/A';

    $query = User::activeOfficers()->where('org', $orgId);

    // Filter by year or full text if applicable
    if ($request->filled('term')) {
        preg_match('/\d{4}-\d{4}/', $request->term, $searchMatch);

        if (!empty($searchMatch)) {
            $query->where('term', 'LIKE', '%' . $searchMatch[0] . '%');
        } else {
            $query->where('term', 'LIKE', '%' . $request->term . '%');
        }
    }

    $users = $query->paginate(10);

    return view('officers', compact('users', 'yearOnly'));
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
