<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Setting;
use App\Models\FineSetting;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\OrgList;

class ApplyAbsentAndIncompleteFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   

    /**
     * The console command description.
     *
     * @var string
     */


    /**
     * Execute the console command.
     *
     * @return int
     */
   protected $signature = 'fines:apply';
    protected $description = 'Apply fines for absentees and incomplete attendance (no timeout)';

public function handle()
{
    $today = Carbon::now('Asia/Manila')->toDateString();

    $events = Event::whereDate('event_date', $today)->get();
    $setting = Setting::where('key', 'academic_term')->first();
    $term = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    foreach ($events as $event) {
        $eventOrgName = $event->org;
        $eventOrg = OrgList::where('org_name', $eventOrgName)->first();
        if (!$eventOrg) continue;

        $fineSettings = FineSetting::where('org', $eventOrgName)->first();
        if (!$fineSettings) continue;

        // Collect org and its child org names
        $orgNames = collect([$eventOrgName]);
        $childOrgs = $eventOrg->children()->pluck('org_name');
        $orgNames = $orgNames->merge($childOrgs);

        // Get all student IDs from users belonging to those orgs
        $userIds = User::whereIn('org', $orgNames)->pluck('student_id');
        $allStudents = Student::whereIn('id_number', $userIds)->get();

        $isWholeDay = count(json_decode($event->times)) === 4;

        foreach ($allStudents as $student) {
            $attendance = Attendance::where('student_id', $student->id_number)
                ->where('event_id', $event->id)
                ->first();

            // Get user details for officer check
            $user = User::where('student_id', $student->id_number)->first();

            $isOfficer = false;

            if ($eventOrg->parent_org_id) {
                $parentOrg = OrgList::find($eventOrg->parent_org_id);
                if ($parentOrg) {
                    $isOfficer = User::where('student_id', $student->id_number)
                        ->where('org', $parentOrg->org_name)
                        ->where('term', $term)
                        ->where('role', 'like', '%- Officer')
                        ->exists();
                }
            }

            $absentFine = $isOfficer ? $fineSettings->absent_officer : $fineSettings->absent_member;

            // Case 1: No attendance at all = Absent
            if (!$attendance) {
                if ($absentFine > 0) {
                    Transaction::updateOrCreate(
                        [
                            'student_id' => $student->id_number,
                            'event' => $event->name,
                            'transaction_type' => 'FINE',
                            'org' => $eventOrgName,
                            'date' => $today,
                        ],
                        [
                            'fine_amount' => $absentFine,
                            'acad_term' => $term,
                            'acad_code' => $acadCode,
                        ]
                    );
                }
                continue;
            }

            // Case 2: Has time-in but missing timeout = also treated as Absent
            $missed = false;
            if ($isWholeDay) {
                if ($attendance->time_in1 && !$attendance->time_out1) $missed = true;
                if ($attendance->time_in2 && !$attendance->time_out2) $missed = true;
            } else {
                if ($attendance->time_in1 && !$attendance->time_out1) $missed = true;
            }

            if ($missed) {
                Transaction::updateOrCreate(
                    [
                        'student_id' => $student->id_number,
                        'event' => $event->name,
                        'transaction_type' => 'FINE',
                        'org' => $eventOrgName,
                        'date' => $today,
                    ],
                    [
                        'fine_amount' => $absentFine,
                        'acad_term' => $term,
                        'acad_code' => $acadCode,
                    ]
                );
            }
        }
    }

    $this->info('Absent fines applied including incomplete attendances.');
}

}
