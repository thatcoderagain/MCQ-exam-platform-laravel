<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function takeTestAttempt(Request $request, Quiz $quiz, $questionNumber = 1)
    {
        // Check if there is already an existing active test
        if ($request->session()->exists('activeTest')) {
            // Check if the active test is from different quiz return from here
            if ($request->session()->get('activeTest.quizId') !== null &&
                $request->session()->get('activeTest.quizId') !== $quiz->id
            ) {
                return response()->json(['message' => 'You have a incomplete quiz test.'], 402);
            }
        } else {
            // If no active quiz then Start a fresh session for quiz test
            $request->session()->put('activeTest.startTime', Carbon::now());
            $request->session()->put('activeTest.endTime', Carbon::now()->addMinute($quiz->duration));
            $request->session()->put('activeTest.quizId', $quiz->id);
            $request->session()->put('activeTest.questions.attempted', []);
            $request->session()->put('activeTest.questions.marked', []);
            $request->session()->put('activeTest.questions.seen', []);
            $request->session()->put("activeTest.answers", []);
        }

        $questionNumber = $questionNumber-1;
        // If question number is less than 1 or more than total number of questions then abort with 404
        if ($questionNumber < 0 || $questionNumber + 1 > $quiz->questions()->count()) {
            abort(404);
        }

        // Fetch the start time of quiz
        $quiz['startTime'] = $request->session()->get('activeTest.startTime');
        $quiz['endTime']   = $request->session()->get('activeTest.endTime');
        $quiz['activeQuestionNumber'] = $questionNumber;

        $question = $quiz->getQuestion($questionNumber)->load('options');

        // Fetch status of the questions in quiz test
        $questionsStatus = $request->session()->get('activeTest.questions');
        $answer = $request->session()->get("activeTest.answers.{$question->id}", []);

        // If current question is no already in the seen array then push it and mark is as seen
        if (!in_array($question->id, $questionsStatus['seen'])) {
            $request->session()->push('activeTest.questions.seen', $question->id);
        }

        $questions = $quiz->questions
            ->map(function ($question) use ($questionsStatus) {
                if (in_array($question->id, $questionsStatus['marked'])) {
                    $question['status'] = "marked";
                } else if (in_array($question->id, $questionsStatus['attempted'])) {
                    $question['status'] = "attempted";
                } elseif (in_array($question->id, $questionsStatus['seen'])) {
                    $question['status'] = "seen";
                } else {
                    $question['status'] = "unseen";
                }
                return $question->only(['id', 'status']);
            });

        return view('quiz.attempt', compact('quiz', 'question', 'questions', 'answer', 'questionsStatus'));
    }

    public function saveTestAttempt(Request $request, Quiz $quiz)
    {
        // Check if there is already an existing active test
        if ($request->session()->exists('activeTest')) {
            // Check if the active test is from different quiz return from here
            if ($request->session()->get('activeTest.quizId') !== null &&
                $request->session()->get('activeTest.quizId') !== $quiz->id
            ) {
                return response()->json(['message' => 'You have a incomplete quiz test.'], 402);
            }
        }

        $request->validate([
            'questionId'           => ['required', 'numeric', 'exists:App\Models\Question,id'],
            'correctness'          => ['required', 'array'],
            'correctness.*'        => ['required'],
            'activeQuestionNumber' => ['required', 'numeric'],
            'submitMode'           => ['required', 'string', 'in:submit,mark'],
        ]);

        $questionId = $request->input('questionId');
        $answer     = $request->input('correctness');
        $activeQuestionNumber = $request->input('activeQuestionNumber');

        // Fetch status of the questions in quiz test
        $questionsStatus = $request->session()->get('activeTest.questions');

        // If current question is no already in the seen array then push it and mark is as seen
        if (!in_array($questionId, $questionsStatus['attempted'])) {
            $request->session()->push('activeTest.questions.attempted', $questionId);
        }

        $request->session()->put("activeTest.answers.{$questionId}", $answer);

        if ($quiz->questions()->count() > $activeQuestionNumber) {
            return redirect(route('take-test-attempt', [$quiz->id, $activeQuestionNumber+1]));
        }
        return "TODO SAVE TEST AND GENERATE RESULT";
    }
}
