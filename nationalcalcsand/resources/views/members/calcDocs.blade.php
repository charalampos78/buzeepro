{{ MForm::hidden("notebook.document_error") }}

@foreach ($documents as $doc)

    <div class="row">
        <div class="col-sm-6 form-group">
            <h4>{{ $doc->name }}</h4>
            <label>{{ $doc->price_text }}:</label> ${{ $doc->price_first }}
            @if ($doc->price_additional && $doc->price_additional != 0)
                <br /> <label>Additional:</label> ${{ $doc->price_additional }}
            @endif
            @if (($taxes = $doc->taxes) && count($taxes))
                <br />
                @foreach ($doc->taxes as $tax)
                    @if ($tax->type != "fixed") @continue @endif
                    <br />
                    <b>{{ $tax->name }}</b>
                    <br />
                    ${{ number_format($tax->percent, 2) }}
                @endforeach
                <br />
                @foreach ($doc->taxes as $tax)
                    @if ($tax->type == "fixed") @continue @endif
                    <br />
                    <b>{{ $tax->name }}</b>
                    <br />
                    {{ number_format($tax->percent * 100, 3) }}% on {{ $tax->type }}
                @endforeach
            @endif

        </div>
        <div class="col-sm-6 form-group">
            {{ MForm::toggleBootstrap(true) }}
            {{ MForm::text("notebook.documents.[$doc->id].pages", ($doc->pivot)?$doc->pivot->pages:null, ['labelName'=>"Page count: ", 'placeholder'=>"Number of Pages"]) }}
        </div>
    </div>

@endforeach