<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Attendance;
use App\Models\FineSetting;
use Illuminate\Support\Facades\Log;
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

    $isOfficerForOrg = function ($studentId, $org) use ($acadTerm) {
        return User::where('student_id', $studentId)
            ->where('term', $acadTerm)
            ->where('org', $org)
            ->where('role', 'like', '%- Officer')
            ->exists();
    };

    foreach ($attendanceData as $record) {
        $existingIds[] = $record['student_id'];

        $hasSeparateSessions = isset($record['status_morning']) && isset($record['status_afternoon']);

        $eventOrg = Event::where('name', $record['event'])
            ->whereDate('event_date', $record['date'])
            ->value('org');

        if (!$eventOrg) continue;

        $fineSettings = FineSetting::where('org', $eventOrg)->first();
        if (!$fineSettings) continue;

        $isOfficer = $isOfficerForOrg($record['student_id'], $eventOrg);
       $studentOrg = $eventOrg === 'CCMS Student Government'
    ? $eventOrg
    : (User::where('student_id', $record['student_id'])->where('term', $acadTerm)->value('org') ?? $eventOrg);

        $getFineAmount = function ($status) use ($fineSettings, $isOfficer) {
            if ($isOfficer) {
                return match ($status) {
                    'Late' => $fineSettings->late_officer,
                    'Absent' => $fineSettings->absent_officer,
                    default => 0,
                };
            } else {
                return match ($status) {
                    'Late' => $fineSettings->late_member,
                    'Absent' => $fineSettings->absent_member,
                    default => 0,
                };
            }
        };

        if ($hasSeparateSessions) {
            foreach (['morning' => $record['status_morning'], 'afternoon' => $record['status_afternoon']] as $session => $status) {
                $status = strtolower($status);
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
            $shouldSkipLateFine = false;

            if ($status === 'late') {
                $attendance = Attendance::where('student_id', $record['student_id'])
                    ->whereDate('created_at', $record['date'])
                    ->first();

                if ($attendance && !is_null($attendance->time_in1) && is_null($attendance->time_out1)) {
                    $shouldSkipLateFine = true;
                }
            }

            if (($status === 'late' && !$shouldSkipLateFine) || $status === 'absent') {
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

    // === Handle unscanned absentees ===
    if (count($attendanceData) > 0) {
        $eventName = $attendanceData[0]['event'];
        $eventDate = $attendanceData[0]['date'];

        $eventOrg = Event::where('name', $eventName)
            ->whereDate('event_date', $eventDate)
            ->value('org');

        $fineSettings = FineSetting::where('org', $eventOrg)->first();
        if (!$eventOrg || !$fineSettings) return;

        $allOrgUsers = $eventOrg === 'CCMS Student Government'
            ? User::where('term', $acadTerm)->get()
            : User::where('org', $eventOrg)->where('term', $acadTerm)->get();

        foreach ($allOrgUsers as $user) {
            if (in_array($user->student_id, $existingIds)) continue;

            $isOfficer = $isOfficerForOrg($user->student_id, $eventOrg);
            $getFineAmount = function ($status) use ($fineSettings, $isOfficer) {
                if ($isOfficer) {
                    return match ($status) {
                        'Absent' => $fineSettings->absent_officer,
                        default => 0,
                    };
                } else {
                    return match ($status) {
                        'Absent' => $fineSettings->absent_member,
                        default => 0,
                    };
                }
            };

            $fineAmount = $getFineAmount('Absent');
            $isWholeDay = isset($attendanceData[0]['status_morning']);

            if ($fineAmount > 0) {
                if ($isWholeDay) {
                    foreach (['Morning', 'Afternoon'] as $session) {
                        Transaction::create([
                            'student_id' => $user->student_id,
                            'event' => $eventName . ' - ' . $session,
                            'transaction_type' => 'FINE',
                            'fine_amount' => $fineAmount,
                            'org' => $eventOrg,
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
                        'fine_amount' => $fineAmount,
                        'org' => $eventOrg,
                        'date' => $eventDate,
                        'acad_term' => $acadTerm,
                        'acad_code' => $acadCode,
                    ]);
                }
            }
        }
    }

    // === Auto-mark afternoon absent ===
    foreach ($attendanceData as $record) {
        if (!isset($record['status_morning']) || !isset($record['status_afternoon'])) continue;

        $attendance = Attendance::where('student_id', $record['student_id'])
            ->whereDate('created_at', $record['date'])
            ->first();

        if (
            $attendance &&
            !is_null($attendance->time_in1) &&
            !is_null($attendance->time_out1) &&
            is_null($attendance->time_in2) &&
            is_null($attendance->time_out2) &&
            is_null($attendance->status_afternoon)
        ) {
            $attendance->status_afternoon = 'Absent';
            $attendance->save();

            $eventOrg = Event::where('name', $record['event'])
                ->whereDate('event_date', $record['date'])
                ->value('org');

            $fineSettings = FineSetting::where('org', $eventOrg)->first();
            $isOfficer = $isOfficerForOrg($record['student_id'], $eventOrg);
            $fineAmount = $isOfficer ? $fineSettings->absent_officer : $fineSettings->absent_member;

            if ($fineAmount > 0) {
                Transaction::create([
                    'student_id' => $record['student_id'],
                    'event' => $record['event'] . ' - Afternoon',
                    'transaction_type' => 'FINE',
                    'fine_amount' => $fineAmount,
                    'org' => $eventOrg,
                    'date' => $record['date'],
                    'acad_term' => $acadTerm,
                    'acad_code' => $acadCode,
                ]);
            }
        }
    }

    // === Auto-mark half-day absent ===
    foreach ($attendanceData as $record) {
        if (isset($record['status_morning']) || isset($record['status_afternoon'])) continue;

        $attendance = Attendance::where('student_id', $record['student_id'])
            ->whereDate('created_at', $record['date'])
            ->first();

        if (
            $attendance &&
            !is_null($attendance->time_in1) &&
            is_null($attendance->time_out1) &&
            strtolower($attendance->status) !== 'absent'
        ) {
            $attendance->status = 'Absent';
            $attendance->save();

            $eventOrg = Event::where('name', $record['event'])
                ->whereDate('event_date', $record['date'])
                ->value('org');

            $fineSettings = FineSetting::where('org', $eventOrg)->first();
            $isOfficer = $isOfficerForOrg($record['student_id'], $eventOrg);
            $fineAmount = $isOfficer ? $fineSettings->absent_officer : $fineSettings->absent_member;

            if ($fineAmount > 0) {
                Transaction::create([
                    'student_id' => $record['student_id'],
                    'event' => $record['event'],
                    'transaction_type' => 'FINE',
                    'fine_amount' => $fineAmount,
                    'org' => $eventOrg,
                    'date' => $record['date'],
                    'acad_term' => $acadTerm,
                    'acad_code' => $acadCode,
                ]);
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
