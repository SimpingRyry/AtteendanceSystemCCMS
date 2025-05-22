<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{

    public function index()
    {
        return view('events'); // Blade view
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
            'repeat_dates' => 'nullable|string',
            'guests' => 'nullable|string'
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
            $extraDates = explode(',', $request->repeat_dates);
            $extraDates = array_map('trim', $extraDates);
            $dates = array_merge($dates, $extraDates);
        }
    
        // ✅ Determine course based on user role or organization
        $user = auth()->user();
        $role = $user->role ?? null;
        $organization = $user->org ?? null;
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
    
        // ✅ Parse guest emails
        $guestEmails = [];
    
        if (!empty($request->guests)) {
            $guestEmails = array_filter(array_map('trim', explode(',', $request->guests)));
        }
    
        foreach ($dates as $date) {
            $event = Event::create([
                'name' => $request->name,
                'venue' => $request->venue,
                'event_date' => $date,
                'timeouts' => $request->timeouts,
                'times' => json_encode($times),
                'course' => $course,
                'guests' => json_encode($guestEmails),
                'description' => $request->description,
            ]);
    
            // ✅ Notify users based on course
            $recipients = \App\Models\User::when($course && $course !== 'All', function ($query) use ($course) {
                return $query->where(function ($q) use ($course) {
                    if ($course === 'MAIN-BSIT') {
                        $q->whereIn('org', ['Information Technology Society', 'ITS']);
                    } elseif ($course === 'MAIN-BSIS') {
                        $q->where('org', 'PRAXIS');
                    }
                });
            })->get();
    
            foreach ($recipients as $user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => 'New Event: ' . $event->name,
                    'message' => 'An event is scheduled for ' . $event->event_date . ' at ' . $event->venue,
                ]);
            }
    
            // ✅ Notify tagged guests
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
    
        return redirect()->back()->with('success', 'Events created and notifications sent successfully!');
    }
    
    
    public function fetchEvents()
    {
        $user = Auth::user(); // Get the logged-in user
        $userOrg = $user->org;
    
        // Get all events
        $events = Event::all();
        $calendarEvents = [];
    
        foreach ($events as $event) {
            $times = json_decode($event->times, true);
            $guests = json_decode($event->guests, true);
    
            // Clean the course name
            $cleanCourse = Str::replaceFirst('MAIN-', '', $event->course);
    
            // Filter based on user org
            if (
                ($userOrg === 'Information Technology Society' || $userOrg === 'ITS') && $cleanCourse !== 'BSIT' &&
                $cleanCourse !== 'All'
            ) {
                continue;
            }
    
            if (
                $userOrg === 'PRAXIS' && $cleanCourse !== 'BSIS' &&
                $cleanCourse !== 'All'
            ) {
                continue;
            }
    
            // Event with 2 timeouts
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
            }
            // Event with 4 timeouts
            elseif ($event->timeouts == 4) {
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
        'edit_times' => 'required|array|min:2|max:4'
    ]);

    $event = Event::findOrFail($request->event_id);

    $event->name = $request->title;
    $event->venue = $request->venue;
    $event->event_date = $request->date;
    $event->course = $request->course;

    // Convert dayType to timeouts
    $event->timeouts = $request->dayType === 'Half Day' ? 2 : 4;

    // Store times as JSON
    $event->times = json_encode($request->edit_times);

    $event->save();

    return redirect()->back()->with('success', 'Events created successfully!');
}
    
}
