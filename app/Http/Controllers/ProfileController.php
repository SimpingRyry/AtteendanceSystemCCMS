<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB; 

class ProfileController extends Controller
{
    public function show()
    {
        $profile = UserProfile::first(); // or use `auth()->user()->id` to get dynamic profile
        return view('profile', compact('profile'));
    }
}
