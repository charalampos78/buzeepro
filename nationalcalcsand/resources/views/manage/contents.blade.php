@section('content')

	<div class="page-header">
      <h1>
		  Contents
		  {{ HTML::link('manage/content/add', "Add Content", ['class'=>"btn btn-default pull-right"]) }}
      </h1>
    </div>


    <table id="contents-list" class="table table-striped table-bordered"></table>

@stop