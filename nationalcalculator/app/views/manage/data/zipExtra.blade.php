<hr />
<h3>
    Related Zips <small>(bold: zip exists in multiple counties)</small>
</h3>
<div class="row" id="related-zips">
    @foreach ($related_zips as $rz)
        @if ($zip && $rz->id == $zip->id) @continue @endif
        <div class="col-xs-4 form-group">
            @if ($rz->multi_county) <strong> @endif
            <a href="{{ URL::action("Controller\\Manage\\DataController@getZipEdit", $rz->id) }}">
                {{$rz->primary_county}} -
                {{ $rz->zip }} :
                {{ $rz->state->abbr }} :
                {{ $rz->county->name }}
            </a>
            @if ($rz->multi_county) </strong> @endif
        </div>
    @endforeach
</div>

<hr />
<h3>
    Similar Zips <small>(bold: most of zip in county)</small>
</h3>
<div class="row">
    @foreach ($similar_zips as $sz)
        @if ($zip && $sz->id == $zip->id) @continue @endif
        <div class="col-xs-4 form-group">
            @if ($sz->primary_county) <strong> @endif
            <a href="{{ URL::action("Controller\\Manage\\DataController@getZipEdit", $sz->id) }}">
                {{ $sz->zip }} :
                {{ $sz->state->abbr }} :
                {{ $sz->county->name }}
            </a>
            @if ($sz->primary_county) </strong> @endif
        </div>
    @endforeach
</div>