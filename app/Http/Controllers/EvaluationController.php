<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\EvalAnswer;
use App\Models\Evaluation;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\DB;
use App\Models\EvaluationAssignment;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    // ----------------------------------------------
    // LIST
    // ----------------------------------------------
public function index()
{
    $user = Auth::user();
    $org = $user->org;

    // Evaluation assignments (for "Responses" tab)
    $assignments = EvaluationAssignment::with(['evaluation', 'event'])
        ->whereHas('event', function ($query) use ($org) {
            $query->where('org', $org);
        })
        ->latest()
        ->get();

    // Evaluations (for "Evaluations" tab)
    $evaluations = Evaluation::latest()->get();

    // Event names (for dropdown)
    $eventNames = Event::pluck('name', 'id'); // [id => name]

    return view('evaluation', compact('assignments', 'evaluations', 'eventNames'));
}

    public function create()
    {
        $eventNames = Event::pluck('name', 'id');

        return view('evaluation', [
            'evaluations' => Evaluation::latest()->get(),
            'eventNames' => $eventNames
        ]);
    }


    public function send(Request $request)
{
    
    $request->validate([
        'evaluation_id' => 'required|exists:evaluation,id',
        'event_id' => 'required|exists:events,id',
    ]);

    // Attach evaluation to event (logic depends on your DB structure)
    // Example if there's a pivot table or similar model:
    EvaluationAssignment::create([
        'evaluation_id' => $request->evaluation_id,
        'event_id' => $request->event_id,
    ]);

    return back()->with('success', 'Evaluation sent to event successfully!');
}

    public function getEvaluationJson($id)
{
    $evaluation = Evaluation::with(['question' => function($q) {
        $q->orderBy('order');
    }])->findOrFail($id);

    return response()->json([
        'id' => $evaluation->id,
        'title' => $evaluation->title,
        'description' => $evaluation->description,
        'questions' => $evaluation->question->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'type' => $q->type,
                'order' => $q->order,
                'is_required' => true,
                'options' => is_string($q->options) ? json_decode($q->options, true) : $q->options,
            ];
        })
    ]);
}

    // ----------------------------------------------
    // STORE
    // ----------------------------------------------
    public function store(Request $request)
{
    $request->validate([
        'event_id'       => 'nullable|exists:events,id',
        'title'          => 'required|string|max:255',
        'description'    => 'nullable|string',
        'questions_json' => 'required|json'
    ]);

    DB::beginTransaction();

    try {
        // Fetch the event to get 'name' and 'course'
      
        // Save evaluation with event name and course
       $evaluation = Evaluation::create([
    'title'       => $request->title,
    'description' => $request->description,
    'created_by'  => Auth::user()->name, // 👈 insert the current user's name
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
    public function getEvaluationWithQuestions($id)
{
    $evaluation = Evaluation::with(['questions']) // eager-load the related questions
        ->findOrFail($id);

        return response()->json([
            'id' => $evaluation->id,
            'title' => $evaluation->title,
            'description' => $evaluation->description,
            'questions' => $evaluation->questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'type' => $q->type,
                    'order' => $q->order,
                    'is_required' => true,
                    'options' => is_string($q->options) ? json_decode($q->options, true) : $q->options,
                ];
            })
        ]);
}

public function submitAnswers(Request $request)
{
    $data = $request->validate([
        'evaluation_id' => 'required|exists:evaluation,id',
        'responses' => 'required|array',
        'responses.*.question_id' => 'required|exists:evaluation_questions,id',
        'responses.*.answer' => 'nullable',
    ]);

    $today = Carbon::today()->toDateString();

    $event = \App\Models\Event::whereDate('event_date', $today)->first();

    if (!$event) {
        return response()->json(['error' => 'No event found for today.'], 404);
    }

    $studentId = Auth::user()->student_id;

    foreach ($data['responses'] as $resp) {
        $answer = is_array($resp['answer']) ? json_encode($resp['answer']) : $resp['answer'];

        \App\Models\EvaluationAnswer::create([
            'evaluation_id' => $data['evaluation_id'],
            'question_id' => $resp['question_id'],
            'student_id' => $studentId,
            'answer' => $answer,
            'event_id' => $event->id,
        ]);
    }

    // ✅ Update is_answered = true in Attendance
    \App\Models\Attendance::where('student_id', $studentId)
        ->where('event_id', $event->id)
        ->update(['is_answered' => true]);

    return response()->json(['message' => 'Answers submitted successfully.']);
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
