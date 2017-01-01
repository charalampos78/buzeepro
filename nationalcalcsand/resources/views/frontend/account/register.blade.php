@section('content')

{{ MForm::model( $user, ['id'=>'form-register', 'action' => ['Controller\Api\UserApi@'.($user->exists?'putIndex':'postIndex') , $user->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
<div class="row row-centered">

	<div class="col-md-7 col-xs-12">
		{{ HTML::content('register_top') }}
	</div>
	<div class="col-md-6 col-xs-11">
		<div class="row">
			<div class="col-sm-6 form-group">
				{{ MForm::text('profile.first_name') }}
			</div>
			<div class="col-sm-6 form-group">
				{{ MForm::text('profile.last_name') }}
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 form-group">
				{{ MForm::text('email') }}
			</div>
			<div class="col-sm-6 form-group">
				{{ MForm::password('password') }}
				<label for="user[password]" class="has-error" style="display: none;"></label>
			</div>
		</div>

		<div class="row">
			<label>Terms</label>
			<div class="col-md-12">
				<div class="register-terms">
					<div>
						{{ HTML::content('register_terms') }}
					</div>
				</div>
            </div>
            <div class="col-sm-6 form-group">
                <label>{{ Form::checkbox('user[terms]') }} I agree to the terms</label>
				<br />
				<span id="user[terms]-error" class="error has-error"></span>
			</div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-md-12">
			{{ MForm::submit('Continue', array('class'=>'btn-primary form-control')) }}
			</div>
		</div>

	</div>
</div>
{{ MForm::close() }}

@stop
