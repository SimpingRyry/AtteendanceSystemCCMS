<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        // Adjust validation for organization depending on user role
        $request->validate([
            'sname' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'uploaded_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fingerprint' => 'nullable|int|max:255',
            'captured_image' => 'nullable|string',
            'organization' => Auth::user()->role === 'admin' ? 'nullable' : 'required|string|max:255',
            'role' => 'required|string|max:255',
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
    
        // Set organization based on user role
        $user->org = (Auth::user()->role === 'admin') ? Auth::user()->org : $request->organization;
    
        $user->save();
    
        // Update student record
        $student = Student::where('id_number', $request->student_id)->first();
    
        if ($student) {
            if ($request->filled('fingerprint')) {
                $student->f_id = $request->fingerprint;
            }
    
            if ($student->status == 'Unregistered') {
                $student->status = 'Registered';
            }
    
            $student->save();
        } else {
            return redirect()->back()->with('error', 'Student record not found.');
        }
    
        return redirect()->back()->with('success', 'Student registered successfully!');
    }
    
    
}
