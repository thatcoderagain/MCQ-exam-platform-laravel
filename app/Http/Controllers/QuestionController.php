<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param Quiz $quiz
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request, Quiz $quiz)
    {
        Gate::authorize('update-quiz', $quiz);

        return view('quiz.question.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Quiz $quiz
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, Quiz $quiz)
    {
        Gate::authorize('update-quiz', $quiz);

        $request->validate([
            'title'      => ['required', 'string', 'min:10', 'max:255'],
            'optionType'    => ['required', 'string', 'in:radio,checkbox'],
            'options'       => ['required', 'array'],
            'options.*'     => ['required', 'string', 'distinct'],
            'correctness'   => ['required', 'array'],
            'correctness.*' => ['required'],
            'saveMode'      => ['required', 'string', 'in:finish,add_more'],
        ]);

        DB::beginTransaction();
        try {
            $question = Question::create([
                'quiz_id'     => $quiz->id,
                'title'    => $request->input('title'),
                'option_type' => $request->input('optionType'),
            ]);

            $option      = $request->input('options');
            $correctness = $request->input('correctness');
            $saveMode    = $request->input('saveMode');

            collect($option)
                ->map(function ($option, $index) use ($question, $correctness) {
                    Option::create([
                        'question_id' => $question->id,
                        'title'       => $option,
                        'correctness' => in_array($index, $correctness) ? 'correct' : 'incorrect',
                    ]);
                });

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        if ($saveMode === 'finish') {
            return response()->redirectToRoute('quiz-list');
        }
        return response()->redirectTo("/quiz/{$quiz->id}/question/add");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }
}
