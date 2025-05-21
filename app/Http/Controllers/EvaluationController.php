<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvalAnswer;
use App\Models\Event;
use App\Models\UserProfile;
use App\Models\EvaluationQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    // ----------------------------------------------
    // LIST
    // ----------------------------------------------
    public function index()
    {
        $evaluations = Evaluation::latest()->get();
        $eventNames = Event::pluck('name', 'id'); // Fixed: Use id=>name pair

        return view('evaluation', compact('evaluations', 'eventNames'));
    }

    public function create()
    {
        $eventNames = Event::pluck('name', 'id');

        return view('evaluation', [
            'evaluations' => Evaluation::latest()->get(),
            'eventNames' => $eventNames
        ]);
    }

    // ----------------------------------------------
    // STORE
    // ----------------------------------------------
    public function store(Request $request)
{
    $request->validate([
        'event_id'       => 'required|exists:events,id',
        'title'          => 'required|string|max:255',
        'description'    => 'nullable|string',
        'questions_json' => 'required|json'
    ]);

    DB::beginTransaction();

    try {
        // Fetch the event to get 'name' and 'course'
        $event = Event::findOrFail($request->event_id);

        // Save evaluation with event name and course
        $evaluation = Evaluation::create([
            'event_id'    => $event->id,
            'event'       => $event->name,   // store event name as string
            'course'      => $event->course, // store course as string
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        $questions = json_decode($request->questions_json, true);

        foreach ($questions as $index => $q) {
            EvaluationQuestion::create([
                'evaluation_id' => $evaluation->id,
                'question'      => $q['question'],
                'type'          => $q['type'],
                'options'       => $q['options'] ?? null,
                'order'         => $index + 1,
                'is_required'   => !empty($q['is_required']),
            ]);
        }

        DB::commit();

        return redirect()
            ->back()
            ->with('success', 'Evaluation created successfully!');
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}


    // ----------------------------------------------
    // FETCH QUESTIONS (AJAX)
    // ----------------------------------------------
    public function questions(Evaluation $evaluation)
    {
        return response()->json(
            $evaluation->load('questions')
        );
    }

    // ----------------------------------------------
    // UPDATE
    // ----------------------------------------------
    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'questions_json' => 'required|json',
        ]);

        DB::beginTransaction();

        try {
            // Update evaluation fields
            $evaluation->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            $questions = json_decode($validated['questions_json'], true);

            // Remove deleted questions
            $submittedIds = collect($questions)->pluck('id')->filter()->all();
            EvaluationQuestion::where('evaluation_id', $evaluation->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();

            // Insert or update each question
            foreach ($questions as $index => $q) {
                $data = [
                    'question'    => $q['question'],
                    'type'        => $q['type'] ?? 'text',
                    'options'     => $q['options'] ?? null,
                    'order'       => $index + 1,
                    'is_required' => !empty($q['is_required']),
                ];

                if (!empty($q['id'])) {
                    EvaluationQuestion::where('id', $q['id'])->update($data);
                } else {
                    $data['evaluation_id'] = $evaluation->id;
                    EvaluationQuestion::create($data);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Evaluation and questions updated!'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
