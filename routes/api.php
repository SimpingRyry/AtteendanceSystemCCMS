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

    $times = json_decode($event->times); // Expecting array of time strings
    $timeoutCount = count($times); // 2 = halfday, 4 = wholeday
    $isWholeDay = $timeoutCount === 4;

    $existing = Attendance::where('student_id', $student->id_number)
        ->where('event_id', $event->id)
        ->first();

    $timeFields = ['time_in1', 'time_out1', 'time_in2', 'time_out2'];
    $fieldToUpdate = null;

    if (!$existing) {
        $existing = Attendance::create([
            'student_id' => $student->id_number,
            'event_id' => $event->id,
            'date' => $today,
            'is_answered' => false
        ]);
    }

    // Initialize separate statuses
    $statusMorning = $existing->status_morning ?? null;
    $statusAfternoon = $existing->status_afternoon ?? null;

    foreach ($timeFields as $field) {
        if ($timeoutCount == 2 && in_array($field, ['time_in2', 'time_out2'])) continue;

        if (!$existing->$field) {
            // Check if it's the last timeout field
            $isLastTimeout = (
                ($timeoutCount == 2 && $field === 'time_out1') ||
                ($timeoutCount == 4 && $field === 'time_out2')
            );

            if ($isLastTimeout && !$existing->is_answered) {
                return response()->json([
                    'message' => 'You must answer the evaluation before checking out.'
                ], 403);
            }

            $existing->$field = $now->format('H:i');
            $fieldToUpdate = $field;

            // Determine reference time and check for lateness
            $index = $field === 'time_in1' ? 0 : ($field === 'time_in2' ? 2 : null);
            if ($index !== null && isset($times[$index])) {
                $refTime = Carbon::parse($times[$index])->addMinutes(30);
                $status = $now->gt($refTime) ? 'Late' : 'On Time';

                if ($isWholeDay) {
                    if ($field === 'time_in1') {
                        $statusMorning = $status;
                    } elseif ($field === 'time_in2') {
                        $statusAfternoon = $status;
                    }
                } else {
                    $existing->status = $status;
                }
            }

            break;
        }
    }

    // Save statuses
    if ($isWholeDay) {
        $existing->status_morning = $statusMorning;
        $existing->status_afternoon = $statusAfternoon;
    }

    $existing->save();

    return response()->json([
        'message' => 'Attendance recorded',
        'student' => $student->name,
        'time' => $now->toTimeString(),
        'status' => $isWholeDay ? [
            'morning' => $statusMorning,
            'afternoon' => $statusAfternoon
        ] : ($existing->status ?? 'On Time')
    ]);
});