<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/student', function () {
    return view('student');
});

Route::get('/template', function () {
    return view('schedTemplate');
});
Route::get('/profile', function () {
    return view('profile');
});
Route::post('/student', [ScheduleController::class, 'generatePDF'])->name('generate.memo.pdf');
Route::post('/import', [ImportController::class, 'import'])->name('import');
Route::post('/registration', [StudentController::class, 'store'])->name('register');


Route::get('/student', [ImportController::class, 'show']);
Route::get('/registration', [ImportController::class, 'showUnregistered']);