@section('content')

	<div class="page-header">
      <h1>
		  Zips
          {{ HTML::link('manage/data/zip-add', "Add Zip", ['class'=>"btn btn-default pull-right"]) }}
      </h1>
    </div>

    <table id="zips-list" class="table table-striped table-bordered" style="width:100% !important;"></table>

@stop