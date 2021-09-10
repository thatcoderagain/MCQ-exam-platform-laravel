@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @auth
            <div class="col-md-12">
                <div class="row">
                    <div class="col text-right mb-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('quiz-my-list') }}" class="btn btn-outline-primary">My Quizzes</a>
                            <a href="{{ route('quiz-create') }}" class="btn btn-outline-primary">Create Quiz</a>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        <div class="col-md-12">
            <div class="row">
                @if ($quizzes->count() === 0)
                    <div class="alert alert-danger col-8 offset-2 mt-4">
                        <h4>Oops!</h4>
                        <ul>
                            <li>No records found!</li>
                        </ul>
                    </div>
                @else
                    <div class="col-4">
                        <div class="list-group" id="quiz-tab" role="tablist">
                            @php($active = 'active')
                            @foreach($quizzes as $quiz)
                                <a class="list-group-item list-group-item-action {{$active}}" id="{{$quiz->id}}" data-toggle="list" href="#quiz_{{$quiz->id}}" role="tab" aria-controls="home">{{$quiz->title}}</a>
                                @php($active = '')
                            @endforeach
                        </div>
{{--                            {{$quizzes}}--}}
{{--                        <div class="btn-group" role="group">--}}
{{--                            @foreach($quizzes->links as $link)--}}
{{--                                <a href="{{ route('quiz-my-list') }}" class="btn btn-outline-primary">My Quizzes</a>--}}
{{--                                <a href="{{ route('quiz-create') }}" class="btn btn-outline-primary">Create Quiz</a>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
                    </div>
                    <div class="col-8">
                        <div class="tab-content" id="quiz-tabContent">
                            @php($active = 'show active')
                            @foreach($quizzes as $quiz)
                                <div class="tab-pane fade {{$active}}" id="quiz_{{$quiz->id}}" role="tabpanel" aria-labelledby="{{$quiz->id}}">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="card-title">{{$quiz->title}}</h3>
                                            <h6 class="card-subtitle mb-3 text-muted">Duration: {{$quiz->duration}} minutes</h6>
                                            <h5 class="card-subtitle my-2">Rules</h5>
                                            <p class="card-text card-subtitle mb-3">
                                                {{$quiz->description}}
                                            </p>
                                            <span class="card-link text-muted"><b>Author :</b>
                                                <a href="{{ route('quiz-user-list', [$quiz->user->id]) }}" class="btn btn-link">{{ ucwords($quiz->user->name) }}</a></span>
                                            <span class="card-link text-muted"><b>Posted at :</b> {{$quiz->created_at->diffForHumans()}}</span>
                                            <div class="btn-group btn-group-sm float-right" role="group">
                                                @if ($quiz->user->id === auth()->id())
                                                    <a class="btn btn-success" href="#"><b>Edit quiz</b></a>
                                                @endif
                                                <a class="btn btn-danger" href="#"><b>Take quiz</b></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php($active = '')
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
