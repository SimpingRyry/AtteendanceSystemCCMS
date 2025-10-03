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
use Illuminate\Support\Facades\DB;
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

    $isParentOrg = function ($org) {
        return \App\Models\OrgList::where('name', $org)->whereNull('parent_id')->exists();
    };

    $getChildOrgs = function ($org) {
        return \App\Models\OrgList::whereHas('parent', function ($q) use ($org) {
            $q->where('name', $org);
        })->pluck('name')->toArray();
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

        $studentOrg = User::where('student_id', $record['student_id'])
            ->where('term', $acadTerm)
            ->value('org') ?? $eventOrg;

        $isParent = $isParentOrg($eventOrg);
        $childOrgs = $isParent ? $getChildOrgs($eventOrg) : [];

        $isOfficer = $isParent
            ? $isOfficerForOrg($record['student_id'], $eventOrg)
            : $isOfficerForOrg($record['student_id'], $eventOrg);

        $applyParentFine = $isParent && in_array($studentOrg, $childOrgs) && !$isOfficer;

        $getFineAmount = function ($status) use ($fineSettings, $isOfficer, $applyParentFine) {
            if ($isOfficer) {
                return match ($status) {
                    'Late' => $fineSettings->late_officer,
                    'Absent' => $fineSettings->absent_officer,
                    default => 0,
                };
            } elseif ($applyParentFine) {
                return match ($status) {
                    'Late' => $fineSettings->late_member,
                    'Absent' => $fineSettings->absent_member,
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

        $isParent = $isParentOrg($eventOrg);
        $childOrgs = $isParent ? $getChildOrgs($eventOrg) : [];

        $allOrgUsers = $isParent
            ? User::whereIn('org', $childOrgs)->orWhere('org', $eventOrg)->where('term', $acadTerm)->get()
            : User::where('org', $eventOrg)->where('term', $acadTerm)->get();

        foreach ($allOrgUsers as $user) {
            if (in_array($user->student_id, $existingIds)) continue;

            $isOfficer = $isParent
                ? $isOfficerForOrg($user->student_id, $eventOrg)
                : $isOfficerForOrg($user->student_id, $eventOrg);

            $applyParentFine = $isParent && in_array($user->org, $childOrgs) && !$isOfficer;

            $getFineAmount = function ($status) use ($fineSettings, $isOfficer, $applyParentFine) {
                if ($isOfficer) {
                    return match ($status) {
                        'Absent' => $fineSettings->absent_officer,
                        default => 0,
                    };
                } elseif ($applyParentFine) {
                    return match ($status) {
                        'Absent' => $fineSettings->absent_member,
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

public function getAttendance($eventId, $studentId)
{
 $attendance = Attendance::where('event_id', $eventId)
        ->where('student_id', $studentId)
        ->first();

    if ($attendance) {
        return response()->json([
            'time_in1' => $attendance->time_in1,
            'time_out1' => $attendance->time_out1,
            'time_in2' => $attendance->time_in2,
            'time_out2' => $attendance->time_out2,
            'status' => $attendance->status,
            'morning_status' => $attendance->morning_status,
            'afternoon_status' => $attendance->afternoon_status,
        ]);
    }

    return response()->json(null, 404);
}


public function showArchive(Request $request, $event_id)
{
    $archiveEvent = Event::findOrFail($event_id);
    $archiveAttendances = Attendance::with('student', 'event')
        ->where('event_id', $event_id)
        ->get();


    return view('attendance', compact('archiveEvent', 'archiveAttendances'));
}

public function liveData(Request $request)
{
    $event = Event::whereDate('event_date', now('Asia/Manila')->toDateString())->first();

    if (!$event) return [];

    $query = Attendance::with('student')
        ->where('event_id', $event->id);

    // ✅ Apply status filter if user selected one
    if ($request->filled('status')) {
        $status = $request->status;

        // Handle both 2-timeout and 4-timeout events
        if ($event->timeouts == 4) {
            $query->where(function ($q) use ($status) {
                $q->where('status_morning', $status)
                  ->orWhere('status_afternoon', $status);
            });
        } else {
            $query->where('status', $status);
        }
    }

    $records = $query->get()->map(function ($att) use ($event) {
        return [
            'student_id'       => $att->student->id_number,
            'name'             => $att->student->name,
            'program'          => $att->student->course,
            'block'            => $att->student->section,
            'event'            => $event->name,
            'date'             => $att->date,
            'time_in1'         => $att->time_in1,
            'time_out1'        => $att->time_out1,
            'time_in2'         => $att->time_in2,
            'time_out2'        => $att->time_out2,
            'status'           => $att->status,          // 2-timeout events
            'status_morning'   => $att->status_morning,  // 4-timeout events
            'status_afternoon' => $att->status_afternoon // 4-timeout events
        ];
    });

    return response()->json($records);
}
public function fetchArchiveAjax($event_id)
{
    $event = Event::findOrFail($event_id);
    $attendances = Attendance::with('student', 'event')
        ->where('event_id', $event_id)
        ->get();

    return view('partials.attendance_archive_table', compact('event', 'attendances'))->render();
}
public function submitExcuse(Request $request)
{
    $request->validate([
        'attendance_id' => 'required|exists:attendances,id',
        'reason' => 'required|string',
        'excuse_letter' => 'required|file|mimes:pdf,jpg,png|max:2048',
    ]);

    DB::transaction(function () use ($request) {
        // Save file to public/excuses
        $file = $request->file('excuse_letter');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('excuses');
        
        // Create the directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $excusePath = 'excuses/' . $filename;

        // Update attendance
        $attendance = Attendance::findOrFail($request->attendance_id);
        $event = $attendance->event;

        if ($event->timeouts == 4) {
            $attendance->morning_status = 'Excused';
            $attendance->afternoon_status = 'Excused';
        } else {
            $attendance->status = 'Excused';
        }

        $attendance->excuse_reason = $request->reason;
        $attendance->excuse_letter = $excusePath;
        $attendance->save();

        // Delete related fine
        Transaction::where('student_id', $attendance->student_id)
            ->where('event', $attendance->event->name)
            ->whereDate('date', $attendance->date)
            ->where('transaction_type', 'fine')
            ->delete();
    });

    return redirect()->back()->with('success', 'Excuse submitted and fine removed.');
}
public function bulkRevert(Request $request)
{
    $ids = $request->input('attendance_ids', []);

    if (!empty($ids)) {
        foreach ($ids as $id) {
            $attendance = Attendance::findOrFail($id);
            $event = $attendance->event;

            // Revert status depending on timeouts
            if ($event->timeouts == 4) {
                // Reset both morning and afternoon statuses
                $attendance->morning_status = 'On Time';
                $attendance->afternoon_status = 'On Time';
            } else {
                $attendance->status = 'On Time';
            }

            // Clear excuse fields (since this is a revert, not excuse)
            $attendance->excuse_reason = null;
            $attendance->excuse_letter = null;
            $attendance->save();

            // Delete related fines
            Transaction::where('student_id', $attendance->student_id)
                ->where('event', $attendance->event->name)
                ->whereDate('date', $attendance->date)
                ->where('transaction_type', 'fine')
                ->delete();
        }
    }

    return back()->with('success', 'Selected attendances reverted and fines removed.');
}
}
