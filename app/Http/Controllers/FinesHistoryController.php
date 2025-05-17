<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FineHistory;

class FinesHistoryController extends Controller
{
    //


public function showSettings()
{
    $fines = Fine::first(); // Current fines
    $settings = Setting::first(); // Academic settings
    $history = FineHistory::latest('changed_at')->take(10)->get(); // Last 10 changes

    return view('settings.config', compact('fines', 'settings', 'history'));
}

}
