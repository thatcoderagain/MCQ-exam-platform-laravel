@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form method="POST" action="{{ route('quiz-question-store', [request()->quizId]) }}">
                    @csrf
                    <div id="AddQuestion">
                        @include('layouts.validationErrorBlock')
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
