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
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Get all users with this email
        $users = User::where('email', $request->email)->get();
    
        // Loop through and find the first user where the password matches
        foreach ($users as $user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
    
                session([
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'user_img' => $user->image ?? 'default.png',
                ]);
    
                if ($user->role === 'Member') {
                    return redirect()->intended('/student_dashboard');
                } elseif ($user->role === 'Super Admin') {
                    return redirect()->intended('/super_dashboard');
                } elseif ($user->role === 'OSSD') {
                    return redirect()->intended('/ossd-dashboard');
                } else {
                    return redirect()->intended('/dashboard_page');
                }
            }
        }
    
        // If none matched
        return back()->with('error', 'Invalid credentials.');
    }
}
