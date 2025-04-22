<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class StudentController extends Controller
{
    public function store(Request $request)
    {
        // Validate inputs
        $request->validate([
            'sname' => 'required|string|max:255',
            'student_id' => 'required|string|max:255',
            'course' => 'required|string|max:255',

            'email' => 'required|email|max:255',
            'uploaded_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fingerprint' => 'required|int|max:255',

            'captured_picture' => 'nullable|string', // 
        ]);

        if (!$request->uploaded_picture && !$request->captured_picture) {
            return back()->withErrors(['image' => 'Please upload or capture a picture.']);
        }
        if ($request->uploaded_picture && $request->captured_picture) {
            return back()->withErrors(['image' => 'Only one image should be provided.']);
        }

    
        $imageData = null;

        // Save uploaded picture (convert to BLOB)
        if ($request->hasFile('uploaded_picture')) {
            $image = $request->file('uploaded_picture');
            $imageData = file_get_contents($image);
        }

        // Save captured picture (Base64 to BLOB)
        if ($request->captured_picture) {
            $image = $request->captured_picture;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
        }

        // Insert into users table
        $user = new User();
        $user->name = $request->sname;
        $user->email = $request->email;
        $user->password = $request->student_id; // always hash passwords!
        $user->picture = $imageData;
        $user->role = "member";


        $user->save();

        

       
       

        $student = Student::where('id_number', $request->student_id)->first();

        if ($student) {
            // Update the fingerprint and status columns
            if ($request->filled('fingerprint')) {
                $student->f_id = $request->fingerprint;  // Update fingerprint
            }
    
            if ($student->status == 'Unregistered') {
                $student->status = 'Registered';  // Change status to 'registered'
            }
    
            // Save changes to the student
            $student->save();
        } else {
            // Optional: what to do if no existing student is found
            return redirect()->back()->with('error', 'Student record not found.');
        }
    
        return redirect()->back()->with('success', 'Student registered successfully!');
    }
}
