@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <form id="TestAttemptForm" method="POST" action="{{ route('save-attempt', [request()->quiz->id]) }}">
                @csrf
                <div class="col-12 row" id="Quiz"
                     data-quiz="{{$quiz}}"
                     data-questions='@json($questions)'
                     data-question='@json($question)'
                     data-answer='@json($answer)'
                     data-questions-status='@json($questionsStatus)'
                     data-attempted-questions='@json($questionsStatus['attempted'])'
                     data-marked-questions='@json($questionsStatus['marked'])'
                     data-seen-questions='@json($questionsStatus['seen'])'
                >
                </div>
            </form>
        </div>
    </div>
@endsection
