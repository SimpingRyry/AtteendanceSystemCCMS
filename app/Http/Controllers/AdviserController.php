<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\OrgList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdviserController extends Controller
{
    public function index()
    {
        $advisers = Adviser::all(); // Fetch all advisers
        $org_list = $org_list = OrgList::all();

        return view('advisers', compact('advisers','org_list')); // Make sure this file exists: resources/views/advisers.blade.php
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:adviser,email',
            'password'     => 'required|min:6',
            'organization' => 'required|string',
        ]);

        Adviser::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'org'          => $request->organization,
        ]);

        return redirect()->back()->with('success', 'Adviser added successfully!');
    }
}
