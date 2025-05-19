<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvalAnswer;
use App\Models\UserProfile;
use App\Models\EvaluationQuestion;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /* -------------------------------------------------
       LIST
    --------------------------------------------------*/
    public function index()
    {
        $evaluations = Evaluation::latest()->get();   // or ->paginate(10)
        return view('evaluation', compact('evaluations'));
    }

    /* -------------------------------------------------
       CREATE
    --------------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'questions_json' => 'required|json'
        ]);

        DB::beginTransaction();

        try {
            $evaluation = Evaluation::create([
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
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
    }

    /* -------------------------------------------------
       AJAX for edit-modal
    --------------------------------------------------*/
    public function questions(Evaluation $evaluation)
    {
        return response()->json(
            $evaluation->load('questions')
        );
    }

    /* -------------------------------------------------
       UPDATE
    --------------------------------------------------*/
    public function update(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'questions_json' => 'required|json',
        ]);

        DB::beginTransaction();

        try {
            /* 1️⃣  update evaluation fields */
            $evaluation->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            $questions = json_decode($validated['questions_json'], true);

            /* 2️⃣  prune deleted rows */
            $submittedIds = collect($questions)->pluck('id')->filter()->all();
            EvaluationQuestion::where('evaluation_id', $evaluation->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();

            /* 3️⃣  upsert each question */
            foreach ($questions as $index => $q) {
                $data = [
                    'question'    => $q['question'],
                    'type'        => $q['type'] ?? 'text',
                    'options'     => $q['options'] ?? null,
                    'order'       => $index + 1,
                    'is_required' => !empty($q['is_required']),
                ];

                if (!empty($q['id'])) {
                    // update
                    EvaluationQuestion::where('id', $q['id'])->update($data);
                } else {
                    // insert
                    $data['evaluation_id'] = $evaluation->id;
                    EvaluationQuestion::create($data);
                }
            }

            DB::commit();

            // Return JSON success response for AJAX
            return response()->json([
                'status' => 'success',
                'message' => 'Evaluation and questions updated!'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
    }
    public function studentIndex()
{
    $evaluations = Evaluation::latest()->get();   // or whatever subset students should see
    return view('evaluation_student', [
        'evaluations' => $evaluations   // ← don’t forget to pass it
    ]);
}

    /**
     * AJAX – return evaluation + its questions as JSON.
     * Route:  GET /student/evaluation/{evaluation}/json
     */
    public function json(Evaluation $evaluation)
    {
        return response()->json($evaluation->load('questions'));
    }

    /**
     * Store a student's answers.
     * Route:  POST /student/evaluation/{evaluation}/answer
     *
     * Expected payload (from the modal):
     *  responses = [
     *     [ 'question_id' => 5, 'answer' => 'Yes' ],
     *     [ 'question_id' => 6, 'answer' => ['A','B'] ],   // checkbox array
     *     …
     *  ]
     */
    public function submitAnswers(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'responses'               => 'required|array|min:1',
            'responses.*.question_id' => 'required|integer|exists:evaluation_questions,id',
            'responses.*.answer'      => 'nullable',   // validation per-type happens below
        ]);

        // make sure all incoming q-ids belong to this evaluation (basic tamper-proofing)
        $validIds = $evaluation->questions()->pluck('id')->all();
        foreach ($request->responses as $resp) {
            if (! in_array($resp['question_id'], $validIds)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid question detected.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->responses as $resp) {
                $question = $evaluation->questions->firstWhere('id', $resp['question_id']);

                // Normalise checkbox array into comma-separated string
                $answer = $resp['answer'];
                if ($question->type === 'checkbox') {
                    $answer = is_array($answer) ? implode(',', $answer) : null;
                }

                EvalAnswer::create([
                    'evaluation_id' => $evaluation->id,
                    'question_id'   => $question->id,
                    'student_id'    => UserProfile::id(),
                    'answer'        => $answer,
                ]);
            }

            DB::commit();
            return response()->json(['status'=>'success'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
