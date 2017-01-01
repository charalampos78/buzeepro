{{ MForm::hidden("notebook.misc_error") }}
@foreach ($miscs_selected as $m)

    <div class="row" >
        <div class="col-xs-1 form-group">
            {{ MForm::checkbox("notebook.miscs.[$m->id]", 1, true, ['label'=>false]) }}
        </div>
        <div class="col-sm-9 col-xs-8 form-group">
            {{ MForm::label("notebook.miscs.[$m->id]", $m->name) }}
        </div>
        <div class="col-sm-1 col-xs-2 form-group">
            ${{$m->price}}
        </div>
    </div>

@endforeach

@foreach ($miscs as $m)

    <div class="row" >
        <div class="col-xs-1 form-group">
            {{ MForm::checkbox("notebook.miscs.[$m->id]", 1, null, ['label'=>false]) }}
        </div>
        <div class="col-sm-9 col-xs-8 form-group">
            {{ MForm::label("notebook.miscs.[$m->id]", $m->name) }}
        </div>
        <div class="col-sm-1 col-xs-2 form-group">
            ${{$m->price}}
        </div>
    </div>

@endforeach
