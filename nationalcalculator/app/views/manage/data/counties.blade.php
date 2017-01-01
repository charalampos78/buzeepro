@section('content')

	<div class="page-header">
      <h1>
		  Counties
          {{ HTML::link('manage/data/county-add', "Add County", ['class'=>"btn btn-default pull-right"]) }}
      </h1>
    </div>

    <table id="counties-list" class="table table-striped table-bordered" style="width:100% !important;"></table>

@stop