<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


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
