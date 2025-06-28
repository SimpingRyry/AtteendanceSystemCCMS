<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Starting user creation', ['request' => $request->all()]);
        // dd($request->all());
        // Adjust validation for organization depending on user role
        $request->validate([
            'sname' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'uploaded_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fingerprint' => 'nullable|int|max:255',
            'captured_image' => 'nullable|string',
            'organization' => (Str::lower(Auth::user()->role) === 'adviser' || Str::contains(Str::lower(Auth::user()->role), 'officer')) ? 'nullable' : 'required|string|max:255',

            'role' => 'required|string|max:255',
            'fingerprint_user_id' => 'nullable|integer',

        ]);
    
        if (!$request->uploaded_picture && !$request->captured_image) {
            return back()->withErrors(['image' => 'Please upload or capture a picture.']);
        }
    
        if ($request->uploaded_picture && $request->captured_image) {
            return back()->withErrors(['image' => 'Only one image should be provided.']);
        }
    
        $filename = null;
    
        // Save uploaded picture to public/uploads
        if ($request->hasFile('uploaded_picture')) {
            $image = $request->file('uploaded_picture');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $filename);
        }
    
        // Save captured picture (Base64) to public/uploads
        if ($request->captured_image) {
            $image = $request->captured_image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
    
            $filename = uniqid() . '.png';
            file_put_contents(public_path('uploads/' . $filename), $imageData);
        }
    
        // Save to users table
        $user = new User();
        $user->name = $request->sname;
        $user->email = $request->email;
        $user->password = Hash::make($request->student_id);
        $user->picture = $filename;
        $user->role = $request->role;
        $user->student_id = $request->student_id;
        // Set organization based on user role
        $role = Str::lower(Auth::user()->role);
        Log::info('Determined role', ['role' => $role]);
        $user->org = ($role === 'adviser' || Str::contains($role, 'officer')) ? Auth::user()->org : $request->organization;

          Log::info('Final user data before save', $user->toArray());

     
        $user->save();

        event(new Registered($user));
    
        // Update student record
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
    
        return redirect()->back()->with('success', 'Student registered successfully!');
    }
    
    
}
