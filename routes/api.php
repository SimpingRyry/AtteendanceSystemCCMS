<?php

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


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

    $timeFields = ['time_in1', 'time_out1', 'time_in2', 'time_out2'];
    $statusMorning = $existing->status_morning ?? null;
    $statusAfternoon = $existing->status_afternoon ?? null;

    // Determine current session (morning or afternoon)
    $session = null;

    if ($isWholeDay) {
        $timeout1Grace = isset($times[1]) ? Carbon::parse($times[1])->addMinutes(30) : null;
        $timeout2Grace = isset($times[3]) ? Carbon::parse($times[3])->addMinutes(30) : null;

        // If morning missed and it's already past timeout1 + grace, mark morning absent
        if (is_null($existing->time_in1) && is_null($existing->time_out1) && $timeout1Grace && $now->gt($timeout1Grace)) {
            $existing->time_in1 = null;
            $existing->time_out1 = null;
            $statusMorning = 'Absent';
        }

        // If afternoon missed and it's past timeout2 + grace, mark afternoon absent and prevent scan
        if (is_null($existing->time_in2) && is_null($existing->time_out2) && $timeout2Grace && $now->gt($timeout2Grace)) {
            $existing->time_in2 = null;
            $existing->time_out2 = null;
            $statusAfternoon = 'Absent';
            $existing->status_morning = $statusMorning;
            $existing->status_afternoon = $statusAfternoon;
            $existing->save();

            return response()->json([
                'message' => 'You missed both time-in and time-out for both sessions.',
                'status' => 'Absent'
            ], 403);
        }

        // Decide which session to process
        if (is_null($existing->time_in1) || is_null($existing->time_out1)) {
            $session = 'morning';
        } elseif (is_null($existing->time_in2) || is_null($existing->time_out2)) {
            $session = 'afternoon';
        } else {
            return response()->json([
                'message' => 'Attendance already complete for the day.'
            ], 200);
        }
    } else {
        $session = 'halfday';
    }

    // Select relevant fields and time index based on session
    $fieldMap = [
        'morning' => ['time_in1', 'time_out1', 0],
        'afternoon' => ['time_in2', 'time_out2', 2],
        'halfday' => ['time_in1', 'time_out1', 0],
    ];
    [$timeInField, $timeOutField, $timeIndex] = $fieldMap[$session];

    // Handle scan for time in
    if (is_null($existing->$timeInField)) {
        $refTime = isset($times[$timeIndex]) ? Carbon::parse($times[$timeIndex]) : null;
        $graceTime = $refTime?->copy()->addMinutes(30);

        if ($now->gt($graceTime)) {
            return response()->json([
                'message' => 'You are late. Please wait until time-out.',
                'status' => 'Too Late'
            ], 403);
        }

        $existing->$timeInField = $now->format('H:i');
        $status = $now->gt($refTime) ? 'Late' : 'On Time';

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
            'status' => $status
        ]);
    }

    // Handle scan for time out
    if (is_null($existing->$timeOutField)) {
        if (!$existing->is_answered && $timeOutField === ($isWholeDay ? 'time_out2' : 'time_out1')) {
            return response()->json([
                'message' => 'You must answer the evaluation before checking out.'
            ], 403);
        }

        // If time in is missing, auto-fill both with late
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
            ] : ($existing->status ?? 'Late')
        ]);
    }

    return response()->json([
        'message' => 'Already recorded for this session.'
    ]);
});
