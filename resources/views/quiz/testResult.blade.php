@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div id="TestResult"
                 data-total="@json($test['total'])"
                 data-correct="@json($test['correct'])"
                 data-incorrect="@json($test['incorrect'])"
                 data-unattended="@json($test['unattended'])"
            >
            </div>
        </div>
    </div>
@endsection
