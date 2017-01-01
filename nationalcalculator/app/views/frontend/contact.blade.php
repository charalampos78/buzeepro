@section('content')

    {{ MForm::open( ['id'=>'form-contact', 'action' => 'Controller\Api\ContactApi@postIndex', 'bootstrap'=>true] ) }}
    <div class="row row-centered">

        <div class="col-md-7 col-xs-12">
            {{ HTML::content('contact') }}
        </div>
        <div class="col-md-6 col-xs-11">
            <div class="row">
                <div class="col-sm-6 form-group">
                    {{ MForm::text('contact.name') }}
                </div>
                <div class="col-sm-6 form-group">
                    {{ MForm::text('contact.email') }}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 form-group">
                    {{ MForm::text('contact.subject') }}
                </div>
                <div class="col-sm-12 form-group">
                    {{ MForm::textarea('contact.message') }}
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                    {{ MForm::submit('Send us a message', array('class'=>'btn-primary form-control')) }}
                </div>
            </div>

        </div>
    </div>
    {{ MForm::close() }}

    <div id="contact-success" class="row row-centered" style="display: none;">
        <div class="col-md-7 col-xs-12">
            <h1>Thanks!</h1>
            <p>Your message has been sent.  We appreciate your feedback and will get back to you asap if necessary.</p>
        </div>
    </div>

@stop
