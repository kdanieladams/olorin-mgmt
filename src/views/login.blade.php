@extends('mgmt::master')

@section('main')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading login"><h3 class="panel-title">Login</h3></div>
                <div class="panel-body">
                    {!! Form::open(['url' => '/login']) !!}

                    {!! FormGroup::email('email', ['value' => (empty($email) ? '' : $email)]) !!}

                    {!! FormGroup::password('password') !!}

                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('remember', 1, false) !!}
                            Remember Me
                        </label>
                    </div>

                    <hr>

                    <div class="form-group">
                        {!! Form::submit('Login', ['class' => 'btn btn-primary form-control']) !!}
                    </div>
                    {!! Form::close() !!}
                    <a href="{{ url('/register') }}">...or Register, if you haven't yet.</a>
                </div>
            </div>
        </div>
    </div>
@stop