<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrgList;
use Illuminate\Support\Facades\Auth;

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
    $startDate = Carbon::parse($request->input('start_date'));
    $endDate = Carbon::parse($request->input('end_date'));

    // Retrieve only unregistered students
    $students = Student::where('status', 'Unregistered')
        ->orderBy('name')
        ->get();

    // Chunk students into groups of 10 (10 per day)
    $chunks = $students->chunk(10);

    // Limit the date range based on number of student chunks
    $dateRange = [];
    $maxDaysNeeded = $chunks->count();

    $currentDate = $startDate->copy();
    while ($currentDate->lte($endDate) && count($dateRange) < $maxDaysNeeded) {
        $dateRange[] = $currentDate->copy();
        $currentDate->addDay();
    }

    // Build paged chunks for the view
    $pagedChunks = [];
    foreach ($dateRange as $index => $date) {
        $pagedChunks[] = [
            'date' => $date->format('F d, Y'),
            'students' => $chunks[$index] ?? collect(),
        ];
    }

    // ✅ Get authenticated user org and fetch the logo from OrgList
    $authUser = Auth::user();
    $orgList = OrgList::where('org_name', $authUser->org)->first();
    $orgLogo = $orgList?->org_logo ?? 'default-logo.png';

    // Generate PDF with passed data
    $pdf = Pdf::loadView('biometrics_schedule', [
        'title' => 'Biometric Registration Schedule',
        'pagedChunks' => $pagedChunks,
        'orgLogo' => $orgLogo,
    ]);

    return $pdf->stream('biometrics_schedule.pdf');
}
}
