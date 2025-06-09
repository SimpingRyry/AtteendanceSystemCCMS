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

    $existing = Attendance::where('student_id', $student->id_number)
                ->where('event_id', $event->id)
                ->first();

    // Determine time slot to update (time_in1, time_out1, time_in2, time_out2)
    $timeFields = ['time_in1', 'time_out1', 'time_in2', 'time_out2'];
    $fieldToUpdate = null;

    if (!$existing) {
        $existing = Attendance::create([
            'student_id' => $student->id_number,
            'event_id' => $event->id,
            'date' => $today
        ]);
    }

    foreach ($timeFields as $field) {
        if ($timeoutCount == 2 && in_array($field, ['time_in2', 'time_out2'])) continue; // Skip PM fields if halfday

        if (!$existing->$field) {
            $existing->$field = $now->format('H:i');
            break;
        }
    }

    // Determine status based on grace period (15 + 15 mins)
    $status = 'On Time';
    if ($fieldToUpdate === 'time_in1' || $fieldToUpdate === 'time_in2') {
        $index = $fieldToUpdate === 'time_in1' ? 0 : 2;
        $refTime = Carbon::parse($times[$index])->addMinutes(30); // 15 mins + grace 15
        if ($now->gt($refTime)) {
            $status = 'Late';
        }
    }

    $existing->status = $status;
    $existing->save();

    return response()->json([
        'message' => 'Attendance recorded',
        'student' => $student->name,
        'time' => $now->toTimeString(),
        'status' => $status
    ]);
});