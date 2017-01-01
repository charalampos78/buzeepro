<?php /** @var Models\Notebook $notebook */ ?>
@section('inlineCSS')
    hr {
        margin: 0 8% 20px 8%;
        border-color: #CCC;
    }

    .cost-col span {
        display: inline-block;
        min-width: 80px;
        text-align: right;
        float: right;
    }

    .tax-collector a {
        padding-right:30px;
    }

@append

@section('content')

    <div class="row">
        <div class="col-xs-12">
            {{ HTML::link("members/calculator/$notebook->id", "Edit Info", ["class"=>"btn btn-info right hidden-print"]) }}
            {{ HTML::content('calculated-form') }}
        </div>
        @if ($notebook->name)
        <div class="col-xs-12">
            <h3>Name <small>{{ $notebook->name }}</small></h3>
        </div>
        @endif
        <div class="col-xs-12">
            <h3>Location</h3>
            <div id="location">
                <div class="row">
                    <div class="col-sm-6">
                        {{ $notebook->zip->city }}, {{ $notebook->county->name }}, {{ $notebook->zip->state->abbr }}
                    </div>
                    <div class="col-sm-6">
                        {{ $notebook->county->note }}
                    </div>
                </div>
            </div>
            <h3>Policy Info</h3>
            <div id="loan">
                <div class="row">
                    <div class="col-sm-4 ">
                        <label>Type:</label> {{ ucfirst($notebook->type)  }}
                    </div>
                    @if (!empty($notebook->purchase_price))
                    <div class="col-sm-4 " id="purchase-price">
                        <label>Purchase Price:</label> <span>${{ number_format($notebook->purchase_price,2) }}</span>
                    </div>
                    @endif
                    @if (!empty($notebook->loan_amount))
                    <div class="col-sm-4 " id="loan-amount">
                        <label>Loan Amount:</label> <span>${{ number_format($notebook->loan_amount,2) }}</span>
                    </div>
                    @endif
                </div>
                <div class="row">
                @if (($ownerCost = $notebook->getCalculator()->ownerCost()) && !empty($ownerCost))
                    <div class="col-sm-9 col-xs-6 ">
                        <label>Owner Policy:</label>
                    </div>
                    <div class="col-sm-3 col-xs-6 cost-col">
                        <label>Cost:</label> <span>${{ number_format($ownerCost, 2) }}</span>
                    </div>
                @endif
                </div>
                <div class="row">
                @if (($lenderCost = $notebook->getCalculator()->lenderCost()) && !empty($lenderCost))
                    <div class="col-sm-9 col-xs-6 ">
                        <label>Lender Policy:</label>
                    </div>
                    <div class="col-sm-3 col-xs-6 cost-col">
                        <label>Cost:</label> <span>${{ number_format($lenderCost, 2) }}</span>
                    </div>
                @endif
                </div>
            </div>
            @if ( Auth::user()->hasRole('admin') || ($user->onPlan(Models\User::PLANS[2]['code'])) )
            <h3>Documents</h3>
            <div id="documents">
                {{--@include('members.calcDocs')--}}
                @foreach ($notebook->documents as $doc)
                <div class="document">
                    @if ($doc->pivot->pages == 0) @continue @endif
                    <div class="row">
                        <div class="col-sm-7">
                            <h4>{{ $doc->name }}</h4>
                        </div>

                        <div class="col-sm-2 col-xs-6">
                            <label>Pages:</label> {{ $doc->pivot->pages }}

                            <small class="{{ !Auth::user()->hasRole('admin')?"hide":"" }}">
                                <br/>
                                <label>{{ $doc->price_text }}:</label> ${{ $doc->price_first }}
                                @if ($doc->price_additional && $doc->price_additional != 0 && $doc->price_first != $doc->price_additional )
                                    <br /> <label>add'l:</label> ${{ $doc->price_additional }}
                                @endif
                            </small>

                        </div>

                        <div class="col-sm-3 col-xs-6 cost-col">
                            <label>Cost:</label> <span>${{ number_format($notebook->getCalculator()->docPageCost($doc), 2) }}</span>
                        </div>

                    </div>
                    @if ($doc->taxes->count())

                    @foreach ($doc->taxes as $tax)
                        @if ($tax->type != "fixed") @continue @endif
                        <div class="row">
                            <div class="col-sm-9 col-xs-6">
                                <b>{{ $tax->name }}</b>
                                <small class="{{ !Auth::user()->hasRole('admin')?"hide":"" }}">
                                    <br>
                                ${{ number_format($tax->percent, 2) }}
                                </small>
                            </div>
                            <div class="col-sm-3 col-xs-6 cost-col">
                                <label>Cost:</label> <span>${{ number_format($notebook->getCalculator()->docTaxCost($doc, $tax), 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    @foreach ($doc->taxes as $tax)
                        @if ($tax->type == "fixed") @continue @endif
                        <div class="row">
                            <div class="col-sm-9 col-xs-6">
                                <b>{{ $tax->name }}</b>
                                <small class="{{ !Auth::user()->hasRole('admin')?"hide":"" }}">
                                    <br>
                                {{ number_format($tax->percent*100, 3) }}% on {{ $tax->type }}
                                </small>
                            </div>
                            <div class="col-sm-3 col-xs-6 cost-col">
                                <label>Cost:</label> <span>${{ number_format($notebook->getCalculator()->docTaxCost($doc, $tax), 2) }}</span>
                            </div>
                        </div>
                    @endforeach

                    @endif


                    <hr />
                </div>
                @endforeach
                <div class="row">
                    <div class="col-sm-5 col-sm-push-7 cost-col">
                        <label>Document and Tax Total:</label> <span>${{ number_format($notebook->getCalculator()->docTaxCost() + $notebook->getCalculator()->docPageCost(), 2) }}</span>
                    </div>
                </div>
            </div>
            @endif
            @if ($notebook->endorsements->count())
            <h3>Endorsements</h3>
            <div id="endorsements">
                @foreach ($notebook->endorsements as $e)
                    <div class="row">
                        <div class="col-sm-9 col-xs-6">
                            {{ $e->name  }}
                            <small class="{{ !Auth::user()->hasRole('admin')?"hide":"" }}">
                                <br/>
                                @if ($e->type == 'fixed')
                                    ${{$e->amount}}
                                @elseif ($e->type == 'percent')
                                    {{$e->amount}}%
                                @endif
                            </small>
                        </div>
                        <div class="col-sm-3 col-xs-6 cost-col">
                            <label>Cost:</label> <span>${{ number_format($notebook->getCalculator()->endorsementCost($e), 2) }}</span>
                        </div>
                    </div>
                    <hr />
                @endforeach
                <div class="row">
                    <div class="col-sm-5 col-sm-push-7 cost-col">
                        <label>Endorsement Total:</label> <span>${{ number_format($notebook->getCalculator()->endorsementCost(), 2) }}</span>
                    </div>
                </div>
            </div>
            @endif
            @if ($notebook->miscs->count())
            <h3>Estimated Additional Fees</h3>
            <div id="misc">
                @foreach ($notebook->miscs as $m)
                    <div class="row">
                        <div class="col-sm-9 col-xs-6">
                            {{ $m->name  }}
                        </div>
                        <div class="col-sm-3 col-xs-6 cost-col">
                            <label>Cost:</label> <span>${{ number_format($notebook->getCalculator()->miscCost($m), 2) }}</span>
                        </div>
                    </div>
                    <hr />
                @endforeach
                <div class="row">
                    <div class="col-sm-5 col-sm-push-7 cost-col">
                        <label>Additional Total:</label> <span>${{ number_format($notebook->getCalculator()->miscCost(), 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <div class="row">
                    <div class="col-sm-7 col-sm-push-5 cost-col" style="font-weight: bold;">
                        <label><h3>Total Estimated Cost</h3></label> <span><h3>${{ number_format($notebook->getCalculator()->totalCost(), 2) }}</h3></span>
                    </div>
                </div>
            </div>

            <h3 class="hidden-print">Export Options</h3>
            <div class="hidden-print">
                {{ MForm::open(['id'=>'form-export', 'action' => ['Controller\Api\ExportApi@postIndex', $notebook->id], 'bootstrap'=>true] ) }}
                <div class="row">
                    <div class="col-sm-7 ">
                        {{ MForm::radioGroup('export.output', ['HUD'=>'HUD Settlement', 'GFE'=>'GFE Form', 'print'=>'Print','email'=>'Email'], null, ['class'=>'export-output', 'key-as-val'=>true]) }}
                        {{ MForm::hidden('export.notebook_id', $notebook->id, ['id'=>'export-notebook_id']) }}
                        <br />
                        <span id="export[output]-error" class="error has-error"></span>
                        <label id="export[output]-error" class="error has-error" for="export[output]"></label>
                        <small>Updated forms coming soon!</small>
                    </div>
                    <div class="col-sm-5" id="export-email-box" style="display:none;">
                        {{ MForm::text('export.email', $user->email, ['id'=>'export-email']) }}
                    </div>
                    <div class="col-sm-12">
                        <button class="btn btn-primary export-button" disabled>Export</button>
                    </div>
                </div>
                {{ MForm::close() }}
            </div>

            <h3>Tax Collector Info</h3>
            <div id="tax-collector">
                @if ( Auth::user()->hasRole('admin') || ($user->onPlan(Models\User::PLANS[2]['code'])) )
                    @if (!$notebook->county->taxCollectors->count())
                        <div class="row">
                            <div class="col-xs-11">
                                <div class="row">
                                    <div class="col-xs-12">
                                        No tax collector information available for selected county.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($notebook->county->taxCollectors as $tc)
                            <div class="row row-centered tax-collector">
                                <div class="col-xs-11">
                                    <div class="row">
                                        <div class="col-sm-6 form-group-sm {{$tc->municipality?"":"hide"}}">
                                            <b>
                                            {{ $tc->municipality ? "Municipality: " . $tc->municipality . "<br/>" : "" }}
                                            </b>
                                        </div>
                                        <div class="col-sm-6 form-group-sm">
                                            <b>
                                            {{ $tc->commissioner ? "Commissioner: " . $tc->commissioner . "<br />" : "" }}
                                            </b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group-sm">
                                            @if ($tc->email && strpos($tc->email,'@') !== false)
                                                <a href='mailto:{{{$tc->email}}}'>{{$tc->email}}</a> <br />
                                            @elseif ($tc->email)
                                                <a href="{{{$tc->email}}}">Contact</a> <br />
                                            @endif
                                            {{ $tc->website ? " <a href='{$tc->website}'>Website</a> " : "" }}
                                            {{ $tc->paysite ? " <a href='{$tc->paysite}'>Payment Site</a> " : "" }}
                                        </div>
                                        <div class="col-sm-6 form-group-sm">
                                            {{ $tc->phone ? "Phone: " . $tc->phone . "<br />" : "" }}
                                            {{ $tc->phone2 ? "Phone2: " . $tc->phone2 . "<br />" : "" }}
                                            {{ $tc->fax ? "Fax: " . $tc->fax . "<br />" : "" }}
                                            {{ $tc->fax2 ? "Fax2: " . $tc->fax2 . "<br />" : "" }}
                                        </div>
                                    </div>
                                    <div class="row tax-address">
                                        @if ($tc->address)
                                        <div class="col-sm-6 form-group-sm">
                                            <b>Location:</b> <br />
                                            {{ $tc->address ? $tc->address . "<br />" : "" }}
                                            {{ $tc->city ? $tc->city . ", " : "" }}
                                            {{ $tc->state ? $tc->state . ", " : "" }}
                                            {{ $tc->zip ? $tc->zip . "" : "" }}
                                        </div>
                                        @endif
                                        @if ($tc->m_address)
                                        <div class="col-sm-6 form-group-sm">
                                            <b>Mailing Address:</b> <br />
                                            {{ $tc->m_address ? $tc->m_address . "<br />" : "" }}
                                            {{ $tc->m_city ? $tc->m_city . ", " : "" }}
                                            {{ $tc->m_state ? $tc->m_state . ", " : "" }}
                                            {{ $tc->m_zip ? $tc->m_zip . "" : "" }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr />
                        @endforeach
                    @endif
                @else
                    <div class="row">
                        <div class="col-xs-11">
                            <div class="row">
                                <div class="col-xs-12">
                                    Upgrade your subscription to {{ Models\User::PLANS[2]['name'] }} to view all tax collector information available for this county.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <div class="modal fade" id="wait-modal" tabindex="-1" role="dialog" aria-labelledby="waitModalLabel" aria-hidden="true">
        <div class="vertical-alignment-helper">
            <div class="modal-dialog modal-sm vertical-align-center">
                <div class="modal-content" style="text-align: center;">
                    <div class="modal-body">
                        Please wait while we generate your <span class="export-output"></span> export.
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
