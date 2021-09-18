@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @auth
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col-md-6 col-sm-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('quiz-create') }}" class="btn btn-outline-primary">
                                    <i class="far fa-plus-square"></i>&nbsp;&nbsp;Create Quiz
                                </a>
                                <a href="{{ route('quiz-my-list') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i>&nbsp;&nbsp;My Quizzes
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                        <span class="float-right">
                            {{ $quizzes->links() }}
                        </span>
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
                        <div class="col-md-6 offset-5">
                            {{ $quizzes->links() }}
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
                        </div>
                        <div class="col-8">
                            <div class="tab-content" id="quiz-tabContent">
                                @php($active = 'show active')
                                @foreach($quizzes as $quiz)
                                    <div class="tab-pane fade {{$active}}" id="quiz_{{$quiz->id}}" role="tabpanel" aria-labelledby="{{$quiz->id}}">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="col">
                                                    <h3 class="card-title">{{$quiz->title}}</h3>
                                                    <h6 class="card-subtitle mb-3 text-muted">Duration: {{$quiz->duration}} minutes</h6>
                                                    <h5 class="card-subtitle my-2">Rules</h5>
                                                    <p class="card-text card-subtitle mb-3">
                                                        {{$quiz->description}}
                                                    </p>
                                                    <hr class="align-self-md-start">
                                                    <span class="card-link text-muted">
                                                        <b>Author :</b>
                                                        <a href="{{ route('quiz-user-list', [$quiz->user->id]) }}" class="btn btn-link">{{ ucwords($quiz->user->name) }}</a>
                                                    </span>

                                                    <br class="align-self-md-start">
                                                    <span class="card-link text-muted">
                                                        <b>Posted at :</b> {{$quiz->created_at->diffForHumans()}}
                                                    </span>
                                                    <div class="btn-group btn-group-sm float-right" role="group">
                                                        @if ($quiz->user->id === auth()->id())
                                                            <form method="POST" action="{{route('quiz-update-notification', [$quiz->id])}}">
                                                                @csrf
                                                                <input type="hidden" name="notification_status" value="{{ $quiz->notification_status === 'on' ? 'off' : 'on' }}">
                                                                <button type="submit" class="btn btn-sm btn-{{ $quiz->notification_status === 'off' ? 'light' : 'outline-danger' }}">
                                                                    <b>
                                                                        <i class="{{ $quiz->notification_status === 'on' ? 'far fa-bell-slash' : 'far fa-bell' }}"></i>
                                                                        Turn {{ $quiz->notification_status === 'on' ? 'off' : 'on' }} notification
                                                                    </b>
                                                                </button>
                                                            </form>
                                                            <a class="btn btn-outline-success" href="{{route('quiz-question-add', [$quiz->id])}}">
                                                                <b>
                                                                    <i class="far fa-plus-square"></i>
                                                                    Add questions
                                                                </b>
                                                            </a>
                                                        @endif
                                                        <a class="btn btn-outline-primary" href="{{route('take-test-attempt', [$quiz->id, $questionNumber = 1])}}">
                                                            <b>
                                                                <i class="fas fa-diagnoses"></i>
                                                                Take quiz
                                                            </b>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php($active = '')
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="d-flex justify-content-center">
                                {{ $quizzes->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
