<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the form input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to log the user in
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Optional: set extra session variables
            session([
                'user_name' => $user->name,
                'user_role' => $user->position,
                'user_img' => $user->image ?? 'default.png',
            ]);

            return redirect()->intended('/dash');
        }

        return back()->with('error', 'Invalid credentials.');
    }
}