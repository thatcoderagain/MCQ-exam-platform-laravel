@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form method="POST" action="{{ route('quiz-store') }}">
                    @csrf
                    <div id="AddQuestion"></div>
                </form>
            </div>
        </div>
    </div>
@endsection
