<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    const PAGE_LIMIT = 10;

    public function fullList(Request $request)
    {
        $quizzes = Quiz::with('user:id,name')
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    public function myList(Request $request)
    {
        $quizzes = Quiz::with('user:id,name')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    public function userList(Request $request, User $user)
    {
        $quizzes = Quiz::with('user:id,name')
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Request $request, Quiz $quiz)
    {
        $quizzes = Quiz::with('user')
            ->where('id', $quiz->id)
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);
        return view('quiz.list')->with('quizzes', $quizzes);
    }

    public function updateNotification(Request $request, Quiz $quiz)
    {
        return $this->update($request, $quiz, ['notification_status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Quiz $quiz
     * @param array $columnsToBeUpdated
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request, Quiz $quiz, array $columnsToBeUpdated = [])
    {
        if (!count($columnsToBeUpdated)) {
            $columnsToBeUpdated = ['title', 'description', 'duration', 'user_id'];
        }

        $request->validate([
            'title'       => [Rule::requiredIf(function () use ($columnsToBeUpdated) {
                return in_array('title', $columnsToBeUpdated);
            }), 'string', 'min:10', 'max:255'],
            'description' => [Rule::requiredIf(function () use ($columnsToBeUpdated) {
                return in_array('description', $columnsToBeUpdated);
            }), 'string', 'min:10'],
            'duration'    => [Rule::requiredIf(function () use ($columnsToBeUpdated) {
                return in_array('duration', $columnsToBeUpdated);
            }), 'numeric', 'min:5', 'max:180'],
            'notification_status' => [Rule::requiredIf(function () use ($columnsToBeUpdated) {
                return in_array('notification_status', $columnsToBeUpdated);
            }), 'in:on,off'],
        ]);

        if (in_array('title', $columnsToBeUpdated)) {
            $quiz->title = $request->input('title');
        }

        if (in_array('duration', $columnsToBeUpdated)) {
            $quiz->duration = $request->input('duration');
        }

        if (in_array('notification_status', $columnsToBeUpdated)) {
            $quiz->notification_status = $request->input('notification_status');
        }

        if ($quiz->isDirty()) {
            $quiz->save();
        }
        return redirect()->back();
    }
}
