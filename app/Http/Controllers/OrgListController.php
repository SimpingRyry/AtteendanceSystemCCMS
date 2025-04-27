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
    // Validate the incoming request
    $request->validate([
        'org_name'   => 'required|string|max:255',
        'description'=> 'required|string',
        'org_logo'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'bg_image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'name'       => 'required|string|max:255',
        'email'      => 'required|string|email|max:255|unique:users,email',
        'password'   => 'required|string|min:6',
    ]);

    // Handle organization logo upload
    $orgLogoName = time() . '_logo.' . $request->org_logo->extension();
    $request->org_logo->move(public_path('images'), $orgLogoName);

    // Handle background image upload
    $bgImageName = time() . '_bg.' . $request->bg_image->extension();
    $request->bg_image->move(public_path('images'), $bgImageName);

    // Create organization
    $organization = OrgList::create([
        'org_name'    => $request->org_name,
        'description' => $request->description,
        'org_logo'    => $orgLogoName,
        'bg_image'    => $bgImageName,
    ]);

    // Create admin user for organization
    $admin = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'org'      => $organization->org_name, // Save org name in 'org' column of users table
        'role'     => 'admin', // Optional: if you have a 'role' column
    ]);

    return redirect()->back()->with('success', 'Organization and Admin created successfully.');
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
