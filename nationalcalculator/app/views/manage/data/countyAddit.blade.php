@section('inlineCSS')
    #document-multi h4 {
        margin-top:0;
    }
@append

@section('content')

    <div class="page-header cf">
        <h1 class="cf">
            County - {{ $county->name or "Adding" }}
            @if ($county->id)
            <a href="javascript:void(0)" class="btn btn-default pull-right" id="copy-county-toggle">Copy Docs To</a>
            @endif
        </h1>
        <div id="copy-county-box" class="row" style="display:none;">
            {{ MForm::open( ['id'=>'county-copy-form', 'action' => ['Controller\Api\CountyApi@postCopy', $county->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
            <div class="col-sm-6 col-sm-push-6 form-group">
                {{ MForm::select2("copy_counties", [], ['class'=>'select2-county', 'multipleS2'=>true, 'labelName'=>'Counties To Copy To:']) }}
                {{ MForm::submit('Copy Docs Now!', array('class'=>'btn-primary pull-right')) }}
                <div>
                    (note: This will delete all existing documents on counties selected.
                    This could have an effect on users who have a notebook saved with the number of pages of an existing document.)
                </div>
            </div>
            {{ MForm::close() }}
        </div>
    </div>

    {{ MForm::model( $county, ['id'=>'county-form', 'action' => ['Controller\Api\CountyApi@'.($county->exists?'putIndex':'postIndex') , $county->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}

    <div class='row clearfix'>
        <div class='col-md-12'>

            <div class="row">
                <div class="col-sm-4 form-group">
                    {{ MForm::text('name') }}
                </div>
                <div class="col-sm-4 col-xs-6 form-group">
                    {{ MForm::select('state_id', [null=>null]+Models\State::lists('name', 'id'), null, ['id'=>'state-id']) }}
                </div>
                <div class="col-sm-4 col-xs-6 form-group">
                    {{ MForm::select('status_flag', [0=>'Disabled',1=>'Enabled']) }}
                </div>
                <div class="col-xs-6 form-group">
                    {{ MForm::textarea('note', null, ['class'=>'ckeditor-simple']) }}
                    <small>(this will show up on calculator after county is selected)</small>
                </div>
            </div>
            <hr />
            <h3>
                Documents
                <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" data-multi="document">Add</a>
            </h3>

            <div class="row">
                <div id="document-multi" class="multi-list col-xs-11 col-xs-push-1">
                    @foreach ($county->documents()->get() as $document)
                        <div class="multi-item row">
                            {{ MForm::hidden("documents.[$document->id].deleted", null, ['class'=>'input-deleted']) }}

                            <div class="col-xs-1 col-xs-push-11" style="position: absolute;">
                                <label style="display:block;">&nbsp;</label>
                                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default pull-right"></a>
                            </div>
                            <div class="col-xs-11">
                                <div class="row">
                                    <div class="col-sm-3 col-xs-6 form-group">
                                        {{ MForm::text("documents.[$document->id].name", $document->name) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-6 form-group">
                                        {{ MForm::text("documents.[$document->id].price_text", $document->price_text ? : "First Page") }}
                                    </div>
                                    <div class="col-sm-2 col-xs-4 form-group">
                                        {{ MForm::text("documents.[$document->id].price_first", $document->price_first) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-4 form-group">
                                        {{ MForm::text("documents.[$document->id].price_count", $document->price_count) }}
                                    </div>
                                    <div class="col-sm-2 col-xs-4 form-group">
                                        {{ MForm::text("documents.[$document->id].price_additional", $document->price_additional) }}
                                    </div>
                                </div>
                                <h4>
                                    Tax / Fees
                                    <a href="javascript:void(0)" class="multi-add btn btn-default btn-xs"
                                       data-multi="document-tax"
                                       data-multi-postfix="-{{$document->id}}"
                                       data-multi-params='["{{$document->id}}"]'>
                                       Add
                                    </a>
                                </h4>
                                <div class="row">
                                    <div id="document-tax-{{$document->id}}-multi" class="multi-list col-xs-11 col-xs-push-1">
                                        @foreach ($document->taxes()->get() as $tax)
                                            <div class="multi-item row">
                                                {{ MForm::hidden("documents.[$document->id].taxes.[$tax->id].deleted", null, ['class'=>'input-deleted']) }}

                                                <div class="col-xs-1 col-sm-push-11 col-xs-push-12" style="position: absolute">
                                                    <label style="display:block;">&nbsp;</label>
                                                    <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right"></a>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="row">
                                                        <div class="col-xs-4 form-group-sm">
                                                            {{ MForm::text("documents.[$document->id].taxes.[$tax->id].name", $tax->name) }}
                                                        </div>
                                                        <div class="col-xs-4 form-group-sm">
                                                            {{ MForm::text("documents.[$document->id].taxes.[$tax->id].percent", $tax->percent) }}
                                                        </div>
                                                        <div class="col-xs-4 form-group-sm">
                                                            {{ MForm::select("documents.[$document->id].taxes.[$tax->id].type", ['loan'=>'Loan', 'sales'=>'Sales', 'fixed'=>'Fixed', 'sales-loan'=>'Sales-Loan'], $tax->type) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12">
                    <br />
                    <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" data-multi="document">Add Document</a>
                </div>
            </div>
            <hr />

            <h3>
                Tax Collector Info
                <a href="javascript:void(0)" class="multi-add btn btn-default btn-sm" tabindex="0" data-multi="tax-collector">Add</a>
            </h3>
            <div class="row">
                <div id="tax-collector-multi" class="multi-list col-xs-11 col-xs-push-1">
                    @foreach ($county->taxCollectors()->get() as $tc)
                        <div class="multi-item row">
                            {{ MForm::hidden("taxCollectors.[$tc->id].deleted", null, ['class'=>'input-deleted']) }}
                            <div class="col-xs-1 col-xs-push-11" style="position: absolute;">
                                <label style="display:block;">&nbsp;</label>
                                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default pull-right"></a>
                            </div>
                            <div class="col-xs-11">
                                <div class="row">
                                    <div class="col-xs-4 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].municipality", $tc->municipality) }}
                                    </div>
                                    <div class="col-xs-4 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].email", $tc->email) }}
                                    </div>
                                    <div class="col-xs-4 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].commissioner", $tc->commissioner) }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3 col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].phone", $tc->phone) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].phone2", $tc->phone2) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].fax", $tc->fax) }}
                                    </div>
                                    <div class="col-sm-3 col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].fax2", $tc->fax2) }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].website", $tc->website) }}
                                    </div>
                                    <div class="col-xs-6 form-group-sm">
                                        {{ MForm::text("taxCollectors.[$tc->id].paysite", $tc->paysite) }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row tax-address">
                                            <div class="col-xs-12 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].address", $tc->address) }}
                                            </div>
                                            <div class="col-xs-5 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].city", $tc->city) }}
                                            </div>
                                            <div class="col-xs-4 form-group-sm">
                                                {{ MForm::select("taxCollectors.[$tc->id].state", [null=>null]+Models\State::lists('name', 'abbr'), $tc->state) }}
                                            </div>
                                            <div class="col-xs-3 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].zip", $tc->zip) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row tax-mail">
                                            <div class="col-xs-12 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].m_address", $tc->m_address) }}
                                            </div>
                                            <div class="col-xs-5 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].m_city", $tc->m_city) }}
                                            </div>
                                            <div class="col-xs-4 form-group-sm">
                                                {{ MForm::select("taxCollectors.[$tc->id].m_state", [null=>null]+Models\State::lists('name', 'abbr'), $tc->m_state) }}
                                            </div>
                                            <div class="col-xs-3 form-group-sm">
                                                {{ MForm::text("taxCollectors.[$tc->id].m_zip", $tc->m_zip) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <hr />
            <div class="row" style="margin-top:60px;">
                <a href='{{URL::previous()}}' class='btn btn-default pull-right'>Cancel</a>

                {{ MForm::submit('Submit', array('class'=>'btn-primary')) }}
            </div>

        </div>
    </div>

    <script type="text/template" id="document-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("documents.{0}.deleted", null, ['class'=>'input-deleted']) }}

            <div class="col-xs-1 col-xs-push-11" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default pull-right"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-sm-3 col-xs-6 form-group">
                        {{ MForm::text("documents.{0}.name", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-6 form-group">
                        {{ MForm::text("documents.{0}.price_text", "First Page") }}
                    </div>
                    <div class="col-sm-2 col-xs-4 form-group">
                        {{ MForm::text("documents.{0}.price_first", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-4 form-group">
                        {{ MForm::text("documents.{0}.price_count", "") }}
                    </div>
                    <div class="col-sm-2 col-xs-4 form-group">
                        {{ MForm::text("documents.{0}.price_additional", "", ['labelName'=>'Price Add\'l', 'placeholder'=>'Price Add\'l']) }}
                    </div>
                </div>
                <h4>
                    Tax
                    <a href="javascript:void(0)" class="multi-add btn btn-default btn-xs" data-multi="document-tax" data-multi-postfix="-{0}" data-multi-params='["{0}"]'>Add</a>
                </h4>
                <div class="row">
                    <div id="document-tax-{0}-multi" class="multi-list col-xs-11 col-xs-push-1">
                    </div>
                </div>
            </div>
        </div>
    </script>


    <script type="text/template" id="document-tax-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("documents.{1}.taxes.{0}.deleted", null, ['class'=>'input-deleted']) }}

            <div class="col-xs-1 col-sm-push-11 col-xs-push-12" style="position: absolute">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default btn-sm pull-right"></a>
            </div>
            <div class="col-sm-11">
                <div class="row">
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("documents.{1}.taxes.{0}.name", "") }}
                    </div>
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("documents.{1}.taxes.{0}.percent", "") }}
                    </div>
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::select("documents.{1}.taxes.{0}.type", ['loan'=>'Loan', 'sales'=>'Sales', 'fixed'=>'Fixed', 'sales-loan'=>'Sales-Loan'], "") }}
                    </div>
                </div>
            </div>
        </div>
    </script>
    
    <script type="text/template" id="tax-collector-multi-template">
        <div class="multi-item row">
            {{ MForm::hidden("taxCollectors.{0}.deleted", null, ['class'=>'input-deleted']) }}
            <div class="col-xs-1 col-xs-push-11" style="position: absolute;">
                <label style="display:block;">&nbsp;</label>
                <a href="javascript:void(0)" class="multi-remove glyphicon glyphicon-trash btn btn-default pull-right"></a>
            </div>
            <div class="col-xs-11">
                <div class="row">
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.municipality", "") }}
                    </div>
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.email", "") }}
                    </div>
                    <div class="col-xs-4 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.commissioner", "") }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.phone", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.phone2", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.fax", "") }}
                    </div>
                    <div class="col-sm-3 col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.fax2", "") }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.website", "") }}
                    </div>
                    <div class="col-xs-6 form-group-sm">
                        {{ MForm::text("taxCollectors.{0}.paysite", "") }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row tax-address">
                            <div class="col-xs-12 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.address", "") }}
                            </div>
                            <div class="col-xs-5 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.city", "") }}
                            </div>
                            <div class="col-xs-4 form-group-sm">
                                {{ MForm::select("taxCollectors.{0}.state", [null=>null]+Models\State::lists('name', 'abbr'), null) }}
                            </div>
                            <div class="col-xs-3 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.zip", "") }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row tax-mail">
                            <div class="col-xs-12 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.m_address", "") }}
                            </div>
                            <div class="col-xs-5 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.m_city", "") }}
                            </div>
                            <div class="col-xs-4 form-group-sm">
                                {{ MForm::select("taxCollectors.{0}.m_state", [null=>null]+Models\State::lists('name', 'abbr'), null) }}
                            </div>
                            <div class="col-xs-3 form-group-sm">
                                {{ MForm::text("taxCollectors.{0}.m_zip", "") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>
    {{ MForm::close() }}

@stop

@section('styles')
    @parent
    <style>
        .multi-list > .multi-item {
            border-bottom:1px dashed #333;
            margin-bottom:10px;
        }
        #document-multi > .multi-item:last-child {
            border-width: 0;
        }
        #document-multi > .multi-item .multi-item {
            border-bottom:1px dashed #EEE;
            margin-bottom:2px;
            padding-bottom:6px;
        }
        #document-multi > .multi-item .multi-item:last-child {
            border-width: 0;
        }

        #tax-collector-multi > .multi-item {
            padding-bottom: 10px;
            border-width: 2px;
        }
        .tax-address {
            border: 1px solid #bec4ff;
            border-width: 1px 1px 0px 0px;
            margin-top: 5px;

        }
        .tax-mail {
            border: 1px solid #bec4ff;
            border-width: 1px 0px 0px 0px;
            margin-top: 5px;
        }

    </style>
@stop
