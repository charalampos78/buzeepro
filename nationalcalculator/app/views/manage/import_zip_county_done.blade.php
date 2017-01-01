@section('content')

    <div class="page-header">
        <h1>
            Import
        </h1>
    </div>

    <div class='row clearfix'>
        <div class='col-md-12'>

            <div class="row row-centered">
                <div class="col-md-6 form-group">
                    Completed importing file.
                    <br>
                    Imported Counties: {{ $countyCount }}
                    <br>
                    Imported Zips: {{ $zipCount }}
                </div>
            </div>

        </div>
    </div>

@stop