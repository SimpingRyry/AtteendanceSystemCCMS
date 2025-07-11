<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Device;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DeviceController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/device/name/{name}/mute', [DeviceController::class, 'toggleMuteByName']);
Route::get('/device/name/{name}/mute', [DeviceController::class, 'getMuteStatusByName']);



Route::post('/device-auth', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'password' => 'required|string',
    ]);

    // Check device in DB or any array
    $device = \App\Models\Device::where('name', $validated['name'])->first();

    if ($device) {
        if ($device->password === $validated['password']) {
            $device->is_online = true;
            $device->last_seen = now();
            $device->save();

            return response()->json(['message' => 'Device authenticated.'], 200);
        } else {
            return response()->json(['message' => 'Wrong password.'], 403);
        }
    } else {
        // Auto-register if not exist
        \App\Models\Device::create([
            'name' => $validated['name'],
            'password' => $validated['password'],
            'is_online' => true,
        ]);

        return response()->json(['message' => 'Device registered and online.'], 200);
    }
});

Route::post('/register', function (Request $request) {
    $id = $request->input('id');
    Cache::put('latest_fingerprint_id', $id, now()->addMinutes(5)); // store temporarily
    return response()->json(['message' => $id]);
});

Route::get('/register', function () {
    // Retrieve the latest fingerprint ID from the cache
    $latestFingerprintId = Cache::get('latest_fingerprint_id');

    if ($latestFingerprintId) {
        return response()->json(['id' => $latestFingerprintId]);
    } else {
        return response()->json(['error' => 'ID not found'], 404);
    }
});

Route::post('/fingerprint-upload', function (Request $request) {
    Log::info('Fingerprint upload called');
    Log::info('Request Data:', $request->all());
    Log::info('Uploaded Files:', $request->allFiles());

    $request->validate([
        'user_id' => 'required|integer',
        'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $id = $request->input('user_id');
    $image = $request->file('image');

    // Create a filename
    $filename = 'user_' . $id . '_' . time() . '.' . $image->getClientOriginalExtension();
    $relativePath = 'fingerprints/' . $filename;
    $publicPath = public_path('fingerprints');

    // Ensure the directory exists
    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0755, true);
    }

    // Move file to public/fingerprints
    $image->move($publicPath, $filename);

    // Create URL to the file
    $url = url('fingerprints/' . $filename);

    // Store temporarily in cache
    Cache::put('latest_fingerprint_data', [
        'user_id' => $id,
        'url' => $url
    ], now()->addMinutes(5));

    Log::info('Cached fingerprint image URL:', ['url' => $url]);

    return response()->json(['message' => 'Fingerprint received', 'url' => $url]);
});


Route::get('/fingerprint/latest', function () {
    $data = Cache::get('latest_fingerprint_data');

    if (!$data) {
        return response()->json(['message' => 'No fingerprint data found in cache'], 404);
    }

    Log::info('Cached fingerprint data retrieved:', $data);

    return response()->json([
        'user_id' => $data['user_id'],
        'url' => $data['url']
    ]);
});
Route::post('/scan', function (Request $request) {
    $finger_id = $request->input('scanned_id');

    $student = Student::where('f_id', $finger_id)->first();
    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $user = User::where('student_id', $student->id_number)->first();
    $image = $user?->picture ? asset('uploads/' . $user->picture) : null;

    $now = Carbon::now('Asia/Manila');
    $today = $now->format('Y-m-d');

    $event = Event::whereDate('event_date', $today)->first();
    if (!$event) {
        return response()->json(['message' => 'No event today'], 404);
    }

    $times = json_decode($event->times);
    $timeoutCount = count($times);
    $isWholeDay = $timeoutCount === 4;

    $existing = Attendance::firstOrCreate([
        'student_id' => $student->id_number,
        'event_id' => $event->id,
    ], [
        'date' => $today,
        'is_answered' => false
    ]);

    $statusMorning = $existing->status_morning ?? null;
    $statusAfternoon = $existing->status_afternoon ?? null;

    $session = null;

    if ($isWholeDay) {
        if (is_null($existing->time_in1) || is_null($existing->time_out1)) {
            $session = 'morning';
        } elseif (is_null($existing->time_in2) || is_null($existing->time_out2)) {
            $session = 'afternoon';
        } else {
            return response()->json([
                'message' => 'Attendance already complete for the day.',
                'student' => $student->name,
                'image' => $image
            ], 200);
        }
    } else {
        $session = 'halfday';
    }

    $fieldMap = [
        'morning' => ['time_in1', 'time_out1', 0],
        'afternoon' => ['time_in2', 'time_out2', 2],
        'halfday' => ['time_in1', 'time_out1', 0],
    ];
    [$timeInField, $timeOutField, $timeIndex] = $fieldMap[$session];

    // ✅ Time-In Logic
    if (is_null($existing->$timeInField)) {
        $refTime = isset($times[$timeIndex]) ? Carbon::parse($times[$timeIndex], 'Asia/Manila') : null;
        $onTimeCutoff = $refTime?->copy()->addMinutes(5); // 5-minute "On Time" window

        if ($refTime && $onTimeCutoff) {
            $existing->$timeInField = $now->format('H:i');

            $status = $now->between($refTime, $onTimeCutoff) ? 'On Time' : 'Late';

            if ($isWholeDay) {
                if ($session === 'morning') $statusMorning = $status;
                if ($session === 'afternoon') $statusAfternoon = $status;
            } else {
                $existing->status = $status;
            }

            $existing->status_morning = $statusMorning;
            $existing->status_afternoon = $statusAfternoon;
            $existing->save();

            return response()->json([
                'message' => 'Time-in recorded',
                'time' => $now->toTimeString(),
                'status' => $status,
                'student' => $student->name,
                'image' => $image
            ]);
        }
    }

    // ✅ Time-Out Logic
    if (is_null($existing->$timeOutField)) {
        if (!$existing->is_answered && $timeOutField === ($isWholeDay ? 'time_out2' : 'time_out1')) {
            return response()->json([
                'message' => 'You must answer the evaluation before checking out.',
                'student' => $student->name,
                'image' => $image
            ], 403);
        }

        if (is_null($existing->$timeInField)) {
            $existing->$timeInField = $now->format('H:i');
        }

        $existing->$timeOutField = $now->format('H:i');

        if ($isWholeDay) {
            if ($session === 'morning' && $statusMorning === null) $statusMorning = 'Late';
            if ($session === 'afternoon' && $statusAfternoon === null) $statusAfternoon = 'Late';
        } else {
            if ($existing->status === null) $existing->status = 'Late';
        }

        $existing->status_morning = $statusMorning;
        $existing->status_afternoon = $statusAfternoon;
        $existing->save();

        return response()->json([
            'message' => 'Time-out recorded',
            'time' => $now->toTimeString(),
            'status' => $isWholeDay ? [
                'morning' => $statusMorning,
                'afternoon' => $statusAfternoon
            ] : ($existing->status ?? 'Late'),
            'student' => $student->name,
            'image' => $image
        ]);
    }

    // ✅ Already Recorded
    return response()->json([
        'message' => 'Already recorded for this session.',
        'student' => $student->name,
        'image' => $image
    ]);
});
Route::get('/event', function () {
    $today = Carbon::now('Asia/Manila')->toDateString();

    $event = Event::whereDate('event_date', $today)->first();

    return response()->json([
        'event_name' => $event?->name ?? 'Untitled Event'
    ]);
});

Route::post('/update', function (Request $request) {
    $device = Device::find($request->device_id);  // 🔄 Use device_id from request

    if ($device) {
        $device->name = $request->device_name;
        $device->password = $request->device_password;
        $device->clock_format = $request->clock_format;
        $device->save();

        return response()->json(['message' => 'Settings updated.']);
    }

    return response()->json(['message' => 'Device not found.'], 404);
});

Route::get('/device-settings/{device_id}', function ($device_id) {
    $device = \App\Models\Device::find($device_id);

    if ($device) {
        return response()->json([
            'device_name' => $device->name,
            'device_password' => $device->password,
            'clock_format' => $device->clock_format,
        ]);
    }

    return response()->json(['message' => 'Device not found.'], 404);
});

Route::post('/check-role', function (Request $request) {
    $student = Student::where('f_id', $request->finger_id)->first();

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $users = User::where('student_id', $student->id_number)
                 ->whereNotNull('term')  // skip "Member" role (no term)
                 ->get();

    if ($users->isEmpty()) {
        return response()->json(['message' => 'No valid roles found'], 404);
    }

    $currentTerm = Setting::where('key', 'academic_term')->value('value');

    // Find valid officer for current term
    $validUser = $users->first(function ($user) use ($currentTerm) {
        return str_ends_with($user->role, 'Officer') && $user->term === $currentTerm;
    });

    if (!$validUser) {
        return response()->json(['message' => 'No officer role found for current term'], 403);
    }

    return response()->json([
        'role' => $validUser->role,
        'term' => 'current',
    ]);
});