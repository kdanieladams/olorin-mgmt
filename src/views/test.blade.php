<label for="testing">Testing Stuff:</label>
{{ Form::input('text', 'testing', 'habooboolaba') }}

@if(Auth::guest())
    <p>You're a guest</p>
@else
    <p>You're logged in</p>
@endif