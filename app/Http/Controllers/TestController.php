<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    const PAGE_LIMIT = 10;

    /**
     * Methods shows the quiz page with specific question with synced quiz state with the session.
     *
     * @param Request $request
     * @param Quiz $quiz
     * @param int $questionNumber
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function takeTestAttempt(Request $request, Quiz $quiz, $questionNumber = 1)
    {
        // Check if there is already an existing active test
        if ($request->session()->exists('activeTest')) {
            // Check if the active test is from different quiz return from here
            if ($request->session()->get('activeTest.quizId') !== null &&
                $request->session()->get('activeTest.quizId') !== $quiz->id
            ) {
                // Redirect user to already running quiz
                $quizId = $request->session()->get('activeTest.quizId');
                return redirect()->route('take-test-attempt', [$quizId, $questionNumber = 1]);
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

        // Submit test if time is over
        if ($request->session()->exists('activeTest') && Carbon::createFromTimeString($quiz['endTime']) < Carbon::now()) {
            return redirect()->action([TestController::class, 'submitTest'], ['quiz' => $quiz->id]);
        }

        $question = $quiz->getQuestion($questionNumber, ['id', 'title', 'option_type']);

        // Adding options with question
        $question['options'] = $question->options()->get()->map(function ($option) {
            return $option->only(['id', 'title']);
        });

        // Fetch status of the questions in quiz test
        $questionsStatus = $request->session()->get('activeTest.questions');
        $answer = $request->session()->get("activeTest.answers.{$question->id}", []);

        // If current question is no already in the seen array then push it and mark is as seen
        if (!in_array($question->id, $questionsStatus['seen'])) {
            $request->session()->push('activeTest.questions.seen', $question->id);
        }

        $questions = $quiz->questions()->get()
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

        $quiz = $quiz->only(['id', 'startTime', 'endTime', 'activeQuestionNumber']);

        return view('quiz.test', compact('quiz', 'question', 'questions', 'answer', 'questionsStatus'));
    }

    /**
     * Method saves the quiz state in session during a test
     *
     * @param Request $request
     * @param Quiz $quiz
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
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
            'correctness'          => ['array'],
            'correctness.*'        => ['nullable', 'numeric'],
            'activeQuestionNumber' => ['required', 'numeric'],
            'submitMode'           => ['required', 'string', 'in:submit,mark,clear'],
        ]);

        $submitMode = $request->input('submitMode');
        $questionId = $request->input('questionId');
        $answer     = $request->input('correctness', []);
        $activeQuestionNumber = $request->input('activeQuestionNumber');

        // Fetch status of the questions in quiz test
        $questionsStatus = $request->session()->get('activeTest.questions');

        if ($submitMode == 'clear') {
            // Remove current question from attempted questions array
            $data = $request->session()->get('activeTest.questions.attempted');
            $this->removeValueIfExists($data, $questionId);
            $request->session()->put('activeTest.questions.attempted', $data);

            // Remove current question from marked questions array
            $data = $request->session()->get('activeTest.questions.marked');
            $this->removeValueIfExists($data, $questionId);
            $request->session()->put('activeTest.questions.marked', $data);

            // Update the answer for current question in session
            $request->session()->remove("activeTest.answers.{$questionId}");
        }

        // If current question is no already in the seen array then push it and mark is as seen
        elseif ($submitMode == 'submit' && !in_array($questionId, $questionsStatus['attempted'])) {
            $request->session()->push('activeTest.questions.attempted', $questionId);

            // Remove current question from marked questions array
            $data = $request->session()->get('activeTest.questions.marked');
            $this->removeValueIfExists($data, $questionId);
            $request->session()->put('activeTest.questions.marked', $data);
        }

        // If current question is marked for review
        elseif ($submitMode == 'mark' && !in_array($questionId, $questionsStatus['marked'])) {
            $request->session()->push('activeTest.questions.marked', $questionId);
        }

        if ($submitMode == 'submit' || $submitMode == 'mark') {
            // Update the answer for current question in session
            $request->session()->put("activeTest.answers.{$questionId}", $answer);
        }

        if ($quiz->questions()->count() > $activeQuestionNumber) {
            return redirect(route('take-test-attempt', [$quiz->id, $activeQuestionNumber+1]));
        }
        return redirect(route('take-test-attempt', [$quiz->id, 1]));
    }

    /**
     * Function accepts an array and a value to loop-up and then remove it from the array
     * Optionally we can pass true|false whether we want to remove first occurrence or all.
     *
     * @param array $array
     * @param $value
     * @param bool $removeAllOccurrences
     */
    private function removeValueIfExists (array &$array, $value, $removeAllOccurrences = true)
    {
        if ($removeAllOccurrences) {
            $array = array_unique($array);
        }
        if (($key = array_search($value, $array)) !== false) {
            array_splice($array, $key, 1);
        }
    }

    public function submitTest(Request $request, Quiz $quiz)
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
            return redirect()->route('quiz-list');
        }

        $test = $request->session()->get('activeTest');
        $testResult = $this->evaluateTest($quiz, $test);

        // Clear test attempt from session
        $request->session()->remove('activeTest');

        return redirect()->route('test-result', [$testResult->id]);
    }

    private function evaluateTest(Quiz $quiz, $test) {
        $answers = $test['answers'];
        $correct = 0;
        $incorrect = 0;
        $unattended = 0;

        $totalQuestions = $quiz->questions()->get()
            ->map(function ($question) use ($answers, &$correct, &$incorrect, &$unattended) {
                if (isset($answers[$question->id])) {
                    $totalCorrectionOptions = $question->options->where('correctness', 'correct')->count();

                    // checked wrong answers
                    $checkedIncorrectOptions = $question->options->where('correctness', 'incorrect')->whereIn('id', $answers[$question->id])->count();

                    // not-checked right answers
                    $notCheckedCorrectOptions = $question->options->where('correctness', 'correct')->whereNotIn('id', $answers[$question->id])->count();

                    // checked correct answers
                    $attemptedCorrectOptions = $question->options->where('correctness', 'correct')->whereIn('id', $answers[$question->id])->count();

                    // Total number of correct answers do not match with total number of options checked
                    if ($totalCorrectionOptions !== count($answers[$question->id])) {
                        $incorrect++;
                    }

                    elseif ($checkedIncorrectOptions > 0) {
                        $incorrect++;
                    }

                    elseif ($notCheckedCorrectOptions > 0) {
                        $incorrect++;
                    }

                    elseif ($totalCorrectionOptions === $attemptedCorrectOptions) {
                        $correct++;
                    }

                } else {
                    $unattended++;
                }
                return $question;
            })->count();

        return $test = Test::create([
            'user_id'    => auth()->id(),
            'quiz_id'    => $quiz->id,
            'total'      => $totalQuestions,
            'correct'    => $correct,
            'incorrect'  => $incorrect,
            'unattended' => $unattended,
            'answers'    => json_encode($answers),
            'created_at' => $test['startTime'] ?? Carbon::now(),   // Test start time of test
            'updated_at' => Carbon::now(),                         // Test Submission time
        ]);
    }

    /**
     * Methods shows the test result with graphical representation.
     *
     * @param Request $request
     * @param Test $test
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showTestResult(Request $request, Test $test)
    {
        return view('quiz.testResult', compact('test'));
    }

    /**
     * Methods shows the list of test results attempted by the user.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showListOfTestResults(Request $request)
    {
        $tests =  auth()->user()->tests()
            ->orderByDesc('id')
            ->paginate(self::PAGE_LIMIT);

        $test['data'] = $tests->map(function ($test) {
                $test['passed'] = (100 * ($test->correct/$test->total)) >= 50;
                $test['quiz_title'] = $test->quiz->title;
                $test = $test->only(['id', 'quiz_id', 'passed', 'quiz_title', 'updated_at']);
                return $test;
            });

        return view('quiz.testResultList', compact('tests'));
    }
}
