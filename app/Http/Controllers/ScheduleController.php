<?php

namespace App\Http\Controllers;

use illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\BiometricScheduleMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ScheduleController extends Controller
{
    
    public function generatePDF(Request $request)
        {

            set_time_limit(120);

            $course = $request->input('course');
            $block = $request->input('block');
            $year = $request->input('year');
            $venue = $request->input('venue');
            $date = $request->input('date');
            $start_time = $request->input('starttime');
            $end_time = $request->input('endtime');
            $logo = public_path('images/org_ccms_logo.png'); // Path to your logo
    
            // Pass each variable separately to the Blade view
            $pdf = Pdf::loadView('schedTemplate', compact(
                'course',
                'block',
                'year',
                'venue',
                'date',
                'start_time',
                'end_time',
                'logo'
            ))->setPaper('A4', 'portrait');

        // Return the generated PDF with streaming (inline display)
        return $pdf->download('Schedule.pdf'); 
    }
public function generateBiometricsSchedule(Request $request)
{
    // ✅ Parse start and end dates
    $startDate = Carbon::parse($request->input('start_date'));
    $endDate = Carbon::parse($request->input('end_date'));

    // ✅ Retrieve unregistered students and clean names
    $students = Student::where('status', 'Unregistered')
        ->orderBy('name')
        ->get()
        ->map(function ($student) {
            $student->name = ltrim($student->name, '*');
            $student->name = trim($student->name);
            return $student;
        });

    // ✅ Group students (10 per day)
    $chunks = $students->chunk(10);

    // ✅ Build date range based on chunk count
    $dateRange = [];
    $maxDaysNeeded = $chunks->count();
    $currentDate = $startDate->copy();

    while ($currentDate->lte($endDate) && count($dateRange) < $maxDaysNeeded) {
        $dateRange[] = $currentDate->copy();
        $currentDate->addDay();
    }

    // ✅ Get authenticated user + organization info
    $authUser = Auth::user();
    $orgList = OrgList::where('org_name', $authUser->org)->first();
    $orgLogo = $orgList?->org_logo ?? 'default-logo.png';
    $orgName = $orgList?->org_name ?? 'Organization Name';

    // ✅ Get current academic term
    $academicTermRecord = Setting::where('key', 'academic_term')->first();
    $activeTerm = $academicTermRecord?->value ?? null;

    $activeYearRange = null;
    if ($activeTerm) {
        preg_match('/\b(\d{4}-\d{4})\b/', $activeTerm, $matches);
        $activeYearRange = $matches[1] ?? null;
    }

    // ✅ Find current President based on matching term
    $president = null;
    if ($activeYearRange) {
        $president = User::where('org', $authUser->org)
            ->where('role', 'President - Officer')
            ->get()
            ->first(function ($user) use ($activeYearRange) {
                preg_match('/\b(\d{4}-\d{4})\b/', $user->term, $m);
                return isset($m[1]) && $m[1] === $activeYearRange;
            });
    }

    // ✅ Build paged chunks for the view and send emails
    $pagedChunks = [];

    foreach ($dateRange as $index => $date) {
        $studentsForTheDay = $chunks[$index] ?? collect();

        $pagedChunks[] = [
            'date' => $date->format('F d, Y'),
            'students' => $studentsForTheDay,
        ];

        // ✅ Send custom email notification for each student
        foreach ($studentsForTheDay as $student) {
            if (!empty($student->email)) {
                try {
                    Mail::to($student->email)->send(
                        new BiometricScheduleMail($student, $date, $orgName)
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send biometric schedule email to {$student->email}: " . $e->getMessage());
                }
            }
        }
    }

    // ✅ Generate PDF with schedule summary
    $pdf = Pdf::loadView('biometrics_schedule', [
        'title' => 'Biometric Registration Schedule',
        'pagedChunks' => $pagedChunks,
        'orgLogo' => $orgLogo,
        'orgName' => $orgName,
        'president' => $president,
    ]);

    return $pdf->stream('biometrics_schedule.pdf');
}

}
