<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OfficerController extends Controller
{
    public function showOfficerPage()
{
    $currentUser = Auth::user();

    // Get only members (not officers/admins) from the same organization
    $users = User::where('org', $currentUser->org)
                 ->where('role', 'Member')
                 ->get();

    return view('officers', compact('users'));
}

 public function add(Request $request)

    {
        
        $request->validate([
            'user_id' => 'required|string',
            'officerPosition' => 'required|string',
        ]);

        $existingUser = User::where('id', $request->user_id)->first();

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
        $user->password = Hash::make($existingUser->email); // or student_id if needed
        $user->picture = $existingUser->picture;
        $user->role = $request->officerPosition;
        $user->student_id = $existingUser->student_id;
        $user->email_verified_at = $existingUser->email_verified_at ?? now();
        $user->org = $existingUser->org;


        // Set organization
  

        $user->save();  

        return back()->with('success', 'Officer added successfully.');
    }
}
