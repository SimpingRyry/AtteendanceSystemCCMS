<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Attendance;
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

    $existingIds = [];

    foreach ($attendanceData as $record) {
        $existingIds[] = $record['student_id'];
        $hasSeparateSessions = isset($record['status_morning']) && isset($record['status_afternoon']);

        // Get event org for today
        $eventOrg = Event::where('name', $record['event'])
            ->whereDate('event_date', now()->toDateString())
            ->value('org');

        if (!$eventOrg) continue;

        // Fine settings
        $fineSettings = FineSetting::where('org', $eventOrg)->first();
        if (!$fineSettings) continue;

        // Officer check
        $officer = User::where('student_id', $record['student_id'])
            ->where('term', $acadTerm)
            ->where('org', $eventOrg)
            ->where('role', 'like', '%- Officer')
            ->first();

        $isOfficer = $officer !== null;
        $studentOrg = $officer->org ?? User::where('student_id', $record['student_id'])->value('org');

        // Fine amount function
        $getFineAmount = function ($status) use ($fineSettings, $isOfficer) {
            return match ($status) {
                'Late' => $isOfficer ? $fineSettings->late_officer : $fineSettings->late_member,
                'Absent' => $isOfficer ? $fineSettings->absent_officer : $fineSettings->absent_member,
                default => 0,
            };
        };

        // Whole day session
        if ($hasSeparateSessions) {
            $statuses = [
                'morning' => strtolower($record['status_morning']),
                'afternoon' => strtolower($record['status_afternoon']),
            ];

            foreach ($statuses as $session => $status) {
                if ($status === 'late' || $status === 'absent') {
                    $fineAmount = $getFineAmount(ucfirst($status));

                    Transaction::create([
                        'student_id' => $record['student_id'],
                        'event' => $record['event'] . ' - ' . ucfirst($session),
                        'transaction_type' => 'FINE',
                        'fine_amount' => $fineAmount,
                        'org' => $studentOrg ?? 'N/A',
                        'date' => $record['date'],
                        'acad_term' => $acadTerm,
                        'acad_code' => $acadCode,
                    ]);
                }
            }
        } else {
            $status = strtolower($record['status']);
            if ($status === 'late' || $status === 'absent') {
                $fineAmount = $getFineAmount(ucfirst($status));

                Transaction::create([
                    'student_id' => $record['student_id'],
                    'event' => $record['event'],
                    'transaction_type' => 'FINE',
                    'fine_amount' => $fineAmount,
                    'org' => $studentOrg ?? 'N/A',
                    'date' => $record['date'],
                    'acad_term' => $acadTerm,
                    'acad_code' => $acadCode,
                ]);
            }
        }
    }

    // 🔁 Now handle students who were NOT in the attendance_data (not scanned)
    if (count($attendanceData) > 0) {
        $eventName = $attendanceData[0]['event'];
        $eventDate = $attendanceData[0]['date'];
        $eventOrg = Event::where('name', $eventName)
            ->whereDate('event_date', $eventDate)
            ->value('org');

        $fineSettings = FineSetting::where('org', $eventOrg)->first();
        if ($eventOrg && $fineSettings) {
            $allOrgUsers = User::where('org', $eventOrg)
                ->where('term', $acadTerm)
                ->get();

            foreach ($allOrgUsers as $user) {
                if (in_array($user->student_id, $existingIds)) continue;

                $isOfficer = str_contains($user->role, '- Officer');
                $getFineAmount = function ($status) use ($fineSettings, $isOfficer) {
                    return match ($status) {
                        'Absent' => $isOfficer ? $fineSettings->absent_officer : $fineSettings->absent_member,
                        default => 0,
                    };
                };

                $fineAmountMorning = $getFineAmount('Absent');
                $fineAmountAfternoon = $getFineAmount('Absent');

                // Assume event is wholeday if the original record has separate sessions
                $isWholeDay = isset($attendanceData[0]['status_morning']);

                if ($isWholeDay) {
                    foreach (['Morning', 'Afternoon'] as $session) {
                        Transaction::create([
                            'student_id' => $user->student_id,
                            'event' => $eventName . ' - ' . $session,
                            'transaction_type' => 'FINE',
                            'fine_amount' => $session === 'Morning' ? $fineAmountMorning : $fineAmountAfternoon,
                            'org' => $user->org,
                            'date' => $eventDate,
                            'acad_term' => $acadTerm,
                            'acad_code' => $acadCode,
                        ]);
                    }
                } else {
                    Transaction::create([
                        'student_id' => $user->student_id,
                        'event' => $eventName,
                        'transaction_type' => 'FINE',
                        'fine_amount' => $fineAmountMorning, // single session
                        'org' => $user->org,
                        'date' => $eventDate,
                        'acad_term' => $acadTerm,
                        'acad_code' => $acadCode,
                    ]);
                }
            }
        }
    }

    return redirect()->back()->with('success', 'Attendance fines recorded successfully, including absentees.');
}



public function liveData()
{
    $event = Event::whereDate('event_date', now()->toDateString())->first();

    if (!$event) return [];

    $records = Attendance::with('student')
        ->where('event_id', $event->id)
        ->get()
        ->map(function ($att) use ($event) {
            return [
                'student_id' => $att->student->id_number,
                'name' => $att->student->name,
                'program' => $att->student->course,
                'block' => $att->student->section,
                'event' => $event->name,
                'date' => $att->date,
                'time_in1' => $att->time_in1,
                'time_out1' => $att->time_out1,
                'time_in2' => $att->time_in2,
                'time_out2' => $att->time_out2,
                'status' => $att->status, // for 2-timeout events
                'status_morning' => $att->status_morning, // for 4-timeout events
                'status_afternoon' => $att->status_afternoon, // for 4-timeout events
            ];
        });

    return response()->json($records);
}
    
}
