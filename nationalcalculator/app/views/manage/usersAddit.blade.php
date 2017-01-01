@section('content')

	<div class="page-header">
      <h1>
		  User - {{ $user->username or "Adding" }}
      </h1>
    </div>

	{{ MForm::model( $user, ['id'=>'user-form', 'action' => ['Controller\Api\UserApi@'.($user->exists?'putIndex':'postIndex') , $user->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}

	<!-- lousy hack to prevent chrom from autofilling user form fields since it ignore the autocomplete off thing -->
	<input style="display:none" type="text" name="username_fake"/>
	<input style="display:none" type="password" name="password_fake"/>

	<div class='row clearfix'>
		<div class='col-md-12'>

			<div class="row">
				<div class="col-md-6">
					{{ MForm::text('profile.first_name') }}
				</div>
				<div class="col-md-6">
					{{ MForm::text('profile.last_name') }}
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					{{ MForm::text('username') }}
				</div>
				<div class="col-md-6">
					{{ MForm::text('email') }}
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					{{ MForm::select('roles', "name", null, array('multiple', 'class'=>"select2-init select2")) }}
				</div>
				<div class="col-md-6">
					{{ MForm::password('password') }}
				</div>
			</div>

            <hr />
			<div class="row" style="margin-top:10px;">
				<a href='{{URL::previous()}}' class='btn btn-default pull-right'>Cancel</a>

				{{ MForm::submit('Submit', array('class'=>'btn-primary')) }}
			</div>

		</div>
	</div>
	{{ MForm::close() }}


@stop