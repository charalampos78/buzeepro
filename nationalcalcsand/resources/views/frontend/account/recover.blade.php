@section('content')

    {{ Form::open(array('id' => 'form-recover', 'url' => '/api/login/recover')) }}
    <div class="row row-centered">

    <div class="col-md-7 col-xs-12">
        <h1>Reset Password</h1>
    </div>
    <div class="col-md-6 col-xs-11">
        <fieldset>
            <p>Please enter your new password.</p>

            <div class="form-group">
                {{ Form::label('recover[password]', Lang::get('Password')) }}
                {{ Form::password('recover[password]', ['class'=>'form-control', 'placeholder'=>Lang::get('Password')]) }}
                {{ Form::hidden('recover[token]', $token) }}
            </div>

            <div class="form-group">
                {{ Form::submit(Lang::get('Update Password'), ['class' => 'btn btn-default form-control']) }}
            </div>
            <div style="text-align: center">
                <small>
                    <a href="{{URL::Route('login')}}">Go Back to Login</a>
                </small>
            </div>

        </fieldset>
    </div>
    </div>
       {{ Form::close() }}

@stop