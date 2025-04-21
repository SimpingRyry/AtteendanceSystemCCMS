<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::all(); // You can also use ->orderBy('date_time', 'desc') if you want newest first
        return view('logs', compact('logs'));
    }
}