<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function showMembers()
    {
        $user = Auth::user();

        $query = User::where('role', 'Member');

        // Filter by organization if the logged-in user has an org
        if ($user->org !== null) {
            $query->where('org', $user->org);
        }

        $users = $query->get();

        return view('members', compact('users'));
    }
}
