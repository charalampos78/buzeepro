@extends('emails.layout')

@section('content')
    <div class="row">
        <h3>Welcome to National Calculator</h3>
        <p>Hi {{ $user->profile->first_name ." ". $user->profile->last_name }},</p>
        <div>
            {{ HTML::content('email-signup') }}
        </div>
    </div>
@stop
