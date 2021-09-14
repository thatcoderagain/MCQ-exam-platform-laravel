@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <form id="TestAttemptForm" method="POST" action="{{ route('save-attempt', [request()->quiz->id]) }}">
                @csrf
                <div class="col-12 row" id="Quiz"
                     data-quiz-id="{{$quiz->id}}"
                     data-selected-question='{{$questionNumber}}'
                     data-questions='@json($questions)'
                     data-question='@json($question)'
                     data-attempted-questions='@json($attemptedQuestions)'
                     data-marked-for-review-questions='@json($markedForReviewQuestions)'
                     data-seen-questions='@json($seenQuestions)'
                >
                </div>
            </form>
        </div>
    </div>
@endsection
