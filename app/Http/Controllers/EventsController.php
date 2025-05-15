<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

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
            'repeat_dates' => 'nullable|string' // should be comma-separated dates
        ]);
    
        // Determine times based on the selected option
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
    
        // Initial event (main date)
        $dates = [$request->event_date];
    
        // Add repeated dates if available
        if (!empty($request->repeat_dates)) {
            $extraDates = explode(',', $request->repeat_dates);
            $extraDates = array_map('trim', $extraDates);
            $dates = array_merge($dates, $extraDates);
        }
    
        // Create an event for each date
        foreach ($dates as $date) {
            Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'venue' => $request->venue,
                'event_date' => $date,
                'timeouts' => $request->timeouts,
                'times' => json_encode($times),
                'course' => $request->course,
            ]);
        }
    
        return redirect()->back()->with('success', 'Events created successfully!');
    }
    public function fetchEvents()
    {
        $events = Event::all();
    
        $calendarEvents = [];
    
        foreach ($events as $event) {
            $times = json_decode($event->times, true); // Decode the JSON times array
    
            if ($event->timeouts == 2) {
                // Half day
                $calendarEvents[] = [
                    'title' => $event->name,
                    'start' => $event->event_date . 'T' . $times[0],
                    'end' => $event->event_date . 'T' . $times[1],
                    'extendedProps' => [
                        'id' => $event->id, // Include event ID
                        'course' => $event->course,
                        'venue' => $event->venue,
                        'timeout' => $event->timeouts,
                        'times' => $times,
                    ],
                    'color' => $event->course === 'BSIS' ? 'violet' : 'blue',
                ];
            } elseif ($event->timeouts == 4) {
                // Morning session
                $calendarEvents[] = [
                    'title' => $event->name . ' (Morning)',
                    'start' => $event->event_date . 'T' . $times[0],
                    'end' => $event->event_date . 'T' . $times[1],
                    'extendedProps' => [
                        'id' => $event->id, // Include event ID
                        'course' => $event->course,
                        'venue' => $event->venue,
                        'timeout' => $event->timeouts,
                        'times' => $times,
                    ],
                    'color' => $event->course === 'BSIS' ? 'violet' : 'blue',
                ];
                // Afternoon session
                $calendarEvents[] = [
                    'title' => $event->name . ' (Afternoon)',
                    'start' => $event->event_date . 'T' . $times[2],
                    'end' => $event->event_date . 'T' . $times[3],
                    'extendedProps' => [
                        'id' => $event->id, // Include event ID
                        'course' => $event->course,
                        'venue' => $event->venue,
                        'timeout' => $event->timeouts,
                        'times' => $times,
                    ],
                    'color' => $event->course === 'BSIS' ? 'violet' : 'blue',
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
