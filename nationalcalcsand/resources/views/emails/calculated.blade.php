@extends('emails.layout')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            {{ HTML::content('calculated-form') }}
        </div>
        <div class="col-xs-12">
            <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Location</h3>
            <div id="location">
                <div class="row">
                    <div class="col-sm-6">
                        {{ $notebook->zip->city }}, {{ $notebook->county->name }}
                        , {{ $notebook->zip->state->abbr }}
                    </div>
                    <div class="col-sm-6">
                        {{ $notebook->county->note }}
                    </div>
                </div>
            </div>
            <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Policy Info</h3>
            <div id="loan">
                <div class="row">
                    <div class="col-sm-4 ">
                        <label style="font-weight:bold;">Type:</label> {{ ucfirst($notebook->type)  }}
                    </div>
                    @if (!empty($notebook->purchase_price))
                        <div class="col-sm-4 " id="purchase-price">
                            <label style="font-weight:bold;">Purchase Price:</label>
                            <span>${{ number_format($notebook->purchase_price,2) }}</span>
                        </div>
                    @endif
                    @if (!empty($notebook->loan_amount))
                        <div class="col-sm-4 " id="loan-amount">
                            <label style="font-weight:bold;">Loan Amount:</label>
                            <span>${{ number_format($notebook->loan_amount,2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="row" style="clear:both">
                    @if (($ownerCost = $notebook->getCalculator()->ownerCost()) && !empty($ownerCost))
                        <div class="col-sm-9 col-xs-6 ">
                            <label style="font-weight:bold;">Owner Policy:</label>
                        </div>
                        <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                            <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                            <span>${{ number_format($ownerCost, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="row">
                    @if (($lenderCost = $notebook->getCalculator()->lenderCost()) && !empty($lenderCost))
                        <div class="col-sm-9 col-xs-6 ">
                            <label style="font-weight:bold;">Lender Policy:</label>
                        </div>
                        <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                            <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                            <span>${{ number_format($lenderCost, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @if ( Auth::user()->hasRole('admin') || ($user->onPlan(Models\User::PLANS[2]['code'])) )
                <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Documents</h3>
                <div id="documents">
                    {{--@include('members.calcDocs')--}}
                    @foreach ($notebook->documents as $doc)
                        <div class="document">
                            @if ($doc->pivot->pages == 0) @continue @endif
                            <div class="row">
                                <div class="col-sm-7" style="float:left; width:60%;">
                                    <h4 style="color: #696969; margin:0 0 10px 0; font-size:18px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">{{ $doc->name }}</h4>
                                </div>

                                <div class="col-sm-2 col-xs-6" style="margin-left:60%;">
                                    <label style="font-weight:bold;">Pages:</label> {{ $doc->pivot->pages }}

                                    <small class="{{ !Auth::user()->hasRole('admin')?"hide":"" }}">
                                        <br/>
                                        <label style="font-weight:bold;">{{ $doc->price_text }}:</label> ${{ $doc->price_first }}
                                        @if ($doc->price_additional && $doc->price_additional != 0 && $doc->price_first != $doc->price_additional )
                                            <br/> <label style="font-weight:bold;">add'l:</label> ${{ $doc->price_additional }}
                                        @endif
                                    </small>

                                </div>

                                <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                                    <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                                    <span>${{ number_format($notebook->getCalculator()->docPageCost($doc), 2) }}</span>
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
                                        <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                                            <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                                            <span>${{ number_format($notebook->getCalculator()->docTaxCost($doc, $tax), 2) }}</span>
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
                                                {{ number_format($tax->percent*100, 3) }}%
                                                on {{ $tax->type }}
                                            </small>
                                        </div>
                                        <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                                            <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                                            <span>${{ number_format($notebook->getCalculator()->docTaxCost($doc, $tax), 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach

                            @endif


                            <hr/>
                        </div>
                    @endforeach
                    <div class="row">
                        <div class="col-sm-5 col-sm-push-7 cost-col" style="text-align:right;">
                            <label style="font-weight:bold; float: left; margin-left:30%;">Document and Tax Total:</label>
                            <br/>
                            <span>${{ number_format($notebook->getCalculator()->docTaxCost() + $notebook->getCalculator()->docPageCost(), 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if ($notebook->endorsements->count())
                <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Endorsements</h3>
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
                            <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                                <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                                <span>${{ number_format($notebook->getCalculator()->endorsementCost($e), 2) }}</span>
                            </div>
                        </div>
                        <hr/>
                    @endforeach
                    <div class="row">
                        <div class="col-sm-5 col-sm-push-7 cost-col" style="text-align:right;">
                            <label style="font-weight:bold; float: left; margin-left:30%;">Endorsement Total:</label>
                            <span>${{ number_format($notebook->getCalculator()->endorsementCost(), 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if ($notebook->miscs->count())
                <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Estimated Additional Fees</h3>
                <div id="misc">
                    @foreach ($notebook->miscs as $m)
                        <div class="row">
                            <div class="col-sm-9 col-xs-6">
                                {{ $m->name  }}
                            </div>
                            <div class="col-sm-3 col-xs-6 cost-col" style="text-align:right;">
                                <label style="font-weight:bold; float: left; margin-left:60%;">Cost:</label>
                                <span>${{ number_format($notebook->getCalculator()->miscCost($m), 2) }}</span>
                            </div>
                        </div>
                        <hr/>
                    @endforeach
                    <div class="row">
                        <div class="col-sm-5 col-sm-push-7 cost-col" style="text-align:right;">
                            <label style="font-weight:bold; float: left; margin-left:30%;">Additional Total:</label>
                            <span>${{ number_format($notebook->getCalculator()->miscCost(), 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <div class="row">
                    <div class="col-sm-7 col-sm-push-5 cost-col">
                        <label style="font-weight:bold;">
                            <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">
                                <span>Total Estimated Cost</span>
                                <span style="float:right;"> ${{ number_format($notebook->getCalculator()->totalCost(), 2) }} </span>
                            </h3>
                        </label>
                    </div>
                </div>
            </div>

            <h3 style="color: #858500; margin:20px 0 10px 0; font-size:24px; font-weight:normal; font-family: Georgia, 'Times New Roman', Times, Serif;">Tax Collector Info</h3>
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
                                                <a style="color:#8c8c73" href='mailto:{{{$tc->email}}}'>{{$tc->email}}</a> <br/>
                                            @elseif ($tc->email)
                                                <a style="color:#8c8c73" href="{{{$tc->email}}}">Contact</a> <br/>
                                            @endif
                                            {{ $tc->website ? " <a style='color:#8c8c73' href='{$tc->website}'>Website</a> " : "" }}
                                            {{ $tc->paysite ? " <a style='color:#8c8c73' href='{$tc->paysite}'>Payment Site</a> " : "" }}
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
                                                <b>Location:</b> <br/>
                                                {{ $tc->address ? $tc->address . "<br />" : "" }}
                                                {{ $tc->city ? $tc->city . ", " : "" }}
                                                {{ $tc->state ? $tc->state . ", " : "" }}
                                                {{ $tc->zip ? $tc->zip . "" : "" }}
                                            </div>
                                        @endif
                                        @if ($tc->m_address)
                                            <div class="col-sm-6 form-group-sm">
                                                <b>Mailing Address:</b> <br/>
                                                {{ $tc->m_address ? $tc->m_address . "<br />" : "" }}
                                                {{ $tc->m_city ? $tc->m_city . ", " : "" }}
                                                {{ $tc->m_state ? $tc->m_state . ", " : "" }}
                                                {{ $tc->m_zip ? $tc->m_zip . "" : "" }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr/>
                        @endforeach
                    @endif
                @else
                    <div class="row">
                        <div class="col-xs-11">
                            <div class="row">
                                <div class="col-xs-12">
                                    Upgrade your subscription to {{ Models\User::PLANS[2]['name'] }} to view
                                    all tax collector information available for this county.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
