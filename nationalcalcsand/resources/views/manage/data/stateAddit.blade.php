@section('inlineCSS')
    .row-type-lender {
        background-color:#EEE;
    }
@stop

@section('content')
    <? /** @var Models\State $state */ ?>
    <div class="page-header">
        <h1>
            State - {{ $state->name or "Adding" }}
        </h1>
    </div>

    {{ MForm::model( $state, ['id'=>'state-form', 'action' => ['Controller\Api\StateApi@'.($state->exists?'putIndex':'postIndex') , $state->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
    {{ MForm::hidden('state_id', $state->id, ['id'=>'state-id', 'name'=>'']) }}
    <div class='row clearfix'>
        <div class='col-md-12'>

            <div class="row">
                <div class="col-xs-8 form-group">
                    {{ MForm::text('name', null, ['readonly'=>'readonly']) }}
                </div>
                <div class="col-xs-4 form-group-sm">
                    {{ MForm::select('status_flag', [0=>'Disabled',1=>'Enabled']) }}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3 form-group">
                    {{ MForm::text('owner_min') }}
                    {{ MForm::text('owner_extra') }}
                </div>
                <div class="col-xs-3 form-group">
                    {{ MForm::text('owner_simultaneous') }}
                </div>
                <div class="col-xs-3 form-group">
                    {{ MForm::text('lender_min') }}
                    {{ MForm::text('lender_extra') }}
                </div>
                <div class="col-xs-3 form-group">
                    {{ MForm::text('lender_simultaneous') }}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-7">
                    <hr />
                    <h3>
                        Endorsements
                        <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="endorsement">Add</a>
                    </h3>
                    <div class="row">
                        <div id="endorsement-multi" class="multi-list col-xs-11 col-xs-push-1">
                            @foreach ($state->endorsements()->orderBy('standard_flag', 'DESC')->orderBy('name')->get() as $endorsement)
                                <div class="multi-item row">
                                    {{ MForm::hidden("endorsements.[$endorsement->id].deleted", null, ['class'=>'input-deleted']) }}
                                    <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                                        <label style="display:block;">&nbsp;</label>
                                        <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
                                    </div>
                                    <div class="col-xs-11">
                                        <div class="row">
                                            <div class="col-sm-1 col-xs-1 form-group-sm">
                                                {{ MForm::hidden("endorsements.[$endorsement->id].standard_flag", 0) }}
                                                {{ MForm::checkbox("endorsements.[$endorsement->id].standard_flag", 1, $endorsement->standard_flag, ['style'=>'padding:0;']) }}
                                            </div>
                                            <div class="col-sm-5 col-xs-5 form-group-sm">
                                                {{ MForm::text("endorsements.[$endorsement->id].name", $endorsement->name) }}
                                            </div>
                                            <div class="col-sm-3 col-xs-3 form-group-sm">
                                                {{ MForm::select("endorsements.[$endorsement->id].type", ['fixed'=>'Fixed', 'percent'=>'%'], $endorsement->type) }}
                                            </div>
                                            <div class="col-sm-3 col-xs-3 form-group-sm">
                                                {{ MForm::text("endorsements.[$endorsement->id].amount", $endorsement->amount) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-xs-12">
                            <br />
                            <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="endorsement">Add Endorsement</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <hr />
                    <h3>
                        Miscs
                        <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="misc">Add</a>
                    </h3>
                    <div class="row">
                        <div id="misc-multi" class="multi-list col-xs-11 col-xs-push-1">
                            @foreach ($state->miscs()->get() as $misc)
                                <div class="multi-item row">
                                    {{ MForm::hidden("miscs.[$misc->id].deleted", null, ['class'=>'input-deleted']) }}
                                    <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                                        <label style="display:block;">&nbsp;</label>
                                        <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
                                    </div>
                                    <div class="col-xs-11">
                                        <div class="row">
                                            <div class="col-xs-8 form-group-sm">
                                                {{ MForm::text("miscs.[$misc->id].name", $misc->name) }}
                                            </div>
                                            <div class="col-xs-4 form-group-sm">
                                                {{ MForm::text("miscs.[$misc->id].price", $misc->price) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-xs-12">
                            <br />
                            <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="misc">Add Misc</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <h3>
                Rates
                <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="rate">Add</a>
            </h3>
            <div class="row">
                <div id="rate-multi" class="multi-list col-xs-11 col-xs-push-1">
                    @foreach ($rates as $rate)
                        <div class="multi-item row row-type-{{ $rate->type }}">
                            {{ MForm::hidden("rates.[$rate->id].deleted", null, ['class'=>'input-deleted']) }}
                            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                                <label style="display:block;">&nbsp;</label>
                                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
                            </div>
                            <div class="col-xs-11">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3 form-group-sm">
                                        {{ MForm::text("rates.[$rate->id].range_min", $rate->range_min) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-3 form-group-sm">
                                        {{ MForm::text("rates.[$rate->id].range_max", $rate->range_max) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-2 form-group-sm">
                                        {{ MForm::text("rates.[$rate->id].percent", $rate->percent) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-2 form-group-sm">
                                        {{ MForm::text("rates.[$rate->id].extra", $rate->extra) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-2 form-group-sm">
                                        {{ MForm::select("rates.[$rate->id].type", ['owner'=>'Owner', 'lender'=>'Lender'], $rate->type) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12">
                    <br />
                    <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="rate">Add Rate</a>
                </div>
            </div>
            <hr />
            <h3>
                County Rates
                <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="rate-county">Add</a>
            </h3>
            <div class="row">
                <div id="rate-county-multi" class="multi-list col-xs-11 col-xs-push-1">
                    @foreach ($rate_counties as $rate)
                        <div class="multi-item row row-type-{{ $rate->type }}">
                            {{ MForm::hidden("rate_counties.[$rate->id].deleted", null, ['class'=>'input-deleted']) }}
                            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                                <label style="display:block;">&nbsp;</label>
                                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
                            </div>
                            <div class="col-xs-11">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-3 form-group-sm">
                                        {{ MForm::text("rate_counties.[$rate->id].range_min", $rate->range_min) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-3 form-group-sm">
                                        {{ MForm::text("rate_counties.[$rate->id].range_max", $rate->range_max) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-3 form-group-sm">
                                        {{ MForm::text("rate_counties.[$rate->id].percent", $rate->percent) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-3 form-group-sm">
                                        {{ MForm::text("rate_counties.[$rate->id].extra", $rate->extra) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-4 form-group-sm">
                                        {{ MForm::select("rate_counties.[$rate->id].type", ['owner'=>'Owner', 'lender'=>'Lender'], $rate->type) }}
                                    </div>
                                    <div class="col-sm-12 col-xs-8 form-group">
                                        {{ MForm::select2("rate_counties.[$rate->id].counties", $rate->counties()->orderBy('name')->lists('name','id'), ['class'=>'select2-county', 'multipleS2'=>true]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12">
                    <br />
                    <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="rate-county">Add County Rate</a>
                </div>
            </div>
            <hr />
            <div class="row" style="margin-top:60px;">
                <a href='{{URL::previous()}}' class='btn btn-default pull-right'>Cancel</a>

                {{ MForm::submit('Submit', array('class'=>'btn-primary')) }}
            </div>

        </div>



    </div>

    <script type="text/template" id="endorsement-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("endorsements.{0}.deleted", null, ['class'=>'input-deleted']) }}
            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-sm-1 col-xs-1 form-group-sm">
                        {{ MForm::hidden("endorsements.{0}.standard_flag", 0) }}
                        {{ MForm::checkbox("endorsements.{0}.standard_flag", 1, null, ['style'=>'padding:0;']) }}
                    </div>
                    <div class="col-sm-5 col-xs-5 form-group-sm">
                        {{ MForm::text("endorsements.{0}.name", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::select("endorsements.{0}.type", ['fixed'=>'Fixed', 'percent'=>'Percent'], "") }}
                    </div>
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::text("endorsements.{0}.amount", "") }}
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="misc-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("miscs.{0}.deleted", null, ['class'=>'input-deleted']) }}
            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-xs-8 form-group-sm">
                        {{ MForm::text("miscs.{0}.name", "") }}
                    </div>
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("miscs.{0}.price", "") }}
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="rate-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("rates.{0}.deleted", null, ['class'=>'input-deleted']) }}
            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::text("rates.{0}.range_min", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::text("rates.{0}.range_max", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-2 form-group-sm">
                        {{ MForm::text("rates.{0}.percent", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-2 form-group-sm">
                        {{ MForm::text("rates.{0}.extra", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-2 form-group-sm">
                        {{ MForm::select("rates.{0}.type", ['owner'=>'Owner', 'lender'=>'Lender'], "") }}
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/template" id="rate-county-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("rate_counties.{0}.deleted", null, ['class'=>'input-deleted']) }}
            <div class="col-xs-1 col-xs-push-11 form-group-sm" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right" tabindex="0"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::text("rate_counties.{0}.range_min", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-3 form-group-sm">
                        {{ MForm::text("rate_counties.{0}.range_max", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-3 form-group-sm">
                        {{ MForm::text("rate_counties.{0}.percent", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-3 form-group-sm">
                        {{ MForm::text("rate_counties.{0}.extra", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-4 form-group-sm">
                        {{ MForm::select("rate_counties.{0}.type", ['owner'=>'Owner', 'lender'=>'Lender'], "") }}
                    </div>
                    <div class="col-sm-12 col-xs-8 form-group">
                        {{ MForm::select2("rate_counties.{0}.counties", [], ['class'=>'select2-county', 'multipleS2'=>true]) }}
                    </div>
                </div>
            </div>
        </div>
    </script>

    {{ MForm::close() }}


@stop
