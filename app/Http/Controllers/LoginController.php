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

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);

        // Optional: session vars
        session([
            'user_name' => $user->name,
            'user_role' => $user->position,
            'user_img' => $user->image ?? 'default.png',
        ]);

        return redirect()->intended('/dashboard_page'); // or /dash
    }

    return back()->with('error', 'Invalid credentials.');
}
}
