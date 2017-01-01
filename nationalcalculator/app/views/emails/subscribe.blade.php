@extends('emails.layout')

@section('content')
    <h2>Thanks For Subscribing {{ $user->profile->first_name }}!</h2>

    <b>Plan:</b> {{ $plan['name'] }}
    <b>Rate:</b> ${{ number_format($plan['cost'], 2) }} / month
@stop

