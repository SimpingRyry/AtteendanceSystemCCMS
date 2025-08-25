<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgList;
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
    $event = $assignment->event;
    $eventId = $assignment->event_id;

    // Actual respondents (who answered)
    $respondentCount = EvaluationAnswer::where('evaluation_id', $evaluationId)
        ->where('event_id', $eventId)
        ->distinct('student_id')
        ->count('student_id');

    // Step 1: Get the org associated with the event (using org name)
    $eventOrg = OrgList::where('org_name', $event->org)->first();

    // Step 2: Get org names (not IDs) — because User.org stores names
    $orgNames = [];

    if ($eventOrg) {
        $orgNames[] = $eventOrg->org_name; // Add the parent org name

        $childOrgNames = OrgList::where('parent_org_id', $eventOrg->id)
            ->pluck('org_name')
            ->toArray();

        if (!empty($childOrgNames)) {
            $orgNames = array_merge($orgNames, $childOrgNames);
        }
    }

    // Step 3: Count members by org name (not ID)
    $totalMembers = User::where('role', 'Member')
        ->whereIn('org', $orgNames)
        ->count();

    // Format the response text like 3/10 (30%)
    $respondentText = $totalMembers > 0
        ? "{$respondentCount}/{$totalMembers} (" . round(($respondentCount / $totalMembers) * 100, 2) . "%)"
        : "{$respondentCount}/0 (0%)";

    // Existing logic: compute question-wise summary
    $questions = EvaluationQuestion::where('evaluation_id', $evaluationId)->get();
    $summary = [];
    $questionAverages = [];

    foreach ($questions as $question) {
        $data = [
            'question' => $question->order . '. ' . $question->type,
            'type' => $question->type,
            'text' => $question->question ?? '',
            'responses' => [],
        ];

        if ($question->type === 'mcq' && $question->options) {
            $options = is_array($question->options) ? $question->options : [];

            $totalScore = 0;
            $totalResponses = 0;

            foreach ($options as $opt) {
                $optTrimmed = trim($opt);
                $count = EvaluationAnswer::where([
                        ['evaluation_id', $evaluationId],
                        ['event_id', $eventId],
                        ['question_id', $question->id],
                        ['answer', $optTrimmed],
                    ])->count();

                // Extract numeric value at the start (e.g. "5 - Excellent" -> 5)
                $numericValue = intval($optTrimmed);

                if ($numericValue > 0) {
                    $totalScore += $numericValue * $count;
                    $totalResponses += $count;
                }

                $data['responses'][] = [
                    'option' => $optTrimmed,
                    'count' => $count,
                ];
            }

            if ($totalResponses > 0) {
                $average = $totalScore / $totalResponses;
                $data['average'] = round($average, 2);
                $questionAverages[] = $average;
            } else {
                $data['average'] = null;
            }
        } else {
            $count = EvaluationAnswer::where([
                    ['evaluation_id', $evaluationId],
                    ['event_id', $eventId],
                    ['question_id', $question->id],
                ])->whereNotNull('answer')->count();

            $data['responses'][] = ['answered' => $count];
        }

        $summary[] = $data;
    }

    $overallRating = count($questionAverages) > 0
        ? round(array_sum($questionAverages) / count($questionAverages), 2)
        : null;

    return response()->json([
        'evaluation_title' => $assignment->evaluation->title,
        'event_name' => $event->name ?? 'N/A',
        'respondent_count' => $respondentCount,
        'respondent_text' => $respondentText,
        'overall_rating' => $overallRating,
        'summary' => $summary,
    ]);
}



}
