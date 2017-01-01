@section('content')

{{ MForm::model( $user, ['id'=>'form-account', 'action' => ['Controller\Api\UserApi@'.($user->exists?'putIndex':'postIndex') , $user->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
<div class="row row-centered">

	<div class="col-md-7 col-xs-12">
		{{ HTML::content('account_top') }}
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
				<div class="form-group">
					{{ MForm::text('email') }}
				</div>
				<div class="form-group">
					{{ MForm::text('username') }}
				</div>
			</div>
			<div class="col-sm-6 form-group">
				{{ MForm::password('password') }}
				<label for="user[password]" class="has-error" style="display: none;"></label>
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

