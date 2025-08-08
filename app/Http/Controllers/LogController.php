<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class LogController extends Controller
{
public function index()
{
    $userLogs = Logs::where('type', 'User')->orderBy('date_time', 'desc')->get();
    $eventLogs = Logs::where('type', 'Event')->orderBy('date_time', 'desc')->get();
    $paymentLogs = Logs::where('type', 'Payment')->orderBy('date_time', 'desc')->get();
    $otherLogs = Logs::whereNotIn('type', ['User', 'Event', 'Payment'])->orderBy('date_time', 'desc')->get();

    return view('logs', compact('userLogs', 'eventLogs', 'paymentLogs', 'otherLogs'));

}

}