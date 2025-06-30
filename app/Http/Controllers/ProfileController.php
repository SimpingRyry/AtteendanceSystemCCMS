<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
 public function show()
{
    $user = auth()->user();

    // Get student details if exists (you can adjust this to match your relationship name)
    $student = $user->studentList;

    // Build profile data
    $profile = (object) [
        'name' => $user->name,
        'position' => $user->role,
        'email' => $user->email,
        'program' => $student->course ?? 'N/A',
        'address' => $student->address ?? 'N/A',
        'mobile' => $student->contact_no ?? 'N/A',
        'photo' => $user->picture ?? 'default.jpg',
    ];

    // Attendance breakdown logic
    $attendances = \App\Models\Attendance::where('student_id', $user->student_id)->get();
    Log::info('Attendance records fetched', ['count' => $attendances->count()]);

    $attended = 0;
    $late = 0;
    $absent = 0;

    foreach ($attendances as $attendance) {
        if (!is_null($attendance->status_morning) || !is_null($attendance->status_afternoon)) {
            // Whole day event
            $statuses = [$attendance->status_morning, $attendance->status_afternoon];
        } else {
            // Single session
            $statuses = [$attendance->status];
        }

        foreach ($statuses as $status) {
            if ($status === 'On Time' || $status === 'Attended') {
                $attended++;
            } elseif ($status === 'Late') {
                $late++;
            } elseif ($status === 'Absent' || $status === null) {
                $absent++;
            }
        }
    }

    return view('profile', compact('profile', 'attended', 'late', 'absent'));
}

public function update(Request $request)
{
    // Ensure you're working with a true Eloquent model
    $user = \App\Models\User::find(auth()->id());

    // Validate only the editable fields
    $request->validate([
        'mobile' => 'nullable|string|max:20',
        'password' => 'nullable|string|min:6',
    ]);

    // Only update password if it's provided
    if ($request->filled('password')) {
        $user->password = Hash::make($request->input('password'));
    }

    // Save user changes
    $user->save();

    // Update studentList only for mobile number
    if ($user->studentList) {
        $user->studentList->contact_no = $request->input('mobile');
        $user->studentList->save();
    }

    return back()->with('success', 'Profile updated successfully.');
}

}
