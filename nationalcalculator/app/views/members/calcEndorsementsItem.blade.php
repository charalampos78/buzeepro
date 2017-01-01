
    <div class="row {{ $e->standard_flag ? "endorse-std" : "endorse-non-std".(isset($endorsement_selected)?"-selected endorse-std":"") }}" >
        <div class="col-xs-1 form-group">
            {{ MForm::checkbox("notebook.endorsements.[$e->id]", 1, isset($endorsement_selected)?$endorsement_selected:null, ['label'=>false]) }}
        </div>
        <div class="col-sm-9 col-xs-8 form-group">
            {{ MForm::label("notebook.endorsements.[$e->id]", $e->name) }}
        </div>
        <div class="col-sm-1 col-xs-2 form-group">
            @if ($e->type == 'fixed')
                ${{$e->amount}}
            @elseif ($e->type == 'percent')
                {{$e->amount}}%
            @endif
        </div>
    </div>
