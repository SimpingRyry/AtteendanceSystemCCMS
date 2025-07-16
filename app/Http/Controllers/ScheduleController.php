<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
    $startDate = \Carbon\Carbon::parse($request->input('start_date'));
    $endDate = \Carbon\Carbon::parse($request->input('end_date'));

    $students = Student::where('status', 'Unregistered')
        ->orderBy('name')
        ->get();

    $dateRange = [];
    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $dateRange[] = $date->copy();
    }

    $chunks = $students->chunk(10); // 10 students per day
    $pagedChunks = [];

    foreach ($dateRange as $index => $date) {
        $pagedChunks[] = [
            'date' => $date->format('F d, Y'),
            'students' => $chunks[$index] ?? collect(), // fallback to empty if not enough students
        ];
    }

    $pdf = Pdf::loadView('biometrics_schedule', [
        'title' => 'Biometric Registration Schedule',
        'pagedChunks' => $pagedChunks,
    ]);

    return $pdf->stream('biometrics_schedule.pdf');
}
}
