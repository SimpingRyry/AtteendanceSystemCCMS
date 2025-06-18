<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OSSDController extends Controller
{

    public function index()
{
    $ossds = User::where('role', 'ossd')->get();
    return view('ossd', compact('ossds'));
}
     public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'ossd', // Make sure this column exists in your `users` table
        ]);

        return redirect()->back()->with('success', 'OSSD user created successfully.');
    }
}
