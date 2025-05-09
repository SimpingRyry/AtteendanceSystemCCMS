<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

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