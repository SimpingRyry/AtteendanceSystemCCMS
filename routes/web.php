<?php

use App\Models\User;
use App\Models\OrgList;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OSSDController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MemberController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\OrgListController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GCashPaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrgDashboardController;
use App\Http\Controllers\OssdDashboardController;
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


Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('/login')->with('message', 'Email already verified.');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return redirect('/login')->with('message', 'Email verified successfully. Please log in.');
})->middleware(['signed'])->name('verification.verify');

Route::get('/', [HomeController::class, 'index']);

Route::get('/student', function () {
    return view('student');
});

Route::get('/Home', function () {
    return view('Home-Page');
});
Route::get('/dashboard_page', [OrgDashboardController::class, 'dashboard']);

Route::get('/student_dashboard', [DashboardController::class, 'studentindex']);

Route::get('/evaluation', function () {
    return view('evaluation');
});


Route::get('/super_dashboard', [DashboardController::class, 'index']);
Route::get('/ossd-dashboard', [OssdDashboardController::class, 'index']);

Route::get('/template', function () {
    return view('schedTemplate');
});
Route::get('/payment_page', function () {
    return view('payment_page');
});

Route::get('/notification', function () {
    return view('notification');
});
Route::get('/payment', [PaymentController::class, 'index'])->name('payments.index');


Route::get('/accounts', [AccountsController::class, 'showAdmins']);

Route::get('/officers', [OfficerController::class, 'showOfficerPage']);


Route::get('/profile', function () {
    return view('profile');
});
Route::post('/events', [EventsController::class, 'store'])->name('events.store');
Route::post('/events/update', [EventsController::class, 'update'])->name('events.update');
// ROUTES FOR STUDENT_EVALLLL


// Route to fetch all events from the database (GET request)
Route::get('/events', [EventsController::class, 'index'])->name('events.index');


Route::get('/api/events', [EventsController::class, 'fetchEvents'])->name('events.fetch');

use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\StudentEvaluationController;
use App\Http\Controllers\EvaluationResponseController;

// Route that receives the POST form when user enters payment amount
Route::post('/pay-with-gcash', [GCashPaymentController::class, 'createSource'])->name('gcash.pay');
// Optional: Return URLs after payment (from PayMongo)
Route::get('/payment-success', function () {
    return view('payment.success');
})->name('gcash.success');

Route::get('/payment-failed', function () {
    return view('payment.failed');
})->name('gcash.failed');

Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

Route::get('/device_page', [DeviceController::class, 'index']);
Route::get('/home_page', function () {
    return view('home_page');
});
Route::get('/student_payment', function () {
    return view('student_payment');
});
Route::get('/evaluation_student', function () {
    return view('evaluation_student');
});
Route::get('/reports', function () {
    return view('reports');
});
Route::get('/clearance', function () {
    return view('clearance');
});
Route::get('/attendance', [EventsController::class, 'CurrentEvent'])->name('attendance');

Route::get('/config', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/config/fines', [SettingsController::class, 'updateFines'])->name('settings.updateFines');
Route::post('/config/academic', [SettingsController::class, 'updateAcademic'])->name('settings.updateAcademicYear');

Route::get('/student', [StudentController::class, 'index']);
Route::get('/advisers', [AdviserController::class, 'index']);
Route::post('/advisers/store', [AdviserController::class, 'store'])->name('advisers.store');



Route::get('/manage_orgs_page', [App\Http\Controllers\OrgListController::class, 'index'])->name('orgs.index');


Route::get('/advisers', [AdviserController::class, 'index'])->name('advisers.index');

Route::get('/profile', [ProfileController::class, 'show'])->name('profile');



Route::post('/profile/{id}/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.uploadPhoto');

Route::resource('orgs', OrgListController::class);

Route::get('/reports/financial', [ReportController::class, 'exportFinancialReport'])->name('report.financial');

Route::get('/report/student-list', [ReportController::class, 'generateStudentListReport'])->name('report.student_list');
Route::get('/report/student-roster', [ReportController::class, 'generatePdf'])->name('report.studentRoster');



Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->middleware('auth');

Route::post('/record-attendance', [AttendanceController::class, 'store'])->name('record.attendance');



Route::get('/clearance', [ClearanceController::class, 'index'])->name('clearance.index');
Route::get('/clearance/{id}', [ClearanceController::class, 'generate'])->name('clearance.show');


Route::post('/evaluation/store', [EvaluationController::class, 'store'])->name('evaluation.store');
Route::get('/evaluations', fn() => \App\Models\Evaluation::all());
Route::delete('/evaluations/{evaluation}', [EvaluationController::class,'destroy']);
// existing POST /evaluation (store) already there

Route::resource('evaluation', App\Http\Controllers\EvaluationController::class);


Route::get('evaluation/{evaluation}/questions', [App\Http\Controllers\EvaluationController::class,'questions'])
     ->name('evaluation.questions');   // used by the modal’s AJAX call
Route::put('/evaluation/{evaluation}', [EvaluationController::class, 'update'])->name('evaluation.update');
Route::get('/student/evaluation/{id}/json', [EvaluationController::class, 'getEvaluationJson']);

Route::post('/student/evaluation/submit', [EvaluationController::class, 'submitAnswers']);

Route::post('/evaluation/send', [EvaluationController::class, 'send'])->name('evaluation.send');


Route::middleware(['auth'])->group(function () {
    // GET list of evaluations for students
    Route::get('/evaluation_student', [StudentEvaluationController::class, 'index'])
         ->name('student.evaluations');

    // AJAX – return evaluation + questions JSON

Route::get('/student/evaluation/{evaluationId}/{eventId}/json', [EvaluationController::class, 'getEvaluationWithQuestions']);

    Route::get('/evaluation_student/{evaluation}/json', [StudentEvaluationController::class,'json'])
         ->name('evaluation.json');

    // POST answers (from the modal’s form)
    Route::post('/evaluation_student/{evaluation}/answer', [StudentEvaluationController::class,'submitAnswers'])
         ->name('evaluation.submit');
});

Route::post('/advisers/store', [AdviserController::class, 'store'])->name('advisers.store');

Route::get('/evaluation_student', [StudentEvaluationController::class,'index']);


Route::post('/orgs', [OrgListController::class, 'store'])->name('orgs.store');

Route::post('/student', [ScheduleController::class, 'generatePDF'])->name('generate.memo.pdf');
Route::post('/import', [ImportController::class, 'import'])->name('import');
Route::post('/student', [StudentController::class, 'store'])->name('register');
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

Route::post('/generate-biometrics-schedule', [ScheduleController::class, 'generateBiometricsSchedule']);
Route::get('/officer-users', function () {
    $authUser = Auth::user();
    $currentTerm = Setting::where('key', 'academic_term')->value('value');

    // Get the current organization object
    $authOrg = OrgList::where('org_name', $authUser->org)->first();

    // Initialize an array of org names to query members from
    $orgNames = [];

    if ($authOrg) {
        // Always include current org
        $orgNames[] = $authOrg->org_name;

        // If it's a parent org, include children orgs
        if ($authOrg->children()->exists()) {
            $childOrgNames = $authOrg->children->pluck('org_name')->toArray();
            $orgNames = array_merge($orgNames, $childOrgNames);
        }
    }

    return User::where('role', 'Member')
        ->whereIn('org', $orgNames)
        
        ->get(['name', 'email', 'picture']);
});

Route::get('/org-members', [UserController::class, 'getEligibleMembers']);
Route::post('/notifications/mark-seen', [NotificationController::class, 'markAllAsSeen'])->name('notifications.markSeen');
Route::get('/public-events', [EventsController::class, 'publicFetchEvents'])->name('events.public.fetch');
Route::get('/student_payment', [PaymentController::class, 'showStatementOfAccount'])->name('student_payment')->middleware('auth');
Route::post('/officers/add', [OfficerController::class, 'add'])->name('officers.add');
Route::get('/admin/soa/{studentId}', [PaymentController::class, 'loadStudentSOA']);
Route::get('/attendance/live-data', [AttendanceController::class, 'liveData']);
Route::post('/students/preview', [ImportController::class, 'preview'])->name('students.preview');
Route::post('/students/confirm-import', [ImportController::class, 'confirmImport'])->name('students.confirmImport');
Route::get('/evaluation-responses', [EvaluationResponseController::class, 'index'])->name('evaluation.responses');
Route::get('/evaluation-responses/{evaluation}', [EvaluationResponseController::class, 'show'])->name('evaluation.responses.view');
Route::get('/evaluation-responses/{assignment}/summary', [EvaluationResponseController::class, 'summary'])->name('evaluation.responses.summary');
Route::post('/ossd/store', [OSSDController::class, 'store'])->name('ossd.store');
Route::get('/OSSD', [OSSDController::class, 'index'])->name('ossd.index');
Route::get('/members', [MemberController::class, 'showMembers'])->middleware('auth');
Route::post('/transactions/pay', [PaymentController::class, 'storePayment'])->name('transactions.pay');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/student/statement', [PaymentController::class, 'showStatementOfAccount'])
    ->name('student.statement'); // optional if route is protected

Route::post('/settings/delivery-units', [SettingsController::class, 'storeUnit'])->name('delivery-units.store');
Route::post('/settings/courses', [SettingsController::class, 'storeCourse'])->name('courses.store');
    Route::post('/officer-roles', [SettingsController::class, 'store'])->name('officer-roles.store');
    Route::delete('/officer-roles/{id}', [SettingsController::class, 'destroy'])->name('officer-roles.destroy');
Route::post('/orgs/set-hierarchy', [OrgListController::class, 'setHierarchy'])->name('orgs.setHierarchy');
Route::get('/officer-roles/{org_name}', function ($org_name) {
    return \App\Models\OfficerRole::where('org', $org_name)->pluck('title');
});

Route::get('/fine-history/search/{acadCode}', [SettingsController::class, 'searchFineHistory']);
Route::get('/attendance/{eventId}/{studentId}', [AttendanceController::class, 'getAttendance']);

 Route::get('/attendance/archive/ajax/{event_id}', [AttendanceController::class, 'fetchArchiveAjax'])->name('attendance.archive.ajax');
Route::post('/submit-excuse', [AttendanceController::class, 'submitExcuse'])->name('excuse.submit');
Route::get('/evaluation-answers/{evaluation}/{event}', [EvaluationController::class, 'getAnswers']);
Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::get('/gcash/success', [PaymentController::class, 'gcashSuccess'])->name('gcash.success');
Route::get('/gcash/failed', [PaymentController::class, 'gcashFailed'])->name('gcash.failed');
Route::get('/organization/{id}', [HomeController::class, 'show']);
Route::get('/events/{event}', [HomeController::class, 'showEvent'])->name('events.show');
Route::get('/homeevents', [HomeController::class, 'showHomeEvent'])->name('homeevents.show');
Route::put('/users/{id}/update-password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('events.destroy');

Route::post('/attendance/bulk-revert', [AttendanceController::class, 'bulkRevert'])->name('attendance.bulkRevert');
Route::post('/students/store', [StudentController::class, 'storeOne'])->name('students.store');
Route::get('/org/{orgId}/children', [OrgListController::class, 'getChildren']);
Route::get('/org/{orgName}/roles', [OrgListController::class, 'getRoles']);
Route::get('/students/{student}/unpaid-events', [PaymentController::class, 'getUnpaidEvents'])
     ->name('students.unpaidEvents');
Route::get('/filter-fines', [OrgDashboardController::class, 'filterFines'])->name('filter.fines');
     