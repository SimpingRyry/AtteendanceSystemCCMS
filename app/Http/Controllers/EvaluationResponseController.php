<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\Log;
use App\Models\EvaluationAssignment;
use Illuminate\Support\Facades\Auth;

class EvaluationResponseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $org = $user->org;

        // Get all evaluation assignments for events under this org
        $assignments = EvaluationAssignment::with(['evaluation', 'event'])
            ->whereHas('event', function ($query) use ($org) {
                $query->where('org', $org);
            })
            ->latest()
            ->get();

        return view('evaluation-responses', compact('assignments'));
    }

    public function show($evaluationId)
{
    $evaluation = Evaluation::findOrFail($evaluationId);

    // Get all questions for this evaluation
    $questions = EvaluationQuestion::where('evaluation_id', $evaluationId)
        ->orderBy('order')
        ->get();

    // Get all answers grouped by question
    $answers = EvaluationAnswer::where('evaluation_id', $evaluationId)
        ->get()
        ->groupBy('question_id');

    return response()->json([
        'title' => $evaluation->title,
        'questions' => $questions,
        'answers' => $answers,
    ]);
}


public function summary($assignmentId)
{
    $assignment = EvaluationAssignment::with(['event', 'evaluation'])->findOrFail($assignmentId);
    $evaluationId = $assignment->evaluation_id;
    $eventId = $assignment->event_id;
    Log::info('Evaluation ID: ' . $evaluationId);
    Log::info('Event ID: ' . $eventId);
   
    // Count distinct student respondents for this specific evaluation-event pair
    $respondentCount = EvaluationAnswer::where('evaluation_id', $evaluationId)
        ->where('event_id', $eventId)
        ->distinct('student_id')
        ->count('student_id');

    $questions = EvaluationQuestion::where('evaluation_id', $evaluationId)->get();
    $summary = [];

    foreach ($questions as $question) {
        $data = [
            'question' => $question->order . '. ' . $question->type,
            'type' => $question->type,
            'text' => $question->text ?? '',
            'responses' => [],
        ];

        if ($question->type === 'radio' && $question->options) {
            $options = explode(',', $question->options);

            foreach ($options as $opt) {
                $count = EvaluationAnswer::where([
                        ['evaluation_id', $evaluationId],
                        ['event_id', $eventId],
                        ['question_id', $question->id],
                        ['answer', trim($opt)],
                    ])
                    ->count();

                $data['responses'][] = [
                    'option' => trim($opt),
                    'count' => $count,
                ];
            }
        } else {
            $count = EvaluationAnswer::where([
                    ['evaluation_id', $evaluationId],
                    ['event_id', $eventId],
                    ['question_id', $question->id],
                ])
                ->whereNotNull('answer')
                ->count();

            $data['responses'][] = [
                'answered' => $count,
            ];
        }

        $summary[] = $data;
    }

    return response()->json([
        'evaluation_title' => $assignment->evaluation->title,
        'event_name' => $assignment->event->name ?? 'N/A',
        'respondent_count' => $respondentCount,
        'summary' => $summary,
    ]);
}

}
