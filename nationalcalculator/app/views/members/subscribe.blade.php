@section('inlineCSS')
    .extra-error {
        display: none;
    }
    .extra-error.has-error {
        display: block;
    }
@append
@section('inlineJS')
    <script>
        Stripe.setPublishableKey('{{ $stripePublic }}');
    </script>
@show
@section('content')

    {{ MForm::open( ['id'=>'form-subscribe', 'action' => 'Controller\Api\SubscribeApi@postIndex', 'bootstrap'=>true] ) }}
    <div class="row row-centered">

        <div class="col-md-7 col-xs-12">
            @if ($user->subscribed())
                {{ HTML::content('subscribed') }}
            @else
                {{ HTML::content('subscribe') }}
            @endif
        </div>
        <div class="col-sm-6 col-xs-11">
            @if ($user->subscribed() && $user->subscription()->onTrial())
                <h3 title="{{-- $user->subscription()->trial_ends_at->format('D M  j \a\t g:ia ') --}}">
                    Trial that ends {{ $user->subscription()->trial_ends_at->diffForHumans() }}
                </h3>
            @endif
        </div>
        <div class="col-sm-6 col-xs-11">
            <div class="row">
                @include('members.subscribe_invoices', ['invoices'=>$invoices])
            </div>

            @if ($user->subscribed())
                <div class="row">
                    <div class="col-xs-6 form-group">
                        {{ MForm::label("Current Plan:") }} {{ $user->getStripePlanInfo()['name'] }}
                    </div>
                    <div class="col-xs-6 form-group">
                        <label>
                            {{ Form::checkbox("subscribe[update_plan]", 1, null, ['class'=>'update-plan']) }}
                            Change plan
                        </label>
                    </div>
                </div>
            @endif

            <div class="row plan-data" style="display:{{ ($user->subscribed()) ? "none" : "block" }}">
                <div class="col-sm-12 form-group">
                    @if ($user->subscribed())
                        {{ MForm::select('subscribe.swap_plan', $plans) }}
                    @else
                        {{ MForm::select('subscribe.plan', $plans) }}
                    @endif
                </div>
            </div>

            @if ($user->subscribed())
                <div class="row">
                    <div class="col-xs-6 form-group">
                        {{ MForm::label("Last 4 of card:") }} {{ $user->card_last_four }}
                    </div>
                    <div class="col-xs-6 form-group">
                        <label>
                            {{ Form::checkbox("subscribe[update_card]", 1, null, ['class'=>'update-card']) }}
                            Update credit card info
                        </label>
                    </div>
                </div>
            @endif

            <div class="stripe-errors">
            </div>
            <input type="hidden" name="err_msg"><!-- place holder for error msgs not attached to other fields -->

            <div class="stripe-data" style="display:{{ ($user->subscribed()) ? "none" : "block" }}">
                <div class="row">
                    <div class="col-sm-12 form-group">
                        {{ MForm::text('Name on Card', $name, ['name'=>'', 'data-stripe'=>'name', 'class'=>'required']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-8 form-group">
                        {{ MForm::text('Credit Card', null, ['name'=>'', 'data-stripe'=>'number', 'maxlength'=>20, 'class'=>'required']) }}
                    </div>
                    <div class="col-xs-4 form-group">
                        {{ MForm::text('CVC', null, ['name'=>'', 'data-stripe'=>'cvc', 'maxlength'=>4, 'class'=>'required']) }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 form-group">
                        {{ MForm::selectMonth('Exp Month', null, ['name'=>'', 'data-stripe'=>'exp_month', 'placeholder'=>"Exp Month", 'class'=>'required']) }}
                    </div>
                    <div class="col-xs-6 form-group">
                        {{ MForm::selectRange('Exp Year', date('Y'), date('Y',strtotime('+7 years')), null, ['name'=>'', 'data-stripe'=>'exp_year', 'placeholder'=>"Exp Year", 'class'=>'required']) }}
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
                <div class="col-md-12 subscribe-button" style="display:{{ ($user->subscribed()) ? "none" : "block" }}">
                    {{ MForm::submit(($user->subscribed()) ? "Update Subscription" : "Subscribe", array('class'=>'btn-primary form-control')) }}
                </div>
            </div>

        </div>
    </div>
    {{ MForm::close() }}

    @if ($user->subscribed())
        {{ MForm::open( ['id'=>'form-cancel-subscribe', 'method'=>'DELETE', 'action' => 'Controller\Api\SubscribeApi@deleteIndex', 'bootstrap'=>true] ) }}
            <div class="col-md-3 pull-right cancel-button" style="margin-top:200px;">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancel-modal">
                    Cancel Subscription
                </button>
                {{ MForm::submit("Cancel Subscription", ['class'=>'btn-danger hide', 'name'=>'subscribe[cancel]']) }}
            </div>
            <input type="hidden" name="err_msg"><!-- place holder for error msgs not attached to other fields -->
        {{ MForm::close() }}
    @endif

    <script class="stripe-error-template" type="text/template">
        <div class="alert alert-danger" role="alert">
            {0}
        </div>
    </script>

    <!-- Modal -->
    <div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="cancelModalLabel">Are you sure you want to cancel?</h4>
                </div>
                <div class="modal-body">
                    If you cancel your subscription, you will still have access till the end of the billing period which you've paid for.
                    You may resubscribe at any time.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger cancel-subscription-modal-button" data-dismiss="modal">Cancel Subscription</button>
                </div>
            </div>
        </div>
    </div>
@stop
