<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Show the evaluation form builder.
     */
    public function create()
    {
        return view('evaluation.create');
    }

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

            return redirect()->route('evaluation.create')->with('success', 'Evaluation saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }
}

