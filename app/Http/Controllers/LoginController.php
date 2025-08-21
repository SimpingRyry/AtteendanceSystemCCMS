<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
