<?php

namespace App\Http\Controllers;

use App\Models\EvalAnswer;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\DB;
use App\Models\EvaluationAssignment;
use Illuminate\Support\Facades\Auth;

class StudentEvaluationController extends Controller
{
    /**
     * Show evaluations available to student.
     */
    public function index()
{
    $studentId = auth()->user()->student_id;

    // Step 1: Get attended events
    $attendedEventIds = DB::table('attendances')
        ->where('student_id', $studentId)
        ->pluck('event_id');

    // Step 2: Get latest EvaluationAssignment per event (with evaluation + event)
    $latestAssignments = \App\Models\EvaluationAssignment::with(['evaluation', 'event'])
        ->whereIn('event_id', $attendedEventIds)
        ->orderBy('created_at', 'desc')
        ->get()
        ->unique('event_id');

    return view('evaluation_student', [
        'assignments' => $latestAssignments // send full assignments, not just evaluations
    ]);
}

    /**
     * Return JSON data for a specific evaluation including its questions.
     */
    public function showJson($id)
    {
        $evaluation = Evaluation::with(['questions'])->findOrFail($id);

        return response()->json([
            'id' => $evaluation->id,
            'title' => $evaluation->title,
            'description' => $evaluation->description,
            'questions' => $evaluation->questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'type' => $q->type,
                    'options' => $q->options,
                    'order' => $q->order,
                    'is_required' => $q->is_required,
                ];
            })
        ]);
    }

    /**
     * Store student answers to an evaluation.
     */
    public function submitAnswers(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $data = $request->validate([
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:evaluation_questions,id',
            'responses.*.answer' => 'nullable'
        ]);

        foreach ($data['responses'] as $response) {
            EvalAnswer::create([
                'evaluation_id' => $evaluation->id,
                'question_id' => $response['question_id'],
                'student_id' => Auth::id(),
                'answer' => is_array($response['answer']) 
                            ? json_encode($response['answer']) 
                            : $response['answer'],
            ]);
        }

        return response()->json(['message' => 'Answers submitted successfully']);
    }
}
