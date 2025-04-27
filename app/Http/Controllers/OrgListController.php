<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgList;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\File;

class OrgListController extends Controller
{
    public function index()
    {
        $org_list = \App\Models\OrgList::all(); // fetch all orgs
        return view('manage_orgs_page', compact('org_list')); // pass it to view
    }
    public function store(Request $request)
{
    $request->validate([
        'org_name' => 'required|string|max:255',
        'description' => 'required|string',
        'org_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'bg_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
    ]);

    // Save logo
    $orgLogo = $request->file('org_logo');
    $orgLogoName = time() . '_logo.' . $orgLogo->getClientOriginalExtension();
    $orgLogo->move(public_path('images/org_list'), $orgLogoName);

    // Save background image
    $bgImage = $request->file('bg_image');
    $bgImageName = time() . '_bg.' . $bgImage->getClientOriginalExtension();
    $bgImage->move(public_path('images/org_list'), $bgImageName);

    // Create the organization first
    $organization = OrgList::create([
        'org_name' => $request->org_name,
        'description' => $request->description,
        'org_logo' => 'org_list/' . $orgLogoName,
        'bg_image' => 'org_list/' . $bgImageName,
    ]);

    // Create the user with the latest organization name
    User::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'org' => $organization->org_name, // Get the saved org name
        'position' => 'admin',
        'picture' => 'default.png'
    ]);

        return back()->with('success', 'Organization added successfully!');
    }

    // ✅ Update an existing organization
    public function update(Request $request, $id)
    {
        $org = OrgList::findOrFail($id);

        // Validate only the file inputs; make text fields optional
        $request->validate([
            'org_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'org_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        // Conditionally update org_name
        if ($request->filled('org_name')) {
            $org->org_name = $request->org_name;
        }

        // Conditionally update description
        if ($request->filled('description')) {
            $org->description = $request->description;
        }

        // Update org_logo if uploaded
        if ($request->hasFile('org_logo')) {
            $logoPath = public_path('images/' . $org->org_logo);
            if (File::exists($logoPath)) {
                File::delete($logoPath);
            }

            $orgLogo = $request->file('org_logo');
            $orgLogoName = time() . '_logo.' . $orgLogo->getClientOriginalExtension();
            $orgLogo->move(public_path('images/org_list/'), $orgLogoName);
            $org->org_logo = 'org_list/' . $orgLogoName;
        }

        // Update bg_image if uploaded
        if ($request->hasFile('bg_image')) {
            $bgPath = public_path('images/' . $org->bg_image);
            if (File::exists($bgPath)) {
                File::delete($bgPath);
            }

            $bgImage = $request->file('bg_image');
            $bgImageName = time() . '_bg.' . $bgImage->getClientOriginalExtension();
            $bgImage->move(public_path('images/org_list/'), $bgImageName);
            $org->bg_image = 'org_list/' . $bgImageName;
        }

        $org->save();

        return back()->with('success', 'Organization updated successfully!');
    }

    // ❌ Delete an organization
    public function destroy($id)
    {
        $org = OrgList::findOrFail($id);

        // Delete logo file
        $logoPath = public_path('images/' . $org->org_logo);
        if (File::exists($logoPath)) File::delete($logoPath);

        // Delete background image
        $bgPath = public_path('images/' . $org->bg_image);
        if (File::exists($bgPath)) File::delete($bgPath);

        $org->delete();

        return back()->with('success', 'Organization deleted successfully!');
    }
}
