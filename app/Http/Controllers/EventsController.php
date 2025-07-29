<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Evaluation;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EvaluationAssignment;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{

public function index()
{
    $orgs = OrgList::all();
    $evaluationTemplates = Evaluation::all();

    $org = auth()->user()->org;

    // Get default time settings for the current org
    $timeSettings = DB::table('fine_settings')
        ->where('org', $org)
        ->select('morning_in', 'morning_out', 'afternoon_in', 'afternoon_out')
        ->first();

    return view('events', compact('orgs', 'evaluationTemplates', 'timeSettings'));
}
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'venue' => 'nullable|string',
        'event_date' => 'required|date',
        'timeouts' => 'required|in:2,4',
        'course' => 'nullable|string',
        'organization' => 'nullable|string', // for Super Admin
        'repeat_dates' => 'nullable|string',
        'guests' => 'nullable|string',
        'involved_students' => 'required|in:All,Members,Officers',
        'attached_memo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'evaluation_template' => 'required|exists:evaluation,id',
    ]);

    $times = [];
    if ($request->timeouts == 2) {
        $times = [$request->half_start, $request->half_end];
    } elseif ($request->timeouts == 4) {
        $times = [
            $request->morning_start,
            $request->morning_end,
            $request->afternoon_start,
            $request->afternoon_end
        ];
    }

    $dates = [$request->event_date];
    if (!empty($request->repeat_dates)) {
        $extraDates = array_map('trim', explode(',', $request->repeat_dates));
        $dates = array_merge($dates, $extraDates);
    }

    $user = auth()->user();
    $role = $user->role ?? null;
    $organization = $role === 'Super Admin' ? $request->organization : $user->org;

    $course = null;
    if ($role === 'Super Admin') {
        $course = 'MAIN-' . $request->course;
    } elseif ($organization === 'Information Technology Society' || $organization === 'ITS') {
        $course = 'MAIN-BSIT';
    } elseif ($organization === 'PRAXIS') {
        $course = 'MAIN-BSIS';
    } elseif ($organization === 'CCMS Student Government') {
        $course = 'All';
    }

    $guestEmails = [];
    if (!empty($request->guests)) {
        $guestEmails = array_filter(array_map('trim', explode(',', $request->guests)));
    }

    $memoFilename = null;
    if ($request->hasFile('attached_memo')) {
        $image = $request->file('attached_memo');
        $memoFilename = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images'), $memoFilename);
    }

    foreach ($dates as $date) {
        $existingEvent = Event::where('event_date', $date)
            ->when($request->venue, function ($query) use ($request) {
                return $query->where('venue', $request->venue);
            })
            ->first();

        if ($existingEvent) {
            return redirect()->back()->withErrors([
                'event_date' => "An event is already scheduled on $date at venue '{$request->venue}'."
            ])->withInput();
        }

        $event = Event::create([
            'name' => $request->name,
            'venue' => $request->venue,
            'event_date' => $date,
            'timeouts' => $request->timeouts,
            'times' => json_encode($times),
            'course' => $course,
            'guests' => json_encode($guestEmails),
            'description' => $request->description,
            'org' => $organization,
            'involved_students' => $request->involved_students,
            'attached_memo' => $memoFilename,
        ]);

        EvaluationAssignment::create([
            'event_id' => $event->id,
            'evaluation_id' => $request->evaluation_template,
        ]);

        $involved = $request->involved_students;

       $targetOrgs = [$organization];

if (in_array($involved, ['All', 'Members'])) {
    $orgModel = \App\Models\OrgList::where('org_name', $organization)->first();
    $childOrgs = $orgModel ? $orgModel->children->pluck('org_name')->toArray() : [];
    $targetOrgs = array_merge($targetOrgs, $childOrgs);
}

// Fetch users based on involved type and selected org scope
$recipients = \App\Models\User::whereIn('org', $targetOrgs)->get()->filter(function ($user) use ($involved) {
    $role = strtolower($user->role);
    return match ($involved) {
        'All'     => $role === 'member' || str_contains($role, 'officer'),
        'Members' => $role === 'member',
        'Officers' => str_contains($role, 'officer'),
        default   => false,
    };
});

        foreach ($recipients as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'New Event: ' . $event->name,
                'message' => 'An event is scheduled for ' . $event->event_date . ' at ' . $event->venue,
            ]);
        }

        $advisers = \App\Models\User::whereIn('org', $targetOrgs)
    ->where('role', 'Adviser')
    ->get();

foreach ($advisers as $adviser) {
    \App\Models\Notification::create([
        'user_id' => $adviser->id,
        'title' => 'Adviser Notice: Event "' . $event->name . '" Scheduled',
        'message' => 'Your advised org has an event on ' . $event->event_date . ' at ' . $event->venue,
    ]);
}

        if (!empty($guestEmails)) {
            $guestUsers = \App\Models\User::whereIn('email', $guestEmails)->get();

            foreach ($guestUsers as $guest) {
                \App\Models\Notification::create([
                    'user_id' => $guest->id,
                    'title' => 'You were tagged in an event: ' . $event->name,
                    'message' => 'You were invited to the event on ' . $event->event_date . ' at ' . $event->venue,
                ]);
            }
        }
    }

    return redirect()->back()->with('success', 'Events created and evaluation assigned successfully!');
}


    
    
    
public function fetchEvents()
{
    $user = Auth::user();
    $userOrgName = strtolower(trim($user->org));
    $userRole = strtolower($user->role);

    // Get user's org record
    $userOrg = OrgList::whereRaw('LOWER(TRIM(org_name)) = ?', [$userOrgName])->first();

    if (!$userOrg) {
        return response()->json([]); // No org, no events
    }

    $userOrgId = $userOrg->id;

    // ✅ Get all orgs the user should access:
    // If parent, only self
    // If child, self + parent (if parent has "all" events)

    $accessibleOrgIds = [$userOrgId];

    if ($userOrg->parent_org_id) {
        // If this is a child org
        $accessibleOrgIds[] = $userOrg->parent_org_id;
    }

    // Get accessible org names
    $accessibleOrgNames = OrgList::whereIn('id', $accessibleOrgIds)
        ->pluck('org_name')
        ->map(fn($name) => strtolower(trim($name)))
        ->toArray();

    $events = Event::all();
    $calendarEvents = [];

    foreach ($events as $event) {
        $eventOrg = strtolower(trim($event->org));
        $involved = strtolower($event->involved_students);
        $times = json_decode($event->times, true);
        $guests = json_decode($event->guests, true);

        // ✅ Skip if not from accessible orgs
        if (!in_array($eventOrg, $accessibleOrgNames)) {
            continue;
        }

        // ✅ Child org user: only see parent events if "involved" is "all"
        if (
            $eventOrg !== $userOrgName && // means event is from parent
            $userOrg->parent_org_id &&    // this user is from child org
            $involved !== 'all'
        ) {
            continue;
        }

        // ✅ Role restrictions
        if ($involved === 'members' && $userRole !== 'member') {
            continue;
        }

        if ($involved === 'officers' && stripos($user->role, 'officer') === false) {
            continue;
        }

        if (!in_array($involved, ['all', 'members', 'officers'])) {
            continue;
        }

        // ✅ Format for calendar
        $calendarEvents[] = [
            'id' => $event->id,
            'title' => $event->name,
            'start' => $event->event_date . 'T' . $times[0],
            'end' => $event->timeouts == 4
                ? $event->event_date . 'T' . $times[3]
                : $event->event_date . 'T' . $times[1],
            'extendedProps' => [
                'id' => $event->id,
                'org' => $event->org,
                'venue' => $event->venue,
                'timeout' => $event->timeouts,
                'times' => $times,
                'guests' => $guests,
                'description' => $event->description,
                'attached_memo' => $event->attached_memo,
                'attached_memo_url' => $event->attached_memo
                    ? asset('images/' . $event->attached_memo)
                    : null,
            ],
            'color' => strtolower($event->org) === 'praxis' ? 'violet' : (empty($event->org) ? 'green' : 'blue'),
        ];
    }

    return response()->json($calendarEvents);
}


    
    public function update(Request $request)
    {
        // dd($request->all());
    
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'title' => 'required|string|max:255',
            'venue' => 'nullable|string',
            'date' => 'required|date',
            'course' => 'nullable|string',
            'dayType' => 'required|in:Half Day,Whole Day',
            'edit_times' => 'required|array',
            'edit_times.*' => 'required|date_format:H:i', // Validate each time value
        ]);
    
        $expectedTimeCount = $request->dayType === 'Half Day' ? 2 : 4;
    
        if (count($request->edit_times) !== $expectedTimeCount) {
            return redirect()->back()->withErrors([
                'edit_times' => "The number of times must match the selected Day Type. Expected $expectedTimeCount time fields.",
            ])->withInput();
        }
    
        $event = Event::findOrFail($request->event_id);
    
        $event->name = $request->title;
        $event->venue = $request->venue;
        $event->event_date = $request->date;
        $event->course = $request->course;
        $event->timeouts = $expectedTimeCount;
        $event->times = json_encode($request->edit_times); // Save times as JSON
    
        $event->save();
    
        return redirect()->back()->with('success', 'Event updated successfully!');
    }
    public function publicFetchEvents()
{
    $events = Event::all();
    $calendarEvents = [];

    foreach ($events as $event) {
        $times = json_decode($event->times, true);
        $guests = json_decode($event->guests, true);
        $cleanCourse = Str::replaceFirst('MAIN-', '', $event->course);

        if ($event->timeouts == 2) {
            $calendarEvents[] = [
                'title' => $event->name,
                'start' => $event->event_date . 'T' . $times[0],
                'end' => $event->event_date . 'T' . $times[1],
                'extendedProps' => [
                    'id' => $event->id,
                    'course' => $cleanCourse,
                    'venue' => $event->venue,
                    'timeout' => $event->timeouts,
                    'times' => $times,
                    'guests' => $guests,
                    'description' => $event->description
                ],
                'color' => $cleanCourse === 'BSIS' ? 'violet' : ($cleanCourse === 'All' ? 'green' : 'blue'),
            ];
        } elseif ($event->timeouts == 4) {
            $calendarEvents[] = [
                'title' => $event->name,
                'start' => $event->event_date . 'T' . $times[0],
                'end' => $event->event_date . 'T' . $times[3],
                'extendedProps' => [
                    'id' => $event->id,
                    'course' => $cleanCourse,
                    'venue' => $event->venue,
                    'timeout' => $event->timeouts,
                    'times' => $times,
                    'guests' => $guests
                ],
                'color' => $cleanCourse === 'BSIS' ? 'violet' : ($cleanCourse === 'All' ? 'green' : 'blue'),
            ];
        }
    }

    return response()->json($calendarEvents);
}

public function CurrentEvent()
{
    $today = Carbon::today('Asia/Manila')->toDateString(); // Gets today's date in 'Y-m-d'

    $currentEvent = Event::whereDate('event_date', $today)->first(); // Assumes only one event per day
    
    $pastEvents = Event::whereDate('event_date', '<', now())
        ->where('org', auth()->user()->org)
        ->get();

    return view('attendance', [ // replace 'your_view_name' with actual Blade name
        'currentEvent' => $currentEvent,
        'pastEvents' => $pastEvents,
       
    ]);
}

}
