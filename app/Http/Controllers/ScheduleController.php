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

            $date = $request->input('date', now()->format('F d, Y'));

            $students = Student::where('status', 'Unregistered')
                ->orderBy('name')
                ->get();
        
            $chunks = $students->chunk(45);
        
            $pdf = Pdf::loadView('biometrics_schedule', [
                'title' => 'Biometric Registration Schedule',
                'scheduleDate' => \Carbon\Carbon::parse($date)->format('F d, Y'),
                'batch' => null, // No batch input — just pass null
                'chunks' => $chunks
            ]);
        
            return $pdf->download('biometrics_schedule.pdf');
    }
}
