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
        // Validate and save the event
        $validated = $request->validate([
            'name' => 'required|string',
            'venue' => 'required|string',
            'event_date' => 'required|date',
            'timeouts' => 'required|integer|in:2,4', // Ensure timeouts is either 2 or 4
            'course' => 'required|string',
            'times' => 'required|array',
            'times.*' => 'required|date_format:H:i', // Ensure that the times follow HH:mm format
        ]);

        // Save event to the database
        $event = new Event();
        $event->name = $validated['name'];
        $event->venue = $validated['venue'];
        $event->event_date = $validated['event_date'];
        $event->timeouts = $validated['timeouts'];
        $event->course = $validated['course'];

        // Check number of timeouts and store the corresponding time slots
        if ($validated['timeouts'] == 2) {
            // Ensure only 2 timeouts are provided
            $event->times = json_encode(array_slice($validated['times'], 0, 2));
        } elseif ($validated['timeouts'] == 4) {
            // Ensure 4 timeouts are provided
            $event->times = json_encode(array_slice($validated['times'], 0, 4));
        }

        $event->save();

        return redirect()->back()->with('success', 'Event successfully!');
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
                    'start' => $event->event_date . 'T' . $times[0], // 08:00
                    'end' => $event->event_date . 'T' . $times[1],   // 12:00
                ];
            } elseif ($event->timeouts == 4) {
                // Morning session
                $calendarEvents[] = [
                    'title' => $event->name . ' (Morning)',
                    'start' => $event->event_date . 'T' . $times[0], // 08:00
                    'end' => $event->event_date . 'T' . $times[1],   // 12:00
                ];
                // Afternoon session
                $calendarEvents[] = [
                    'title' => $event->name . ' (Afternoon)',
                    'start' => $event->event_date . 'T' . $times[2], // 13:00
                    'end' => $event->event_date . 'T' . $times[3],   // 17:00
                ];
            }
        }
    
        return response()->json(data: $calendarEvents);
    }

    
}
