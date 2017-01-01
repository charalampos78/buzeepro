@if ($invoices->count())
    <div class="col-sm-12 form-group">
        <table class="table table-condensed">
            <h4>Invoices</h4>
            <thead>
            <tr>
                <th>Plan</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td><a href="{{ URL::route("memberSubscribeInvoice", ["invoice_id"=>$invoice->id]) }}" target="_blank">{{ $user->getStripePlanInfo($invoice->subscriptions()[0]->plan->id)['name'] }}</a></td>
                    <td>{{ $invoice->date() }}</td>
                    <td>{{ $invoice->total() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
