<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\OrgListController;
use App\Http\Controllers\ProfileController;
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
Route::get('/dashboard_page', function () {
    return view('dashboard_page');
});
Route::get('/template', function () {
    return view('schedTemplate');
});
Route::get('/payment_page', function () {
    return view('payment_page');
});

Route::get('/accounts', [AccountsController::class, 'show']);

Route::get('/profile', function () {
    return view('profile');
});
Route::post('/events', [EventsController::class, 'store'])->name('events.store');

// Route to fetch all events from the database (GET request)
Route::get('/events', [EventsController::class, 'index']);


Route::get('/api/events', [EventsController::class, 'fetchEvents'])->name('events.fetch');



Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

Route::get('/device_page', function () {
    return view('device_page');
});


Route::get('/student', [StudentController::class, 'index']);

Route::get('/manage_orgs_page', [App\Http\Controllers\OrgListController::class, 'index'])->name('orgs.index');


Route::get('/advisers', [AdviserController::class, 'index'])->name('advisers.index');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/{id}/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.uploadPhoto');

Route::resource('orgs', OrgListController::class);

Route::post('/orgs', [OrgListController::class, 'store'])->name('orgs.store');

Route::post('/student', [ScheduleController::class, 'generatePDF'])->name('generate.memo.pdf');
Route::post('/import', [ImportController::class, 'import'])->name('import');
Route::post('/registration', [StudentController::class, 'store'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [HomeController::class, 'showlogin'])->name('login');

Route::get('/registration', [ImportController::class, 'showUnregistered']);
Route::post('/account-update', [AccountsController::class, 'update'])->name('users.update');
Route::get('/student', [ImportController::class, 'show']);
