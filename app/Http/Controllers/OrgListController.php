<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgList;

class OrgListController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'org_name' => 'required|string|max:255',
        'description' => 'required|string',
        'org_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'bg_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
    ]);

    // Save logo
    $orgLogo = $request->file('org_logo');
    $orgLogoName = time() . '_logo.' . $orgLogo->getClientOriginalExtension();
    $orgLogo->move(public_path('images/org_list'), $orgLogoName);

    // Save background image
    $bgImage = $request->file('bg_image');
    $bgImageName = time() . '_bg.' . $bgImage->getClientOriginalExtension();
    $bgImage->move(public_path('images/org_list'), $bgImageName);

    // Save to DB
    OrgList::create([
        'org_name' => $request->org_name,
        'description' => $request->description,
        'org_logo' => 'org_list/' . $orgLogoName,
        'bg_image' => 'org_list/' . $bgImageName,
    ]);

    return back()->with('success', 'Organization added successfully!');
}
}

