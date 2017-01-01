@section('content')

    {{ MForm::model( $notebook, ['id'=>'form-calculator', 'action' => ['Controller\Api\NotebookApi@'.($notebook->exists?'putIndex':'postIndex') , $notebook->id], 'autocomplete'=>'off', 'bootstrap'=>true] ) }}
    <div class="row row-centered">

        <div class="col-xs-12">
            {{ HTML::content('calculator-form') }}
        </div>
        <div class="col-xs-12 accordion-widget" id="accordion">
            <h3>Notebook Name</h3>
            <div id="location">
                <div class="row">
                    <div class="col-sm-12 form-group ">
                        {{ MForm::text('name', null, ['label'=>false]) }}
                        <small style="color: #999;">This is an optional field to be able to find and reference this notebook later</small>
                    </div>
                </div>
            </div>
            <h3>Location Info</h3>
            <div id="location">
                <div class="row">
                    <div class="col-sm-6 form-group ">
                        {{ MForm::select2('zip_id', [$notebook->zip_id=>$notebook->zip->zip], ['labelName'=>"Zip Code", "placeholder"=>"Zip Code", 'class'=>"select2 select2-zip"]) }}
                    </div>
                    <div class="col-sm-6 form-group county-group {{ ($notebook->county_id) ? "" : "hide" }}">
                        {{ MForm::select2('county_id', [$notebook->county_id=>$notebook->county->name], ['labelName'=>"County", "placeholder"=>"County", 'class'=>"select2 select2-county"]) }}
                    </div>
                </div>
                <div id="countyNote">
                    @include('members.calcNote')
                </div>
            </div>
            <h3>Policy Info</h3>
            <div id="loan">
                <div class="row">
                    <div class="col-sm-12 form-group">
                        {{ MForm::radioGroup('type', ['purchase', 'cash', 'refinance'], null, ['class'=>'loan-type']) }}
                        <span id="notebook[type]-error" class="error has-error"></span>
                        <label id="notebook[type]-error" class="error has-error" for="notebook[type]"></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 form-group" id="purchase-price">
                        {{ MForm::text('purchase_price') }}
                    </div>
                    <div class="col-sm-6 form-group" id="loan-amount">
                        {{ MForm::text('loan_amount') }}
                    </div>
                </div>
            </div>

            @if ( $user->hasRole('admin') || ($user->onPlan(Models\User::PLANS[2]['code'])) )
            <h3>Documents</h3>
            <div id="documents">
                @include('members.calcDocs')
            </div>
            @endif

            <h3>Endorsements</h3>
            <div id="endorsements">
                @include('members.calcEndorsements')
            </div>

            @if ( $user->hasRole('admin') || ($user->onPlan(Models\User::PLANS[2]['code'])) )
            <h3>Estimated Additional Fees</h3>
            <div id="misc">
                @include('members.calcMisc')
            </div>
            @endif

            {{--<h3>Form Type</h3>--}}
            {{--<div id="type">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-12 form-group">--}}
                        {{--{{ MForm::radioGroup('output', ['hud'=>'HUD Settlement', 'gfe'=>'GFE Form'], null, ['key-as-val'=>true]) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

        </div>
        <div class="col-xs-12">
            <br />
            <br />
            <br />
            {{ MForm::submit('Continue', array('class'=>'btn-primary form-control')) }}
        </div>



    </div>
    {{ MForm::close() }}


@stop
