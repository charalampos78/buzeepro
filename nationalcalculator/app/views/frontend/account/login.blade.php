@section('content')
	{{ Form::open(array('id' => 'form-login', 'url' => '/api/login')) }}
	<div class="row row-centered">

	<div class="col-md-7 col-xs-12">
		<h1>Login</h1>
	</div>
	<div class="col-md-6 col-xs-11">
        <fieldset>
            <div class="form-group">
            	{{ Form::label('login[email]', Lang::get('Username or Email')) }}
            	{{ Form::text('login[email]', Input::old('login[email]'), ['class'=>'form-control', 'placeholder'=>Lang::get('Username or Email')]) }}
            </div>

            <div class="form-group">
				{{ Form::label('login[password]', Lang::get('Password')) }}
				{{ Form::password('login[password]', ['class'=>'form-control', 'placeholder'=>Lang::get('Password')]) }}
            </div>

            <div class="form-group">
				{{ Form::hidden('login[remember]', 0) }}
				{{ Form::label('login[remember]', Lang::get('Remember')) }}
				&nbsp;&nbsp;&nbsp;
				{{ Form::checkbox('login[remember]', 1, true) }}
            </div>

            <div class="form-group">
            	{{ Form::submit(Lang::get('Log In'), ['class' => 'btn btn-default form-control']) }}
            </div>
			<div style="text-align: center">
				<small>
					<a id="show-forgot" href="javascript:void(0)">Forgot Password? Go here</a>
				</small>
			</div>

        </fieldset>
    </div>
    </div>
   	{{ Form::close() }}


	{{ Form::open(array('id' => 'form-forgot', 'url' => '/api/login/forgot', 'style' => 'display:none;')) }}
	<div class="row row-centered">

	<div class="col-md-7 col-xs-12">
		<h1>Recover Password</h1>
	</div>
	<div class="col-md-6 col-xs-11">
        <fieldset>
            <div class="form-group">
            	{{ Form::label('login[email]', Lang::get('Username or Email')) }}
            	{{ Form::text('login[email]', Input::old('login[email]'), ['class'=>'form-control', 'placeholder'=>Lang::get('Username or Email')]) }}
            </div>

        	<small>Please enter your username or email and we will send you an email with instructions on how to reset your password.</small>
        	<br/>
            <div class="form-group">
            	{{ Form::submit(Lang::get('Recover password'), ['class' => 'btn btn-default form-control']) }}
            </div>
			<div style="text-align: center">
				<small>
					<a id="show-login" href="javascript:void(0)">Go Back to Login</a>
				</small>
			</div>
		</fieldset>

		<div class="recover-message" style="display:none;">
			We have located your account and you should receive an email with instructions on resetting your password shortly.
		</div>

    </div>
    </div>
   	{{ Form::close() }}


@stop