<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\FineSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $attendanceData = json_decode($request->input('attendance_data'), true);
    
        $setting = Setting::where('key', 'academic_term')->first();
        $acadTerm = $setting->value ?? 'Unknown Term';
        $acadCode = $setting->acad_code ?? 'Unknown Code';
    
        foreach ($attendanceData as $record) {
            $status = strtolower($record['status']);
    
            if ($status === 'late' || $status === 'absent') {
                // Get org that facilitated the event today
                $eventOrg = Event::where('name', $record['event'])
                    ->whereDate('event_date', now()->toDateString())
                    ->value('org');
    
                if (!$eventOrg) {
                    continue; // Skip if no event with today's date and that name
                }
    
                // Get fine settings for that org
                $fineSettings = FineSetting::where('org', $eventOrg)->first();
                if (!$fineSettings) {
                    continue; // Skip if no fine setting for this org
                }
    
                $studentOrg = User::where('student_id', $record['student_id'])->value('org');
                $fineAmount = $status === 'late' ? $fineSettings->late_member : $fineSettings->absent_member;
    
                Transaction::create([
                    'student_id'       => $record['student_id'],
                    'event'            => $record['event'],
                    'transaction_type' => 'FINE',
                    'fine_amount'      => $fineAmount,
                    'org'              => $studentOrg ?? 'N/A',
                    'date'             => $record['date'],
                    'acad_term'        => $acadTerm,
                    'acad_code'        => $acadCode,
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Attendance fines recorded successfully.');
    }
    
}
