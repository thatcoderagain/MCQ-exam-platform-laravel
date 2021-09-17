@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="justify-content-center">
            <form id="TestAttemptForm" method="POST" action="{{ route('save-attempt', [request()->quiz->id]) }}">
                @csrf
                <div class="container-fluid" id="Quiz"
                     data-quiz='@json($quiz)'
                     data-questions='@json($questions)'
                     data-question='@json($question)'
                     data-answer='@json($answer)'
                     data-questions-status='@json($questionsStatus)'
                >
                </div>
            </form>
        </div>
    </div>
@endsection
