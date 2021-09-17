@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="row">
                    @if ($tests->count() === 0)
                        <div class="alert alert-danger col-8 offset-2 mt-4">
                            <h4>Oops!</h4>
                            <ul>
                                <li>No records found!</li>
                            </ul>
                        </div>
                    @else
                        <div class="col-12">
                            <ul class="list-group list-group-flush">
                                @foreach($tests as $test)
                                    <li class="list-group-item p-0">
                                        <div class="alert alert-{{$test['passed'] ? 'success' : 'danger'}}" role="alert">
                                            <h4 class="alert-heading">
                                                <a href="{{ route('show-quiz', [$test['quiz_id']]) }}" class="mx-4 alert-link">{{ $test['quiz_title'] }}</a></h4>
                                            <hr>
                                            <p class="mb-0">
                                <span class="mx-4 badge badge-pill badge-{{$test['passed'] ? 'success' : 'danger'}} m-2">
                                    {{$test['passed'] ? 'Passed' : 'Failed'}}
                                </span>
                                                <a href="{{ route('test-result', [$test['id']]) }}" class="mx-4 alert-link">See full result</a>
                                                <span class="lead float-right">
                                    <strong>Attended</strong> {{ $test['updated_at']->diffForHumans() }}
                                </span>
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="d-flex justify-content-center">
                                {{ $tests->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
