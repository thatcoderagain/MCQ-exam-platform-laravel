@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('quiz-store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="quiz-title">Quiz Title</label>
                                <input type="text" class="form-control" id="quiz-title" name="title" required min="10"
                                       placeholder="Basics of C++ programming" aria-describedby="quiz-title-hint"
                                       value="{{request()->old('title') ?? ''}}">
                                <small id="quiz-title-hint" class="form-text text-muted">Provide a short descriptive quiz title.</small>
                            </div>
                            <div class="form-group">
                                <label for="quiz-description">Description</label>
                                <textarea class="form-control" id="quiz-description" name="description" rows="3" required
                                          placeholder="About the quiz...">{{request()->old('description') ?? ''}}</textarea>
                            </div>
                            <div class="row">
                                <div class="col input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Test Duration</span>
                                    </div>
                                    <input type="number" class="form-control" name="duration" min="5" max="1800" value="60" required
                                           placeholder="60" aria-describedby="quiz-duration-hint">
                                    <div class="input-group-append">
                                        <span class="input-group-text">minutes</span>
                                    </div>
                                </div>
                                <div class="col input-group">
                                    <button type="submit" class="btn btn-outline-primary">Proceed and add Questions</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
