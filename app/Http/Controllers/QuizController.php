<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    const PAGE_LIMIT = 10;

    public function fullList(Request $request)
    {
        $quizzes = Quiz::with('user')
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    public function myList(Request $request)
    {
        $quizzes = Quiz::with('user')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    public function userList(Request $request, User $user)
    {
        $quizzes = Quiz::with('user')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'duration'    => ['required', 'numeric', 'min:5', 'max:180']
        ]);

        $quiz = Quiz::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'duration' => $request->input('duration'),
            'user_id' => auth()->id()
        ]);

        return response()->redirectTo("/quiz/{$quiz->id}/question/add");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Quiz $quiz)
    {
        $quizzes = Quiz::with('user')
            ->where('id', $quiz->id)
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        //
    }
}
