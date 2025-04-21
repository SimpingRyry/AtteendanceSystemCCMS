<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adviser;

class AdviserController extends Controller
{
    public function index()
    {
        $advisers = Adviser::all(); // Fetch all advisers

        return view('advisers', compact('advisers')); // Make sure this file exists: resources/views/advisers.blade.php
    }
}
