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
                    Completed importing collectors.
                    <br>
                    Collectors Before Import: {{ $existTCCount }}
                    <br>
                    Collectors in file: {{ $totalRows }}
                    <br>
                    Collectors After Import: {{ $finalTCCount }}
                </div>
            </div>
            @if (count($missing))
                <div class="row row-centered">
                    <div class="col-md-6 form-group">
                        All the tax collectors in the following counties did not match an exiting county in the system.
                        (note: the county needs to be <i>exactly</i> the same as the exiting name.
                        @foreach ($missing as $item)
                            <br /> <strong>State:</strong> {{ $item->st }}  <strong>County:</strong> {{ $item->county }}
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

@stop