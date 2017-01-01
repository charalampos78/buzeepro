@section('content')

    <div class="page-header">
        <h1>
            Import
        </h1>
    </div>


    <div class='row clearfix'>
        <div class='col-md-12'>

            <div class="row row-centered">
                <h3>Zips</h3>
                <div class="col-md-6 form-group">
                    Please select a csv file with no header and the following columns in the corresponding order:
                    <br>
                    zip, city, percent zip, county, state abbr, state
                </div>
                <div class="col-md-6 form-group">
                    {{ MForm::open( ['id'=>'import-zip-county-form', 'action' => ['Controller\Manage\ImportController@postZipCounty'], 'files'=>true, 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
                        {{ MForm::file('CSV', ['class'=> ($errors->has('CSV')?" has-error ":""), 'labelName'=>"Zip CSV"  ]) }}
                        @if ($errors->has('CSV')) <p class="help-block">{{ $errors->first('CSV') }}</p> @endif

                        {{ MForm::submit('Import', array('class'=>'btn-primary pull-right')) }}
                    {{ MForm::close() }}
                </div>
            </div>
            <hr />
            <div class="row row-centered">
                <h3>Tax Collector</h3>
                <div class="col-md-6 form-group">
                    Please select a csv file with no header and the following columns in the corresponding order:
                    <br>
                    <br>
                    'county', 'state', 'office', 'municipality', 'commissioner', 'address', 'city', 'state', 'zip', 'email', 'phone', 'fax', 'paysite', 'commissioner unused', 'mail_address', 'mail_city', 'mail_state', 'mail_zip', 'phone2', 'fax2', 'website'
                    <br>
                    <br>
                    Please note, that unlike zip, this must be a complete list.  When it is uploaded, all existing tax collector data will be purged, and replaced by whatever exists in the new file.
                </div>
                <div class="col-md-6 form-group">
                    {{ MForm::open( ['id'=>'import-collector-form', 'action' => ['Controller\Manage\ImportController@postTaxCollectors'], 'files'=>true, 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
                        {{ MForm::file('CSV', ['class'=> ($errors->has('CSV')?" has-error ":""), 'labelName'=>"Tax Collector CSV"  ]) }}
                        @if ($errors->has('CSV')) <p class="help-block">{{ $errors->first('CSV') }}</p> @endif

                        {{ MForm::submit('Import', array('class'=>'btn-primary pull-right')) }}
                    {{ MForm::close() }}
                </div>
            </div>
            <hr />


            <div class="row" style="margin-top:10px;">
                <a href='{{URL::previous()}}' class='btn btn-default pull-right'>Cancel</a>


            </div>

        </div>
    </div>




@stop