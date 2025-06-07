<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OfficerController extends Controller
{
    public function showOfficerPage()
{
    $currentUser = Auth::user();
    
    $term = Setting::where('key', 'academic_term')->value('value'); // e.g., "25-2 Second Sem A.Y. 2025-2026"

    // Extract the year range (last part)
    preg_match('/\d{4}-\d{4}/', $term, $matches);
    $yearOnly = $matches[0] ?? 'N/A'; // fallback if not found

    // Get only members (not officers/admins) from the same organization
    $users = User::activeOfficers()->get();

    return view('officers', compact('users','yearOnly'));
}

 public function add(Request $request)

    {
        
        $request->validate([
            'user_id' => 'required|string',
            'officerPosition' => 'required|string',
        ]);

        $existingUser = User::where('id', $request->user_id)->first();
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
        $user->password = Hash::make($existingUser->email); // or student_id if needed
        $user->picture = $existingUser->picture;
        $user->role = $request->officerPosition . ' - Officer';
        $user->student_id = $existingUser->student_id;
        $user->email_verified_at = $existingUser->email_verified_at ?? now();
        $user->term = $currentTerm; 
        $user->org = $existingUser->org;


        // Set organization
  

        $user->save();  

        return back()->with('success', 'Officer added successfully.');
    }
}
