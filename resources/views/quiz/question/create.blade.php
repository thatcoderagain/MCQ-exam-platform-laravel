@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form id="AddQuestionForm" method="POST" action="{{ route('quiz-question-store', [request()->quiz->id]) }}">
                    @csrf
                    <div id="AddQuestion">
                        @include('layouts.validationErrorBlock')
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
