<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\OrgList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Setting;

class AdviserController extends Controller
{
public function index()
{
    $fullTerm = Setting::where('key', 'academic_term')->value('value');

    // Extract year like 2025-2026 from the term string
    preg_match('/\d{4}-\d{4}/', $fullTerm, $matches);
    $termYear = $matches[0] ?? 'Unknown Year';

    $advisers = User::where('role', 'Adviser')
                    ->where('term', 'like', "%$termYear%")
                    ->get();

    $org_list = OrgList::all();

    return view('advisers', compact('advisers', 'org_list'));
}




public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email',
        'password'     => 'required|min:6',
        'organization' => 'required|string',
    ]);

    if ($validator->fails()) {
        if ($validator->errors()->has('email')) {
            return redirect()->back()->with('error', 'Email already exists')->withInput();
        }

        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Get the current academic term
    $term = Setting::where('key', 'academic_term')->value('value');

    // Create user (adviser)
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'org'      => $request->organization,
        'role'     => 'Adviser',
        'term'     => $term, // add the term here
    ]);

    event(new Registered($user));

    return redirect()->back()->with('success', 'Adviser added successfully!');
}


}
