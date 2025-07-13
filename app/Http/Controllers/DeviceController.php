<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Device;
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

public function toggleMuteByName(Request $request, $name)
{
    $device = Device::where('name', $name)->firstOrFail();
    $device->is_muted = $request->input('is_muted');
    $device->save();

    return response()->json(['success' => true, 'is_muted' => $device->is_muted]);
}

public function getMuteStatusByName($name)
{
    $device = Device::where('name', $name)->firstOrFail();
    return response()->json(['is_muted' => $device->is_muted]);
}
public function updateEnrollmentStatus(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'enrollment_on' => 'required|boolean',
        ]);

        $device = Device::find($validated['device_id']);
        $device->enrollment_on = $validated['enrollment_on'];
        $device->save();

        return response()->json([
            'message' => 'Enrollment status updated successfully.',
            'device_id' => $device->id,
            'enrollment_on' => $device->enrollment_on,
        ]);
    }
}
