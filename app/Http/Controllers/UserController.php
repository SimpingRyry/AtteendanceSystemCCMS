<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getEligibleMembers()
{
    $currentTerm = Setting::where('key', 'academic_term')->value('value');

    // Step 1: Get all members
    $members = User::where('role', 'Member')->get();

    // Step 2: Extract all student_ids
    $studentIds = $members->pluck('student_id');

    // Step 3: Find users with same student_id who are officers this term
    $officersThisTerm = User::whereIn('student_id', $studentIds)
        ->where('role', 'like', '%Officer%')
        ->where('term', $currentTerm)
        ->pluck('student_id');

    // Step 4: Reject members who are also officers this term
    $eligibleMembers = $members->reject(function ($user) use ($officersThisTerm) {
        return $officersThisTerm->contains($user->student_id);
    });

    // Step 5: Return filtered data
    return response()->json(
        $eligibleMembers->values()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'picture' => $user->picture,
            ];
        })
    );
}

public function updatePassword(Request $request, $id)
{
    $request->validate([
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::findOrFail($id);
    $user->password = bcrypt($request->password);
    $user->save();

    return back()->with('success', 'Password updated successfully.');
}
}
