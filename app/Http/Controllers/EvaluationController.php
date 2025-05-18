<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{

    public function index()
{
    $evaluations = Evaluation::latest()->get();   // or ->paginate(10)
    return view('evaluation', compact('evaluations'));
}


    /**
     * Show the evaluation form builder.
     */

    /**
     * Store the new evaluation with its questions.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions_json' => 'required|json'
        ]);

        DB::beginTransaction();

        try {
            $evaluation = Evaluation::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            $questions = json_decode($request->questions_json, true);

            foreach ($questions as $index => $q) {
                EvaluationQuestion::create([
                    'evaluation_id' => $evaluation->id,
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'order' => $index + 1,
                    'is_required' => isset($q['is_required']) ? 1 : 0,
                ]);
            }

            DB::commit();

            return redirect()
       ->back()               // or ->route('evaluation.index') if you prefer
       ->with('success', 'Evaluation created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function questions(Evaluation $evaluation)
    {
        $evaluation->load('questions');

        // return JSON so the JS can inject it in the modal
        return response()->json($evaluation);
    }
    

public function update(Request $request, Evaluation $evaluation)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'questions_json' => 'required|json',
    ]);

    DB::beginTransaction();

    try {
        // Update evaluation fields
        $evaluation->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        $questions = json_decode($validated['questions_json'], true);

        // Extract IDs of submitted questions that are existing
        $submittedIds = collect($questions)
            ->pluck('id')
            ->filter()
            ->all();

        // Delete questions that are NOT in submitted IDs
        EvaluationQuestion::where('evaluation_id', $evaluation->id)
            ->whereNotIn('id', $submittedIds)
            ->delete();

        // Upsert questions (update existing or create new)
        foreach ($questions as $index => $q) {
            if (isset($q['id']) && $q['id']) {
                // Update existing
                EvaluationQuestion::where('id', $q['id'])
                    ->update([
                        'question' => $q['question'],
                        'type' => $q['type'] ?? 'text',  // adapt if needed
                        'order' => $index + 1,
                        'is_required' => isset($q['is_required']) ? 1 : 0,
                    ]);
            } else {
                // New question
                EvaluationQuestion::create([
                    'evaluation_id' => $evaluation->id,
                    'question' => $q['question'],
                    'type' => $q['type'] ?? 'text',
                    'order' => $index + 1,
                    'is_required' => isset($q['is_required']) ? 1 : 0,
                ]);
            }
        }

        DB::commit();

        return redirect()
            ->route('evaluation.index')
            ->with('success', 'Evaluation and questions updated!');

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}


}

