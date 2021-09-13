<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    public function takeTestAttempt(Request $request, $quizId, $questionNumber = 1)
    {
        if (Session::has('currentTest') && Session::get('currentTest') != $quizId) {
            return "Please finish your active test first";
        }

        # TODO:: make log
//        return Carbon::now();

        Session::put('currentTest', $quizId);
        $attemptedQuestions = Session::get('attemptedQuestions', []);
        $markedForReviewQuestions = Session::get('markedForReviewQuestions', []);
        $seenQuestions = Session::get('seenQuestions', []);

        $quiz = Quiz::findOrFail($quizId);

        $questionNumber = $questionNumber-1;
        // If question number is less than 1 or more than total number of questions then abort with 404
        if ($questionNumber < 0 || $questionNumber + 1 > $quiz->questions()->count()) {
            abort(404);
        }

        $question = Question::with('options')
            ->where('quiz_id', $quizId)
            ->offset($questionNumber)->firstOrFail();

        if (!in_array($question->id, $seenQuestions)) {
            Session::put('seenQuestions', array_merge($seenQuestions, [$question->id]));
        }

        $questions = $quiz->questions
            ->map(function ($question)
                use ($attemptedQuestions, $seenQuestions, $markedForReviewQuestions) {
                if (in_array($question->id, $attemptedQuestions)) {
                    $question['status'] = "attempted";
                } elseif (in_array($question->id, $markedForReviewQuestions)) {
                    $question['status'] = "marked";
                } elseif (in_array($question->id, $seenQuestions)) {
                    $question['status'] = "seen";
                } else {
                    $question['status'] = "unseen";
                }
                $question = $question->only(['id', 'title', 'status']);
                return $question;
            });

        return view('quiz.attempt',
            compact('quizId', 'questions', 'question', 'questionNumber',
            'attemptedQuestions', 'markedForReviewQuestions', 'seenQuestions'));
    }

    public function saveTestAttempt(Request $request)
    {
        return $request->all();
    }
}
