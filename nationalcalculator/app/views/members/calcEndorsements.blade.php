{{ MForm::hidden("notebook.endorsement_error") }}

@foreach ($endorsements_selected as $e)
    @include('members.calcEndorsementsItem', ['endorsement_selected'=>true])
@endforeach

@foreach ($endorsements as $e)
    @include('members.calcEndorsementsItem', ['test'=>'test'])
@endforeach

@if (count($endorsements_more))
    <button type="button" class="view-endorsements btn btn-xs btn-primary view-endorsements">View More</button>
    <br/><br/>
    @foreach ($endorsements_more as $e)
        @include('members.calcEndorsementsItem')
    @endforeach
@endif