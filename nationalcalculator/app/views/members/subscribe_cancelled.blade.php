@section('inlineJS')
    <script>
        $(function() {
            micro.controllers.member.subscribe_stripe = true;
        });
    </script>
@show
@section('content')

    {{ MForm::open( ['id'=>'form-subscribe', 'method'=>'PUT', 'action' => 'Controller\Api\SubscribeApi@putIndex', 'bootstrap'=>true] ) }}
    <div class="row row-centered">

        <div class="col-md-7 col-xs-12">
            {{ HTML::content('subscribe_cancelled') }}
        </div>
        <div class="col-sm-6 col-xs-11">

            <div class="col-sm-12 form-group">
                <div class="alert alert-warning" role="alert">
                    Your plan has been cancelled and will expire on {{ $user->subscription()->ends_at->format('D d M Y')  }}
                </div>
            </div>
            <div class="col-md-12">
                {{ MForm::submit("Resume Subscription", array('class'=>'btn-success form-control', 'name'=>'subscribe[resume]')) }}
            </div>

            <input type="hidden" name="err_msg"><!-- place holder for error msgs not attached to other fields -->

        </div>
        <div class="col-xs-12 col-sm-6" style="margin-top:30px;">
            <div class="row">
                @include('members.subscribe_invoices', ['invoices'=>$invoices])
            </div>
        </div>

    </div>
    {{ MForm::close() }}

@stop
