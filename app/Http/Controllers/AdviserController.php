<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\OrgList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdviserController extends Controller
{
public function index()
{
    $advisers = User::where('role', 'Adviser')->get(); // Fetch all users with role 'adviser'
    $org_list = OrgList::all();

    return view('advisers', compact('advisers', 'org_list')); // View file should still be advisers.blade.php
}



public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email',
        'password'     => 'required|min:6',
        'organization' => 'required|string',
    ]);

    // Handle validation errors
    if ($validator->fails()) {
        if ($validator->errors()->has('email')) {
            return redirect()->back()->with('error', 'Email already exists')->withInput();
        }

        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Create user (adviser)
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'org'      => $request->organization,
        'role'     => 'Adviser', // optional: set role if you're using roles
    ]);

    // Fire the Registered event
    event(new Registered($user));

    return redirect()->back()->with('success', 'Adviser added successfully!');
}

}
