@section('content')

	<div class="page-header">
      <h1>
		  Content - {{ $content->contentname or "Adding" }}
      </h1>
    </div>

	{{ MForm::model( $content, ['id'=>'content-form', 'action' => ['Controller\Api\ContentApi@'.($content->exists?'putIndex':'postIndex') , $content->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
	{{ MForm::hidden('locked_flag') }}

	<div class='row clearfix'>
		<div class='col-md-12'>

			<div class="row">
				<div class="col-md-6 form-group">
					{{ MForm::text('key') }}
				</div>
				<div class="col-md-6 form-group">
					{{ MForm::text('name') }}
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 form-group">
					{{ MForm::textarea('content', null, ['class'=>'ckeditor']) }}
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