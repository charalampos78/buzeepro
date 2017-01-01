@section('content')

    <div class="page-header">
        <h1>
            Zip - {{ $zip->zip or "Adding" }}
        </h1>
    </div>

    {{ MForm::model( $zip, ['id'=>'zip-form', 'action' => ['Controller\Api\ZipApi@'.($zip->exists?'putIndex':'postIndex') , $zip->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
    {{ MForm::hidden('zip_id', $zip->id, ['id'=>'zip-id', 'name'=>'']) }}
    <div class='row clearfix'>
        <div class='col-md-12'>

            <div class="row">
                <div class="col-xs-6 form-group">
                    {{ MForm::text('zip', null, ['id'=>'zip-zip']) }}
                </div>
                <div class="col-xs-6 form-group">
                    {{ MForm::select('status_flag', [0=>'Disabled',1=>'Enabled']) }}
                </div>
                <div class="col-xs-6 form-group">
                    {{ MForm::select('state_id', [null=>null]+Models\State::lists('name', 'id'), null, ['id'=>'state-id']) }}
                </div>
                <div class="col-xs-6 form-group">
                    {{ MForm::select2('county_id', [$zip->county_id=>$zip->county->name], ['class'=>"select2-county ".($zip->county_id?"":"hide")]) }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-xs-6 form-group-sm">
                    {{ MForm::select('primary_county', [0=>'No',1=>'Yes']) }}
                </div>
                <div class="col-sm-4 col-xs-6 form-group-sm">
                    {{ MForm::select('multi_county', [0=>'No',1=>'Yes']) }}
                </div>
                <div class="col-sm-4 form-group">
                    {{ MForm::text('city') }}
                </div>
            </div>

            <hr />
            <div class="row">
                <a href='{{URL::previous()}}' class='btn btn-default pull-right'>Cancel</a>

                {{ MForm::submit('Submit', array('class'=>'btn-primary')) }}
            </div>

            <div id="extra-zips">
                @include('manage.data.zipExtra')
            </div>


        </div>
    </div>
    {{ MForm::close() }}


@stop