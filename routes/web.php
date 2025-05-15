<?php

use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;

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

Route::get('/student_dashboard', function () {
    return view('student_dashboard');
});


Route::get('/super_dashboard', function () {
    return view('super_dashboard');
});
Route::get('/template', function () {
    return view('schedTemplate');
});
Route::get('/payment_page', function () {
    return view('payment_page');
});
Route::get('/payment', function () {
    return view('payment_page2');
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

Route::get('/reports', function () {
    return view('reports');
});
Route::get('/config', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/config/fines', [SettingsController::class, 'updateFines'])->name('settings.updateFines');
Route::post('/config/academic', [SettingsController::class, 'updateAcademic'])->name('settings.updateAcademicYear');
Route::get('/student', [StudentController::class, 'index']);

Route::get('/manage_orgs_page', [App\Http\Controllers\OrgListController::class, 'index'])->name('orgs.index');


Route::get('/advisers', [AdviserController::class, 'index'])->name('advisers.index');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/{id}/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.uploadPhoto');

Route::resource('orgs', OrgListController::class);

Route::get('/report/financial', [ReportController::class, 'generateFinancialReport'])->name('report.financial');

Route::get('/report/student-list', [ReportController::class, 'generateStudentListReport'])->name('report.student_list');
Route::get('/report/student-roster', [ReportController::class, 'generateStudentRoster'])->name('report.studentRoster');




Route::post('/orgs', [OrgListController::class, 'store'])->name('orgs.store');

Route::post('/student', [ScheduleController::class, 'generatePDF'])->name('generate.memo.pdf');
Route::post('/import', [ImportController::class, 'import'])->name('import');
Route::post('/registration', [StudentController::class, 'store'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [HomeController::class, 'showlogin'])->name('login');

Route::get('/registration', [ImportController::class, 'showUnregistered']);
Route::post('/account-update', [AccountsController::class, 'update'])->name('users.update');
Route::get('/student', [ImportController::class, 'show']);
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');