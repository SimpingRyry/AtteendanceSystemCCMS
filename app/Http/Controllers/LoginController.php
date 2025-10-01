<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // public function showLogin()
    // {
    //     return view('auth.login');
    // }

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        // password is optional now
    ]);

    // Clean input email
    $cleanEmail = trim(preg_replace('/^[\*\s]+/', '', $request->email));

    // Get all users (since DB may contain *** in front)
    $users = User::all();

    foreach ($users as $user) {
        // Clean DB email and name
        $dbEmail = trim(preg_replace('/^[\*\s]+/', '', $user->email));
        $dbName  = trim(preg_replace('/^[\*\s]+/', '', $user->name));

        if ($dbEmail === $cleanEmail) {
            // If user password is null and no password entered → allow login
            if (is_null($user->password) && empty($request->password)) {
                Auth::login($user);
            }
            // Or if user has password and it matches
            elseif (!is_null($user->password) && Hash::check($request->password, $user->password)) {
                Auth::login($user);
            } else {
                continue;
            }

            // ✅ Check officer term if role ends with "- Officer"
         if (str_ends_with($user->role, '- Officer')) {
    $academicTermRecord = Setting::where('key', 'academic_term')->first();
    $activeTerm = $academicTermRecord->value ?? null;

    if ($activeTerm) {
        // Extract academic year like "2024-2025" from "24-1 First Sem A.Y. 2024-2025"
        preg_match('/\d{4}-\d{4}/', $activeTerm, $matchesActive);
        $activeYear = $matchesActive[0] ?? null;

        // Extract academic year from user term
        preg_match('/\d{4}-\d{4}/', $user->term, $matchesUser);
        $userYear = $matchesUser[0] ?? null;

        Log::info("Active year: " . $activeYear . " | User year: " . $userYear);

        // If they don't match → block login
        if ($activeYear !== $userYear) {
            Auth::logout();
            return back()->with('error', 'Your officer account has expired.');
        }
    }
}
            // Store session with cleaned name
            session([
                'user_name' => $dbName,
                'user_role' => $user->role,
                'user_img'  => $user->image ?? 'default.png',
            ]);

            // Redirect based on role
            return match ($user->role) {
                'Member'      => redirect()->intended('/student_dashboard'),
                'Super Admin' => redirect()->intended('/super_dashboard'),
                'OSSD'        => redirect()->intended('/ossd-dashboard'),
                default       => redirect()->intended('/dashboard_page'),
            };
        }
    }

    return back()->with('error', 'Invalid credentials.');
}



}
