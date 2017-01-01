@section('content')

	<div class="page-header">
      <h1>
		  Users
		  {{ HTML::link('manage/user/add', "Add User", ['class'=>"btn btn-default pull-right"]) }}
      </h1>
    </div>


    <table id="users-list" class="table table-striped table-bordered"></table>

@stop