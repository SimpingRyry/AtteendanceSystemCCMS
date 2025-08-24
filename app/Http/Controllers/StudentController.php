<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\Logs;

class StudentController extends Controller
{
public function store(Request $request)
{
    
    Log::info('Starting user creation', ['request' => $request->all()]);

    $request->validate([
        'sname' => 'required|string|max:255',
        'student_id' => 'required|string|max:255',
        'course' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'uploaded_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'fingerprint' => 'nullable|int|max:255',
        'captured_image' => 'nullable|string',
        'organization' => 'nullable|string|max:255',
        'role' => 'required|string|max:255',
        'fingerprint_user_id' => 'nullable|integer',
        'sg_officer_role' => 'nullable|string|max:255',
    ]);

    // ❌ Prevent being officer in both main org and Student Government
    if (
        Str::lower($request->role) !== 'member' &&
        $request->filled('sg_officer_role')
    ) {
        return redirect()->back()->with('error', 'You cannot be an officer in both your local organization and the Student Government.');
    }

    // ✅ Handle image requirements
    if (!$request->uploaded_picture && !$request->captured_image) {
        return back()->withErrors(['error' => 'Please upload or capture a picture.']);
    }

    if ($request->uploaded_picture && $request->captured_image) {
        return back()->withErrors(['error' => 'Only one image should be provided.']);
    }

    $filename = null;

    if ($request->hasFile('uploaded_picture')) {
        $image = $request->file('uploaded_picture');
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads'), $filename);
    }

    if ($request->captured_image) {
        $image = $request->captured_image;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $filename = uniqid() . '.png';
        file_put_contents(public_path('uploads/' . $filename), $imageData);
    }

    // ✅ Determine organization
    $currentUser = Auth::user();
    $currentUserOrgName = $currentUser->org;
    $currentUserOrg = \App\Models\OrgList::where('org_name', $currentUserOrgName)->first();
    $isParentOrg = $currentUserOrg && $currentUserOrg->children()->exists();

    if ($isParentOrg && $request->filled('organization')) {
        $validOrgs = $currentUserOrg->children()->pluck('org_name')->toArray();
        if (!in_array($request->organization, $validOrgs)) {
            return back()->withErrors(['organization' => 'Invalid organization selected.']);
        }
        $organization = $request->organization;
    } else {
        $organization = $currentUserOrgName;
    }

    // ✅ Create base MEMBER user
    $memberUser = new User();
    $memberUser->name = $request->sname;
    $memberUser->email = $request->email;
    $memberUser->password = Hash::make($request->student_id);
    $memberUser->picture = $filename;
    $memberUser->role = 'Member';
    $memberUser->student_id = $request->student_id;
    $memberUser->org = $organization;

    Log::info('Saving Member account', $memberUser->toArray());
    $memberUser->save();

    event(new Registered($memberUser));

    $term = Setting::where('key', 'academic_term')->value('value');

    // ✅ Main org officer account
    if (Str::lower($request->role) !== 'member') {
        $officerUser = new User();
        $officerUser->name = $request->sname;
        $officerUser->email = $request->email;
        $officerUser->password = Hash::make($request->email);
        $officerUser->picture = $filename;
        $officerUser->role = $request->role . ' - Officer';
        $officerUser->student_id = $request->student_id;
        $officerUser->org = $organization;
        $officerUser->email_verified_at = now();
        $officerUser->term = $term;

        Log::info('Saving Officer account with term', $officerUser->toArray());
        $officerUser->save();
    }

    // ✅ SG officer account (if Member but selected SG role)
    if (
        Str::lower($request->role) === 'member' &&
        $request->filled('sg_officer_role')
    ) {
        $sgOfficer = new User();
        $sgOfficer->name = $request->sname;
        $sgOfficer->email = $request->email;
        $sgOfficer->password = Hash::make($request->email);
        $sgOfficer->picture = $filename;
        $sgOfficer->role = $request->sg_officer_role . ' - Officer';
        $sgOfficer->student_id = $request->student_id;
        $sgOfficer->org = $currentUserOrgName;
        $sgOfficer->email_verified_at = now();
        $sgOfficer->term = $term;

        Log::info('Saving SG Officer account', $sgOfficer->toArray());
        $sgOfficer->save();
    }

    // ✅ Update student record
    $student = Student::where('id_number', $request->student_id)->first();

    if ($student) {
        if ($request->filled('fingerprint')) {
            $student->f_id = $request->fingerprint;
        }

        if ($request->filled('fingerprint_user_id')) {
            $student->f_id = $request->fingerprint_user_id;
        }

        $student->status = 'Registered';
        $student->save();
    } else {
        return redirect()->back()->with('error', 'Student record not found.');
    }

    Logs::create([
    'action' => 'Create',
    'description' => 'Registered student "' . $request->sname . '" with ID: ' . $request->student_id,
    'user' => auth()->user()->name ?? 'System',
    'date_time' => now('Asia/Manila'),
    'type' => 'User',
]);

    return redirect()->back()->with('success', 'Student registered successfully!');
}

public function storeOne(Request $request)
{
    $request->validate([
        'id_number' => 'required|unique:students,id_number',
        'name' => 'required|string',
        'gender' => 'required|string',
        'course' => 'required|string',
        'year' => 'required|string',
        'units' => 'required|numeric',
        'section' => 'required|string',
        'birth_date' => 'required|date',
        // optional fields
        'contact_no' => 'nullable|string',
        'address' => 'nullable|string',
    ]);

    Student::create([
        'id_number' => $request->id_number,
        'name' => $request->name,
        'gender' => $request->gender,
        'course' => $request->course,
        'year' => $request->year,
        'units' => $request->units,
        'section' => $request->section,
        'contact_no' => $request->contact_no,
        'birth_date' => $request->birth_date,
        'address' => $request->address,
        'status' => 'Unregistered', // default
    ]);

    return redirect()->back()->with('success', 'Student added successfully!');
}


    
    
}
