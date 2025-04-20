<?php

namespace App\Http\Controllers;

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
}
