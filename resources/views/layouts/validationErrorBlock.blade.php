@if($errors->any())
    <div class="alert alert-danger">
        <h4>Oops!</h4>
        <ul>
            {!! implode('', $errors->all('<li>:message</li>')) !!}
        </ul>
    </div>
@endif
