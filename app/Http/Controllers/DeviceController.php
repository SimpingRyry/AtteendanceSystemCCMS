<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
{
    $devices = \App\Models\Device::all(); // Or use pagination/filtering if needed
      foreach ($devices as $device) {
        if ($device->last_seen && Carbon::parse($device->last_seen)->diffInSeconds(now()) > 10) {
            $device->is_online = false;
            $device->save();
        }
    }
    return view('device_page', compact('devices'));
}
}
