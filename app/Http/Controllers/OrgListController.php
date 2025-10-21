<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Student;
use App\Models\FineSetting;
use App\Models\DeliveryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class OrgListController extends Controller
{
  public function index()
{
    $org_list = OrgList::all(); // fetch all orgs
    $deliveryUnits = DeliveryUnit::with('courses')->get(); // if needed for other uses
    $courses = Course::all(); // fetch all courses directly

    return view('manage_orgs_page', compact('org_list', 'deliveryUnits', 'courses'));
}
    public function showOrg()
{
    $org_list = OrgList::all();
    return view('student', compact('org_list'));
}

public function setHierarchy(Request $request)
{
    $request->validate([
        'child_org_id' => 'required|exists:org_list,id',
        'parent_org_id' => 'nullable|exists:org_list,id|different:child_org_id',
    ]);

    $child = OrgList::findOrFail($request->child_org_id);
    $child->parent_org_id = $request->parent_org_id; // assumes you have this column
    $child->save();

    return back()->with('success', 'Hierarchy updated successfully!');
}
public function store(Request $request)
{
    try {
        $request->validate([
            'org_name'          => 'required|string|max:255',
            'description'       => 'required|string',
            'org_logo'          => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'bg_image'          => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'student_csv'       => 'nullable|file|mimes:csv,txt',

            'adviser_name'      => 'nullable|string|max:255',
            'adviser_email'     => 'nullable|string|email|max:255|unique:users,email',
            'adviser_password'  => 'nullable|string|min:6',

            'president_name'    => 'nullable|string|max:255',
            'president_email'   => 'nullable|email|unique:users,email',
            'president_password'=> 'nullable|string|min:6',
        ]);

        // sanitize and file uploads
        $sanitizedOrgName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->org_name);

        $orgLogoName = $sanitizedOrgName . '_logo.' . $request->org_logo->extension();
        $request->org_logo->move(public_path('images/org_list'), $orgLogoName);

        $bgImageName = $sanitizedOrgName . '_bg.' . $request->bg_image->extension();
        $request->bg_image->move(public_path('images'), $bgImageName);

        $term = Setting::where('key', 'academic_term')->value('value');

        // create org
        $organization = OrgList::create([
            'org_name'    => $request->org_name,
            'description' => $request->description,
            'org_logo'    => $orgLogoName,
            'bg_image'    => $bgImageName,
        ]);

        // default fine settings
        FineSetting::firstOrCreate(
            ['org' => $organization->org_name],
            [
                'absent_member'        => 50,
                'late_member'          => 20,
                'absent_officer'       => 100,
                'late_officer'         => 40,
                'grace_period_minutes' => 15,
                'morning_in'      => '08:00:00',
                'morning_out'     => '12:00:00',
                'afternoon_in'    => '13:00:00',
                'afternoon_out'   => '17:00:00',
            ]
        );

        // create adviser
        if ($request->filled('adviser_name') && $request->filled('adviser_email') && $request->filled('adviser_password')) {
            $adviser = User::create([
                'name'     => $request->adviser_name,
                'email'    => $request->adviser_email,
                'password' => Hash::make($request->adviser_password),
                'org'      => $organization->org_name,
                'role'     => 'Adviser',
                'term'     => $term
            ]);
            event(new Registered($adviser));
        }

        // create president
        if ($request->filled('president_name') && $request->filled('president_email') && $request->filled('president_password')) {
            $president = User::create([
                'name'     => $request->president_name,
                'email'    => $request->president_email,
                'password' => Hash::make($request->president_password),
                'org'      => $organization->org_name,
                'role'     => 'President - Officer',
                'term'     => $term
            ]);
            event(new Registered($president));
        }

        // ✅ Integrated CSV import process (with email support)
        if ($request->hasFile('student_csv')) {
            $file = $request->file('student_csv');
            $path = $file->getRealPath();

            $rows = array_map('str_getcsv', file($path));
            $rows = array_slice($rows, 7); // Skip first 7 lines

            $studentsUpdated = 0;
            $studentsAdded = 0;

            foreach ($rows as $index => $row) {
                // Skip if row is completely empty
                if (empty(array_filter($row))) continue;

                // Safely handle birth date
                $birthDate = null;
                if (!empty($row[9])) {
                    try {
                        $birthDate = \Carbon\Carbon::createFromFormat('m/d/Y', $row[9])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $birthDate = null;
                    }
                }

                // Find existing student by ID number and org
                $student = Student::where('id_number', $row[1] ?? null)
                                  ->where('org', $organization->org_name)
                                  ->first();

                if ($student) {
                    // Update only changed fields
                    $changes = [
                        'no'         => $row[0] ?? null,
                        'name'       => $row[2] ?? null,
                        'gender'     => $row[3] ?? null,
                        'course'     => $row[4] ?? null,
                        'year'       => $row[5] ?? null,
                        'units'      => $row[6] ?? null,
                        'section'    => $row[7] ?? null,
                        'contact_no' => $row[8] ?? null,
                        'birth_date' => $birthDate,
                        'address'    => $row[10] ?? null,
                        'email'      => !empty($row[11]) ? $row[11] : null,
                    ];

                    $hasChanges = false;
                    foreach ($changes as $key => $value) {
                        if ($student->$key != $value) {
                            $student->$key = $value;
                            $hasChanges = true;
                        }
                    }

                    if ($hasChanges) {
                        $student->save();
                        $studentsUpdated++;
                    }
                } else {
                    // Create new student
                    Student::create([
                        'no'         => $row[0] ?? null,
                        'id_number'  => $row[1] ?? null,
                        'name'       => $row[2] ?? null,
                        'gender'     => $row[3] ?? null,
                        'course'     => $row[4] ?? null,
                        'year'       => $row[5] ?? null,
                        'units'      => $row[6] ?? null,
                        'section'    => $row[7] ?? null,
                        'contact_no' => $row[8] ?? null,
                        'birth_date' => $birthDate,
                        'address'    => $row[10] ?? null,
                        'email'      => !empty($row[11]) ? $row[11] : null,
                        'status'     => 'Unregistered',
                        'org'        => $organization->org_name,
                    ]);
                    $studentsAdded++;
                }
            }
        }

        return redirect()->back()->with(
            'success',
            'Organization "' . $organization->org_name . '" created successfully. ' .
            ($studentsAdded + $studentsUpdated > 0
                ? "$studentsAdded students added, $studentsUpdated updated."
                : 'No student data imported.') .
            ' Default fine settings applied.'
        );

    } catch (\Exception $e) {
        Log::error('Error creating organization: ' . $e->getMessage());
        return redirect()->back()->withInput()->with(
            'error',
            'An error occurred while creating the organization: ' . $e->getMessage()
        );
    }
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
