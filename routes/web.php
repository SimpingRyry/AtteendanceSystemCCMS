<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OrgListController;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\AccountsController;
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
Route::get('/', [HomeController::class, 'index']);

Route::get('/student', function () {
    return view('student');
});

Route::get('/template', function () {
    return view('schedTemplate');
});

Route::get('/accounts', [AccountsController::class, 'show']);

Route::get('/profile', function () {
    return view('profile');
});
Route::get('/device_page', function () {
    return view('device_page');
});

Route::get('/manage_orgs_page', function () {
    return view('manage_orgs_page');
});


Route::post('/orgs', [OrgListController::class, 'store'])->name('orgs.store');

Route::post('/student', [ScheduleController::class, 'generatePDF'])->name('generate.memo.pdf');
Route::post('/import', [ImportController::class, 'import'])->name('import');
Route::post('/registration', [StudentController::class, 'store'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');


Route::get('/student', [ImportController::class, 'show']);
Route::get('/registration', [ImportController::class, 'showUnregistered']);
Route::post('/account-update', [AccountsController::class, 'update'])->name('users.update');
