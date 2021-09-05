@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @auth
            <div class="col-md-12">
                <div class="row">
                    <div class="col text-right">
                        <a href="/quiz/create" class="btn-link">Create Quiz</a>
                    </div>
                </div>
            </div>
        @endauth
        <div class="col-md-12">
            <div class="row">
                <div class="col-4">
                    <div class="list-group" id="quiz-tab" role="tablist">
                        @php($active = 'active')
                        @foreach($quizzes as $quiz)
                            <a class="list-group-item list-group-item-action {{$active}}" id="{{$quiz->id}}" data-toggle="list" href="#quiz_{{$quiz->id}}" role="tab" aria-controls="home">{{$quiz->title}}</a>
                            @php($active = '')
                        @endforeach
                    </div>
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
                                        <span class="card-link text-muted">Posted at {{$quiz->created_at->diffForHumans()}}</span>
                                        <a class="card-link" href="#">Take quiz</a>
                                        <a class="card-link" href="#">More Details</a>
                                    </div>
                                </div>
                            </div>
                            @php($active = '')
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
