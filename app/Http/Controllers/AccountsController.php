<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function show()
    {
        $users = User::select('id', 'name', 'email', 'role', 'org')->get();
        return view('accounts', compact('users'));
    }

    public function showAdmins()
{
    $users = User::select('id', 'name', 'email', 'role', 'org')
                      ->where('role', 'like', '%admin%')
                      ->get();

    return view('accounts', compact('users'));
}
public function showOfficers()
{
    $currentOrg = Auth::user()->org;

    $users = User::select('id', 'name', 'email', 'role', 'org')
                ->where('role', 'like', '%officer%')
                ->where('org', $currentOrg)
                ->get();

    return view('officers', compact('users'));
}
    public function update(Request $request)
{
    // Validate the input first
    $request->validate([
        'user_id' => 'required|exists:users,user_ID',
        'role' => 'required|string',
        'org' => 'required|string',
    ]);

    // Find the user by ID
    $user = User::where('user_ID', $request->user_id)->first();

    if ($user) {
        // Update role and org
        $user->role = $request->role;
        $user->org = $request->org;
        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    return redirect()->back()->with('error', 'User not found.');
}
}
